<?php

namespace App\Controller;

use App\Entity\BankAccount;
use App\Repository\BankAccountRepository;
use App\Repository\UserRepository;
use App\Services\ExchangeService;
use App\Services\LogService;
use App\Services\UserService;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\Serializer;
use App\Entity\User;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Exception;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class AuthController extends AbstractFOSRestController
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;

    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var ArrayTransformerInterface
     */
    private $arrayTransformer;
    /**
     * @var ValidatorInterface
     */
    private $validator;
    /**
     * @var Serializer
     */
    private $serializer;
    /**
     * @var ParameterBagInterface
     */
    private $parameterBag;
    /**
     * @var BankAccountRepository
     */
    private $bankAccountRepository;
    /**
     * @var ExchangeService
     */
    private $exchangeService;
    /**
     * @var LogService
     */
    private $logService;
    /**
     * @var UserService
     */
    private $userService;

    /**
     * AuthController constructor.
     * @param UserPasswordEncoderInterface $encoder
     * @param UserRepository $userRepository
     * @param ValidatorInterface $validator
     * @param SerializerInterface $serializer
     * @param ParameterBagInterface $parameterBag
     * @param BankAccountRepository $bankAccountRepository
     * @param ExchangeService $exchangeService
     * @param LogService $logService
     * @param UserService $userService
     */
    public function __construct(UserPasswordEncoderInterface $encoder, UserRepository $userRepository,
                                ValidatorInterface $validator, SerializerInterface $serializer,
                                ParameterBagInterface $parameterBag, BankAccountRepository $bankAccountRepository,
                                ExchangeService $exchangeService, LogService $logService, UserService $userService)
    {
        $this->encoder = $encoder;
        $this->userRepository = $userRepository;
        $this->validator = $validator;
        $this->serializer = $serializer;
        $this->parameterBag = $parameterBag;
        $this->bankAccountRepository = $bankAccountRepository;
        $this->exchangeService = $exchangeService;
        $this->logService = $logService;
        $this->userService = $userService;
    }

    /**
     * @Route("/auth/login", name="user_login", methods={"POST"})
     */
    public function userLoginAction()
    {
        $this->logService->log('login',
                    'User ' . $this->userService->getCurrentUser()->getEmail() . ' Logged in the system');
    }

    /**
     * @Route("/auth/register", name="user_register", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     * @throws InvalidArgumentException
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function registerAction(Request $request)
    {
        $inputData = $request->getContent();
        $user = $this->serializer->deserialize($inputData, User::class, 'json');
        $validationErrors = $this->validator->validate($user);
        if($validationErrors->count() > 0) {
            return $this->json(
                $validationErrors,
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        $encodedPassword = $this->encoder->encodePassword($user, $user->getPassword());
        $user->setPassword($encodedPassword);

        try {
            $this->userRepository->insert($user, true);
        } catch (Exception $e) {
            $this->logService->log('register',
                 ' Failed to register');
            return $this->json(
              $e->getMessage(),
              Response::HTTP_PRECONDITION_FAILED
            );
        }

        #Also create bank account for the new user
        $currency = $request->get('currency', '');
        $deposit = $request->get('deposit', 0);
        $bankAccount = new BankAccount();
        $bankAccount->setUserId($user);
        $bankAccount->setBalance(floatval($deposit));
        if (!empty($currency) && $this->exchangeService->isCurrencyValid($currency)) {
            $bankAccount->setDefaultCurrency($currency);
        } else {
            $bankAccount->setDefaultCurrency($this->parameterBag->get('defaultCurrency'));
        }

        $this->bankAccountRepository->insert($bankAccount, true);

        $this->logService->log('register',
            'User ' . $user->getEmail() . ' register successful');
        return $this->json(['result' => $user->getName()], Response::HTTP_CREATED);
    }
}

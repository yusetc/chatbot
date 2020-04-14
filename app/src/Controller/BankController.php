<?php

namespace App\Controller;

use App\Entity\BankAccount;
use App\Entity\User;
use App\Repository\BankAccountRepository;
use App\Services\ExchangeService;
use App\Services\LogService;
use App\Services\UserService;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

/**
 * Class BankController
 * @package App\Controller
 * @Route("/bank")
 */
class BankController extends AbstractController
{
    /**
     * @var BankAccountRepository
     */
    private $bankAccountRepository;
    /**
     * @var ExchangeService
     */
    private $exchangeService;
    /**
     * @var User|null
     */
    private $user;
    /**
     * @var BankAccount|null
     */
    private $bankAccount;
    /**
     * @var LogService
     */
    private $logService;

    /**
     * BankController constructor.
     * @param BankAccountRepository $bankAccountRepository
     * @param ExchangeService $exchangeService
     * @param UserService $user
     * @param LogService $logService
     */
    public function __construct(BankAccountRepository $bankAccountRepository, ExchangeService $exchangeService, UserService $user, LogService $logService)
    {
        $this->bankAccountRepository = $bankAccountRepository;
        $this->exchangeService = $exchangeService;
        $this->user = $user->getCurrentUser();
        $this->bankAccount = $this->bankAccountRepository->findOneBy(['userId' => $this->user->getId()]);
        $this->logService = $logService;
    }


    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws InvalidArgumentException
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @Route("/deposit", name="bank_deposit", methods={"POST"})
     */
    public function depositAction(Request $request) : JsonResponse
    {
        $currency = $request->get('currency', '');
        $amount = $request->get('amount', 0);
        if (floatval($amount) > 0)
        {
            if (!empty($currency)) {
                if ($this->exchangeService->isCurrencyValid($currency)) {
                    $amount = $this->exchangeService->convert($currency, $this->bankAccount->getDefaultCurrency(), $amount);
                } else {
                    return $this->json(['error' => 'Invalid currency'], Response::HTTP_CONFLICT);
                }
            }

            $this->bankAccount->setBalance($this->bankAccount->getBalance() + $amount);
            $this->bankAccountRepository->update($this->bankAccount, true);

            $this->logService->log('deposit',
                'User ' . $this->user->getEmail() . ' increased balance to ' . $this->bankAccount->getBalance());
            return $this->json(['balance' => $this->bankAccount->getBalance(), 'currency' => $this->bankAccount->getDefaultCurrency()], Response::HTTP_OK);
        }

        return $this->json(['error' => 'Invalid amount'], Response::HTTP_CONFLICT);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws InvalidArgumentException
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @Route("/withdraw", name="bank_withdraw", methods={"POST"})
     */
    public function withdrawAction(Request $request) : JsonResponse
    {
        $currency = $request->get('currency', '');
        $amount = $request->get('amount', 0);
        if ( floatval($amount) > 0 )
        {
            if (!empty($currency)) {
                if ($this->exchangeService->isCurrencyValid($currency)) {
                    $amount = $this->exchangeService->convert($currency, $this->bankAccount->getDefaultCurrency(), $amount);
                } else {
                    return $this->json(['error' => 'Invalid currency'], Response::HTTP_CONFLICT);
                }
            }

            if ($this->bankAccount->getBalance() >= $amount) {
                $this->bankAccount->setBalance($this->bankAccount->getBalance() - $amount);
                $this->bankAccountRepository->update($this->bankAccount, true);
                $this->logService->log('withdraw',
                    'User ' . $this->user->getEmail() . ' decreased balance to ' . $this->bankAccount->getBalance());
                return $this->json(['balance' => $this->bankAccount->getBalance(), 'currency' => $this->bankAccount->getDefaultCurrency()], Response::HTTP_OK);
            } else {
                return $this->json(['error' => 'Not enough money to perform this operation'], Response::HTTP_CONFLICT);
            }
        }

        return $this->json(['error' => 'Invalid amount'], Response::HTTP_CONFLICT);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws InvalidArgumentException
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @Route("/currency", name="bank_set_currency", methods={"POST"})
     */
    public function setCurrencyAction(Request $request) : JsonResponse
    {
        $currency = mb_strtoupper($request->get('currency'));
        if ( $currency !== $this->bankAccount->getDefaultCurrency() && $this->exchangeService->isCurrencyValid($currency))
        {
            $updatedBalance = $this->exchangeService->convert($this->bankAccount->getDefaultCurrency(), $currency,
                $this->bankAccount->getBalance());
            $this->bankAccount->setBalance($updatedBalance);
            $this->bankAccount->setDefaultCurrency($currency);
            $this->bankAccountRepository->update($this->bankAccount, true);
            $this->logService->log('currency',
                'User ' . $this->user->getEmail() . ' set currency to ' . $this->bankAccount->getDefaultCurrency());
            return $this->json(['result'=> $this->bankAccount->getDefaultCurrency()], Response::HTTP_OK);
        }

        return $this->json(['error'=> 'Invalid Currency'], Response::HTTP_CONFLICT);

    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @Route("/currency", name="bank_get_currency", methods={"GET"})
     */
    public function getCurrencyAction(Request $request)
    {
        return $this->json(['result'=> $this->bankAccount->getDefaultCurrency()], Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws InvalidArgumentException
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @Route("/exchange", name="bank_exchange", methods={"GET"})
     */
    public function exchangeAction(Request $request) : JsonResponse
    {
        $currencyFrom = $request->query->get('currencyFrom');
        $currencyTo = $request->query->get('currencyTo');
        $amount = $request->query->get('amount');

        if (floatval($amount) > 0 && $this->exchangeService->isCurrencyValid($currencyFrom) && $this->exchangeService->isCurrencyValid($currencyTo)) {
            return $this->json(['result' => $this->exchangeService->convert($currencyFrom, $currencyTo, $amount)],
                Response::HTTP_OK);
        }

        return $this->json(['error' => 'invalid conversion'], Response::HTTP_CONFLICT);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws InvalidArgumentException
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @Route("/balance", name="bank_balance", methods={"GET"})
     */
    public function balanceAction(Request $request) : JsonResponse
    {
        $currency = $request->query->get('currency', '');
        $this->logService->log('balance',
            'User ' . $this->user->getEmail() . ' ask for balance');
        if (empty($currency))
        {
            return $this->json(['balance' => $this->bankAccount->getBalance(), 'currency' => $this->bankAccount->getDefaultCurrency()], Response::HTTP_OK);
        }

        if ($this->exchangeService->isCurrencyValid($currency))
        {
            $updatedBalance = $this->exchangeService->convert($this->bankAccount->getDefaultCurrency(), $currency, $this->bankAccount->getBalance());
            return $this->json(['balance' => $updatedBalance, 'currency' => mb_strtoupper($currency)], Response::HTTP_OK);
        }

        return $this->json(['error' => 'invalid currency'], Response::HTTP_CONFLICT);
    }
}

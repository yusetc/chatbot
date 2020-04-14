<?php


namespace App\Services;

use Psr\Cache\InvalidArgumentException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Exception;

class ExchangeService
{
    /**
     * @var ParameterBag
     */
    private $parameterBag;
    /**
     * @var HttpClientInterface
     */
    private $httpClient;
    /**
     * @var FilesystemAdapter
     */
    private $cache;
    /**
     * @var LogService
     */
    private $logService;

    /**
     * ExchangeService constructor.
     * @param ParameterBagInterface $parameterBag
     * @param HttpClientInterface $httpClient
     * @param AdapterInterface $cache
     * @param LogService $logService
     */
    public function __construct(ParameterBagInterface $parameterBag, HttpClientInterface $httpClient, AdapterInterface $cache)
    {
        $this->parameterBag = $parameterBag;
        $this->httpClient = $httpClient;
        $this->cache = $cache;
    }

    /**
     * @param $currency
     * @return bool
     * @throws InvalidArgumentException
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function isCurrencyValid($currency) : bool
    {
        $currency = mb_strtoupper($currency);

        $requestHeaders = [
            'x-rapidapi-host' => 'fixer-fixer-currency-v1.p.rapidapi.com',
            'x-rapidapi-key' => 'fe8234dda2mshff811bf5d90c3f6p1cf7ffjsn3ca8a0e42da6'
        ];

        $requestOptions = [
            'headers' => $requestHeaders
        ];

        $cachedCurrency = $this->cache->getItem($currency);
        if (!$cachedCurrency->isHit()) {
            try {
                $apiSymbolsResponse = $this->httpClient->request('GET', $this->parameterBag->get('fixerIOSymbols'), $requestOptions);
                if (Response::HTTP_OK === $apiSymbolsResponse->getStatusCode()) {
                    $content = $apiSymbolsResponse->toArray();
                    if (in_array(mb_strtoupper($currency), array_keys($content['symbols']))) {
                        $cachedCurrency->set($currency);
                        $this->cache->save($cachedCurrency);
                        return true;
                    }

                    return false;
                }
            } catch (Exception $e) {
                throw new HttpException(
                    Response::HTTP_CONFLICT,
                    $e->getMessage()
                );
            }
        }
        return true;
    }

    /**
     * @param $currencyFrom
     * @param $currencyTo
     * @param $amount
     * @return float
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function convert($currencyFrom, $currencyTo, $amount) : float
    {
        $currencyFrom = mb_strtoupper($currencyFrom);
        $currencyTo = mb_strtoupper($currencyTo);

        $requestHeaders = [
            'x-rapidapi-host' => 'fixer-fixer-currency-v1.p.rapidapi.com',
            'x-rapidapi-key' => 'fe8234dda2mshff811bf5d90c3f6p1cf7ffjsn3ca8a0e42da6'
        ];

        $queryParams = [
            'from' => $currencyFrom,
            'to' => $currencyTo,
            'amount' => $amount
        ];

        $requestOptions = [
            'headers' => $requestHeaders,
            'query' => $queryParams
        ];

        try {
            $conversionResponse =  $this->httpClient->request('GET', $this->parameterBag->get('fixerIOConvert'), $requestOptions );
            if (Response::HTTP_OK === $conversionResponse->getStatusCode()) {
                $conversionArray = $conversionResponse->toArray();
                return $conversionArray['result'];
            }
        } catch (Exception $e) {
            throw new HttpException(
                Response::HTTP_CONFLICT,
                $e->getMessage()
            );
        }

    }
}
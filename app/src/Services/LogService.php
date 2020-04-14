<?php


namespace App\Services;


use App\Repository\BankLogRepository;
use App\Entity\BankLog;

class LogService
{
    /**
     * @var BankLogRepository
     */
    private $bankLogRepository;

    /**
     * LogService constructor.
     * @param BankLogRepository $bankLogRepository
     */
    public function __construct(BankLogRepository $bankLogRepository)
    {
        $this->bankLogRepository = $bankLogRepository;
    }

    /**
     * @param int $userId
     * @param string $operation
     * @param string $detail
     */
    public function log(string $operation, string $detail) {
        $log = new BankLog();
        $log->setOperation($operation);
        $log->setDetail($detail);
        $this->bankLogRepository->insert($log);
    }
}
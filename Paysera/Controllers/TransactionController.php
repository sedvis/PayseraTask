<?php
/**
 * Created by PhpStorm.
 * User: Sedvis
 * Date: 2/27/2017
 * Time: 20:47
 */

namespace Paysera\Controllers;


use Paysera\Repositories\RepositoryInterface;
use Paysera\Repositories\TransactionRepository;

class TransactionController
{
    protected $transactionRepository;
    protected $config;

    /**
     * TransactionController constructor.
     * @param $transactionRepository
     */
    public function __construct(RepositoryInterface $repository, $config)
    {
        $this->transactionRepository = $repository;
        $this->config                = $config;
    }

    public function process($filename)
    {
        $this->transactionRepository->loadFromFile($filename);
        $transactions = $this->transactionRepository->getAll();

        $this->countCommissions($transactions);
    }

    public function countCommissions($transactions)
    {
        foreach ($transactions as $transaction) {
            if ($transaction->transactionType == 'cash_in') {
                $this->cashInCommission($transaction);
            } else {
                $this->cashOutCommission($transaction);
            }
        }
    }

    private function cashInCommission($transaction)
    {
        $commission = $transaction->transactionAmount * $this->config['inputCommissionPercent'];
        if ($commission > $this->config['inputCommissionLimitMax']) {
            printf("%0.2f\n", $this->config['inputCommissionLimitMax']);
        } else {
            printf("%0.2f\n", $commission);
        }
    }

    private function cashOutCommission($transaction)
    {
        if ($transaction->userType == 'natural') {
            
        } else {
            $commission = $transaction->transactionAmount * $this->config['outputCommissionPercentLegal'];
            if ($commission < $this->config['outputCommissionLegalLimitMin']) {
                printf("%0.2f\n", $this->config['outputCommissionLegalLimitMin']);
            } else {
                printf("%0.2f\n", $commission);
            }
        }
    }


}
<?php

namespace Paysera\Controllers;

use Paysera\Models\Transaction;
use Paysera\Repositories\RepositoryInterface;

class TransactionController
{
    /**
     * @var RepositoryInterface
     */
    protected $transactionRepository;

    /**
     * @var array
     */
    protected $config;

    /**
     * TransactionController constructor.
     *
     * @param RepositoryInterface $repository
     * @param array $config
     */
    public function __construct(RepositoryInterface $repository, array $config)
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

    private function countCommissions(array $transactions)
    {
        foreach ($transactions as $transaction) {
            if ($transaction->getTransactionType() == Transaction::CASH_IN) {
                $commission = $this->cashInCommission($transaction);
            } else {
                $commission = $this->cashOutCommission($transaction);
            }
            $this->printCommission($commission);
        }
    }

    private function cashInCommission(Transaction $transaction)
    {
        $commission     = $transaction->getTransactionAmount() * $this->config['inputCommissionPercent'];
        $convertedLimit = $this->convertCurrency($transaction, $this->config['inputCommissionLimitMax']);
        if ($commission > $convertedLimit) {
            return $convertedLimit;
        } else {
            return $commission;
        }
    }

    private function cashOutCommission(Transaction $transaction)
    {
        if ($transaction->getUserType() == 'natural') {
            $date                      = new \DateTime($transaction->getDate());
            $week                      = $date->format('W');
            $userTransactions          = $this->transactionRepository->getByField('userId', $transaction->getUserId());
            $transactionsPerWeek       = 0;
            $transactionsPerWeekAmount = 0;

            /** @var Transaction $userTransaction */
            foreach ($userTransactions as $userTransaction) {
                $currentDate = new \DateTime($userTransaction->getDate());
                if ($week == $currentDate->format('W') && $userTransaction->getTransactionType() == Transaction::CASH_OUT) {
                    if ($userTransaction->getId() == $transaction->getId()) {
                        break;
                    }
                    $transactionsPerWeek++;
                    $transactionsPerWeekAmount += $this->convertCurrency($userTransaction);
                }
            }
            if ($transactionsPerWeek >= $this->config['outputCommissionNormalFreeTransactions']) {
                $commission = $transaction->getTransactionAmount() * $this->config['outputCommissionPercentNormal'];
                return $commission;
            } else {
                if ($transactionsPerWeekAmount > $this->config['outputCommissionNormalDiscount']) {
                    $commission = $transaction->getTransactionAmount() * $this->config['outputCommissionPercentNormal'];
                    return $commission;
                } else {
                    $amount     = max($this->convertCurrency($transaction) + $transactionsPerWeekAmount - $this->config['outputCommissionNormalDiscount'],0);
                    $commission = $amount * $this->config['outputCommissionPercentNormal'];
                    return $this->convertCurrency($transaction, $commission);
                }

            }
        } else {
            $commission     = $transaction->getTransactionAmount() * $this->config['outputCommissionPercentLegal'];
            $convertedLimit = $this->convertCurrency($transaction, $this->config['outputCommissionLegalLimitMin']);
            if ($commission < $convertedLimit) {
                return $convertedLimit;
            } else {
                return $commission;
            }
        }
    }

    private function convertCurrency(Transaction $transaction, $amount = -1)
    {
        if (array_key_exists($transaction->getCurrency(), $this->config['currencyConversion'])) {
            if ($amount == -1) {
                $converted = $transaction->getTransactionAmount() / $this->config['currencyConversion'][$transaction->getCurrency()];
            } else {
                $converted = $amount * $this->config['currencyConversion'][$transaction->getCurrency()];
            }
            $fig       = pow(10, $this->config['commissionPrecision']);
            $converted = ceil($converted * $fig) / $fig;
            return $converted;
        }

        return false;
    }

    private function printCommission($commission)
    {
        fwrite(STDOUT, sprintf("%0.2f\n", $commission));
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: Sedvis
 * Date: 2/27/2017
 * Time: 20:47
 */

namespace Paysera\Controllers;


use Paysera\Models\Transaction;
use Paysera\Repositories\RepositoryInterface;

class TransactionController
{
    protected $transactionRepository;
    protected $config;

    /**
     * TransactionController constructor.
     * @param $repository
     * @param $config
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

    private function cashInCommission(Transaction $transaction)
    {
        $commission     = $transaction->transactionAmount * $this->config['inputCommissionPercent'];
        $convertedLimit = $this->convertCurrency($transaction, $this->config['inputCommissionLimitMax']);
        if ($commission > $convertedLimit) {
            $this->printCommission($convertedLimit);
        } else {
            $this->printCommission($commission);
        }
    }

    private function cashOutCommission(Transaction $transaction)
    {
        if ($transaction->userType == 'natural') {
            $date                      = new \DateTime($transaction->date);
            $week                      = $date->format('W');
            $userTransactions          = $this->transactionRepository->getByUserId($transaction->userId);
            $transactionsPerWeek       = 0;
            $transactionsPerWeekAmount = 0;
            foreach ($userTransactions as $userTransaction) {
                $currentDate = new \DateTime($userTransaction->date);
                if ($week == $currentDate->format('W') && $userTransaction->transactionType == "cash_out") {
                    if ($userTransaction->id == $transaction->id) {
                        break;
                    }
                    $transactionsPerWeek++;
                    $transactionsPerWeekAmount += $this->convertCurrency($userTransaction);
                }
            }
            if ($transactionsPerWeek >= $this->config['outputCommissionNormalFreeTransactions']) {
                $commission = $transaction->transactionAmount * $this->config['outputCommissionPercentNormal'];
                $this->printCommission($commission);
            } else {

                $commission = max($this->convertCurrency($transaction) + $transactionsPerWeekAmount
                        - $this->config['outputCommissionNormalDiscount'], 0)
                    * $this->config['outputCommissionPercentNormal'];
                $this->printCommission($this->convertCurrency($transaction, $commission));
            }
        } else {
            $commission     = $transaction->transactionAmount * $this->config['outputCommissionPercentLegal'];
            $convertedLimit = $this->convertCurrency($transaction, $this->config['outputCommissionLegalLimitMin']);
            if ($commission < $convertedLimit) {
                $this->printCommission($convertedLimit);
            } else {
                $this->printCommission($commission);
            }
        }
    }

    private function convertCurrency(Transaction $transaction, $amount = -1)
    {
        if (array_key_exists($transaction->currency, $this->config['currencyConversion'])) {
            if ($amount == -1) {
                $converted = $transaction->transactionAmount / $this->config['currencyConversion'][$transaction->currency];
            } else {
                $converted = $amount * $this->config['currencyConversion'][$transaction->currency];
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
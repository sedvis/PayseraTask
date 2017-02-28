<?php
namespace Paysera\Repositories;

use Paysera\Models\Transaction;

class TransactionRepository implements RepositoryInterface
{

    protected $transactions = [];

    /**
     * TransactionRepository constructor.
     */
    public function __construct()
    {

    }

    public function getAll()
    {
        return $this->transactions;
    }

    public function getByField($field, $value)
    {
        $userTransactions = [];
        foreach ($this->transactions as $transaction) {
            $method = 'get' . ucfirst($field);

            if (method_exists($transaction, $method)) {
                if ($transaction->$method() == $value) {
                    $userTransactions[] = $transaction;
                }
            }
        }
        return $userTransactions;
    }

    public function loadFromFile($filename)
    {
        if (file_exists($filename)) {

            $contents = file_get_contents($filename);
            $contents = explode("\r\n", $contents);

            foreach ($contents as $content) {
                if ($content != "") {
                    $content     = explode(',', $content);
                    $transaction = new Transaction();
                    $transaction->setDate($content[0]);
                    $transaction->setUserId($content[1]);
                    $transaction->setUserType($content[2]);
                    $transaction->setTransactionType($content[3]);
                    $transaction->setTransactionAmount($content[4]);
                    $transaction->setCurrency($content[5]);
                    $this->transactions[] = $transaction;
                }
            }
        }
    }

    public function add(Transaction $transaction)
    {
        $this->transactions[] = $transaction;
    }


}

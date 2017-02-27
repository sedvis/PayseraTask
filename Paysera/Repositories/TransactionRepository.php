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

    public function getByUserId($id)
    {
        $userTransactions = [];
        foreach ($this->transactions as $transaction) {
            if ($transaction->userId == $id) {
                $userTransactions[] = $transaction;
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
                    $content              = explode(',', $content);
                    $transaction          = new Transaction($content[0], $content[1], $content[2], $content[3], $content[4], $content[5]);
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

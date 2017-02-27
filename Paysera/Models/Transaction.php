<?php
namespace Paysera\Models;

class Transaction
{
    public $date;

    public $userId;

    public $userType;

    public $transactionType;

    public $transactionAmount;

    public $currency;

    /**
     * Transaction constructor.
     * @param $date
     * @param $userId
     * @param $userType
     * @param $transactionType
     * @param $transactionAmount
     * @param $currency
     */
    public function __construct($date, $userId, $userType, $transactionType, $transactionAmount, $currency)
    {
        $this->date              = $date;
        $this->userId            = $userId;
        $this->userType          = $userType;
        $this->transactionType   = $transactionType;
        $this->transactionAmount = $transactionAmount;
        $this->currency          = $currency;
    }




}
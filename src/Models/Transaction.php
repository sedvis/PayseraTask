<?php
namespace Paysera\Models;

class Transaction
{
    const CASH_IN = "cash_in";

    const CASH_OUT = "cash_out";

    private $id;

    private $date;

    private $userId;

    private $userType;

    private $transactionType;

    private $transactionAmount;

    private $currency;

    /**
     * Transaction constructor.
     * @param $id
     */
    public function __construct()
    {
        $this->id = uniqid();
    }


    /**
     * @return mixed
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param mixed $date
     */
    public function setDate($date)
    {
        $this->date = $date;
    }

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param mixed $userId
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    /**
     * @return mixed
     */
    public function getUserType()
    {
        return $this->userType;
    }

    /**
     * @param mixed $userType
     */
    public function setUserType($userType)
    {
        $this->userType = $userType;
    }

    /**
     * @return mixed
     */
    public function getTransactionType()
    {
        return $this->transactionType;
    }

    /**
     * @param mixed $transactionType
     */
    public function setTransactionType($transactionType)
    {
        $this->transactionType = $transactionType;
    }

    /**
     * @return mixed
     */
    public function getTransactionAmount()
    {
        return $this->transactionAmount;
    }

    /**
     * @param mixed $transactionAmount
     */
    public function setTransactionAmount($transactionAmount)
    {
        $this->transactionAmount = $transactionAmount;
    }

    /**
     * @return mixed
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @param mixed $currency
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }


}
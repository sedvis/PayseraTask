<?php
/**
 * Created by PhpStorm.
 * User: Sedvis
 * Date: 2/28/2017
 * Time: 16:56
 */

namespace Paysera\Controllers;


use Paysera\Models\Transaction;
use Paysera\Repositories\TransactionRepository;
use ReflectionClass;
use Paysera\Controllers\TransactionController;

class TransactionControllerTest extends \PHPUnit_Framework_TestCase
{
    private $config;

    /**
     * TransactionControllerTest constructor.
     * @param mixed $config
     */
    public function __construct()
    {
        $this->config = include('../../config.php');
    }

    protected static function getMethod($class, $name)
    {
        $class  = new ReflectionClass($class);
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        return $method;
    }


    public function testCashInCommissionEUR()
    {

        $method      = self::getMethod('Paysera\Controllers\TransactionController', 'cashInCommission');
        $obj         = new TransactionController(new TransactionRepository(), $this->config);
        $transaction = new Transaction();
        $transaction->setDate("2016-01-05");
        $transaction->setUserId("1");
        $transaction->setUserType("natural");
        $transaction->setTransactionType("cash_in");
        $transaction->setTransactionAmount("200.00");
        $transaction->setCurrency("EUR");

        $result = $method->invokeArgs($obj, [$transaction]);

        self::assertEquals(0.06, $result);
    }

    public function testCashInCommissionUSD()
    {
        $method      = self::getMethod('Paysera\Controllers\TransactionController', 'cashInCommission');
        $obj         = new TransactionController(new TransactionRepository(), $this->config);
        $transaction = new Transaction();
        $transaction->setDate("2016-01-05");
        $transaction->setUserId("1");
        $transaction->setUserType("natural");
        $transaction->setTransactionType("cash_in");
        $transaction->setTransactionAmount("100.00");
        $transaction->setCurrency("USD");

        $result = $method->invokeArgs($obj, [$transaction]);

        self::assertEquals(0.03, $result);
    }

    public function testCashInCommissionJPY()
    {
        $method      = self::getMethod('Paysera\Controllers\TransactionController', 'cashInCommission');
        $obj         = new TransactionController(new TransactionRepository(), $this->config);
        $transaction = new Transaction();
        $transaction->setDate("2016-01-05");
        $transaction->setUserId("1");
        $transaction->setUserType("natural");
        $transaction->setTransactionType("cash_in");
        $transaction->setTransactionAmount("10000");
        $transaction->setCurrency("JPY");

        $result = $method->invokeArgs($obj, [$transaction]);

        self::assertEquals(3, $result);
    }


    public function testConvertCurrencyToEUR()
    {
        $method      = self::getMethod('Paysera\Controllers\TransactionController', 'convertCurrency');
        $obj         = new TransactionController(new TransactionRepository(), $this->config);
        $transaction = new Transaction();
        $transaction->setDate("2016-01-05");
        $transaction->setUserId("1");
        $transaction->setUserType("natural");
        $transaction->setTransactionType("cash_in");
        $transaction->setTransactionAmount("10000");
        $transaction->setCurrency("JPY");

        $result = $method->invokeArgs($obj, [$transaction]);

        self::assertEquals(77.21, $result);
    }

    public function testConvertCurrencyFromEUR()
    {
        $method      = self::getMethod('Paysera\Controllers\TransactionController', 'convertCurrency');
        $obj         = new TransactionController(new TransactionRepository(), $this->config);
        $transaction = new Transaction();
        $transaction->setDate("2016-01-05");
        $transaction->setUserId("1");
        $transaction->setUserType("natural");
        $transaction->setTransactionType("cash_in");
        $transaction->setTransactionAmount("10000");
        $transaction->setCurrency("JPY");

        $result = $method->invokeArgs($obj, [$transaction, 100]);

        self::assertEquals(12953, $result);
    }

    public function testCashOutCommissionOneTransactionNatural()
    {
        $method      = self::getMethod('Paysera\Controllers\TransactionController', 'cashOutCommission');
        $obj         = new TransactionController(new TransactionRepository(), $this->config);
        $transaction = new Transaction();
        $transaction->setDate("2016-01-05");
        $transaction->setUserId("1");
        $transaction->setUserType("natural");
        $transaction->setTransactionType("cash_out");
        $transaction->setTransactionAmount("1100");
        $transaction->setCurrency("EUR");

        $result = $method->invokeArgs($obj, [$transaction]);

        self::assertEquals(0.3, $result);
    }

    public function testCashOutCommissionOneTransactionLegalMin()
    {
        $method      = self::getMethod('Paysera\Controllers\TransactionController', 'cashOutCommission');
        $obj         = new TransactionController(new TransactionRepository(), $this->config);
        $transaction = new Transaction();
        $transaction->setDate("2016-01-05");
        $transaction->setUserId("1");
        $transaction->setUserType("legal");
        $transaction->setTransactionType("cash_out");
        $transaction->setTransactionAmount("50");
        $transaction->setCurrency("EUR");

        $result = $method->invokeArgs($obj, [$transaction]);

        self::assertEquals(0.5, $result);
    }

    public function testCashOutCommissionOneTransactionLegalMax()
    {
        $method      = self::getMethod('Paysera\Controllers\TransactionController', 'cashOutCommission');
        $obj         = new TransactionController(new TransactionRepository(), $this->config);
        $transaction = new Transaction();
        $transaction->setDate("2016-01-05");
        $transaction->setUserId("1");
        $transaction->setUserType("legal");
        $transaction->setTransactionType("cash_out");
        $transaction->setTransactionAmount("5000");
        $transaction->setCurrency("EUR");

        $result = $method->invokeArgs($obj, [$transaction]);

        self::assertEquals(15, $result);
    }


}

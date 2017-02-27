<?php
require_once __DIR__ . '/vendor/autoload.php';

use Paysera\Repositories\TransactionRepository;
use Paysera\Controllers\TransactionController;

if ($argc != 2) {
    die('File not specified');
}

$config = include('config.php');

$transactionController = new TransactionController(new TransactionRepository(), $config);
$transactionController->process($argv[1]);

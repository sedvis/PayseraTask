<?php
namespace Paysera\Repositories;

use Paysera\Models\Transaction;

interface RepositoryInterface
{
    public function getAll();

    public function getByField($field, $value);

    public function loadFromFile($filename);

    public function add(Transaction $transaction);
}

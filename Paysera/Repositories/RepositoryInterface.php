<?php
namespace Paysera\Repositories;

use Paysera\Models\Transaction;

interface RepositoryInterface
{
    public function getAll();

    public function getByUserId($id);

    public function loadFromFile($filename);
}

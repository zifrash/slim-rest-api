<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Connection;
use App\Entities\ProductEntity;
use App\Exceptions\MyException;

class ProductRepository extends Repository
{
    public function __construct() {
        $this->connection = Connection::getInstance()->getConnection();
        $this->entity = new ProductEntity();
        $this->table = 'products';
        $this->exception = new MyException();
    }
}
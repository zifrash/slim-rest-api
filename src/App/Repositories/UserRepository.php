<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Connection;
use App\Entities\UserEntity;
use App\Exceptions\MyException;

class UserRepository extends Repository
{
    public function __construct() {
        $this->connection = Connection::getInstance()->getConnection();
        $this->entity = new UserEntity();
        $this->table = 'users';
        $this->exception = new MyException();
    }

    public function getByEmail(string $email): UserEntity
    {
        $prepare = $this->connection->prepare("SELECT * FROM \"{$this->table}\" WHERE email = :email;");
        $prepare->execute(['email' => $email]);

        $data = $prepare->fetch();

        if ($data === false) {
            throw new $this->exception("User with email: {$email} - not found", 404);
        }

        return $this->entity::create($data);
    }
}
<?php

declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

class CreateUser extends AbstractSeed
{
    public function run(): void
    {
        function ph(string $pass): string { return password_hash($pass, PASSWORD_DEFAULT); }

        $usersData = [
            ['name' => 'igor', 'password' => ph('qwe123'), 'email' => 'igor@mail.ru', 'phone' => '79000000000'],
            ['name' => 'ivan', 'password' => ph('qwe123'), 'email' => 'ivan@mail.ru', 'phone' => '79000000001'],
            ['name' => 'petr', 'password' => ph('qwe123'), 'email' => 'petr@mail.ru', 'phone' => '79000000002'],
            ['name' => 'maria', 'password' => ph('qwe123'), 'email' => 'maria@mail.ru', 'phone' => '79000000003'],
            ['name' => 'irina', 'password' => ph('qwe123'), 'email' => 'irina@mail.ru', 'phone' => '79000000004'],
        ];

        $usersData[0]['is_admin'] = true;

        $users = $this->table('users');
        $users->truncate();
        $users->insert($usersData)->saveData();
    }
}

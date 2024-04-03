<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateUserTable extends AbstractMigration
{
    public function change(): void
    {
        $users = $this->table('users');
        $users->addColumn('name', 'string')
              ->addColumn('password', 'string')
              ->addColumn('email', 'string')
              ->addColumn('phone', 'string', ['limit' => 11])
              ->addColumn('is_admin', 'boolean', ['default' => false])
              ->addTimestamps()
              ->addIndex(['email'], ['unique' => true])
              ->create();
    }
}

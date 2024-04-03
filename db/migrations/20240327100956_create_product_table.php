<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateProductTable extends AbstractMigration
{
    public function change(): void
    {
        $products = $this->table('products');
        $products->addColumn('name', 'string')
                 ->addColumn('price', 'float')
                 ->addColumn('quantity', 'integer')
                 ->addTimestamps()
                 ->create();
    }
}

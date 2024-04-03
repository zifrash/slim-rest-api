<?php

declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

class CreateProduct extends AbstractSeed
{
    public function run(): void
    {
        $productData = [
            ['name' => 'chocolate', 'price' => 10.95, 'quantity' => 184],
            ['name' => 'donut', 'price' => 5.48, 'quantity' => 94],
            ['name' => 'marmalade', 'price' => 99.99, 'quantity' => 9],
            ['name' => 'candy', 'price' => 1.25, 'quantity' => 1347],
            ['name' => 'lollipops', 'price' => 0.95, 'quantity' => 832],
        ];

        $products = $this->table('products');
        $products->truncate();
        $products->insert($productData)->saveData();
    }
}

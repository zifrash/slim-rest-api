<?php

namespace App\Entities;

interface EntityInterface
{
    public function getFields(): array;
    public function getFieldsType(): array;
    public static function createFromDbData(array $dbUserData): self;
    public static function create(?array $data = null): self;
}
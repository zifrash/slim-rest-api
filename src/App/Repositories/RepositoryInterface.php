<?php

namespace App\Repositories;

use App\Entities\EntityInterface;
use App\Exceptions\ExceptionInterface;

interface RepositoryInterface
{
    /** @return EntityInterface[] */
    public function getAll(): array;
    /** @throws ExceptionInterface */
    public function getById(string|int $id): EntityInterface;
    /** @throws ExceptionInterface */
    public function create(EntityInterface $entity): EntityInterface;
    /** @throws ExceptionInterface */
    public function update(string|int $id, array $data): EntityInterface;
    /** @throws ExceptionInterface */
    public function delete(string|int $id): true;
}
<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Connection;
use App\Entities\EntityInterface;
use App\Enums\EntitiesFiledType;
use App\Exceptions\ExceptionInterface;
use Cake\Chronos\Chronos;
use PDO;
use PDOException;
use PDOStatement;

abstract class Repository implements RepositoryInterface
{
    protected PDO $connection;
    protected EntityInterface $entity;
    protected string $table;
    protected ExceptionInterface $exception;

    /** @return EntityInterface[] */
    public function getAll(): array
    {
        return array_map(
            function($usersData) { return $this->entity::createFromDbData($usersData); },
            $this->connection->query("SELECT * FROM \"{$this->table}\" ORDER BY id;")->fetchAll()
        );
    }

    /** @throws ExceptionInterface */
    public function getById(string|int $id): EntityInterface
    {
        $prepare = $this->connection->prepare("SELECT * FROM \"{$this->table}\" WHERE id = :id;");
        $prepare->execute(['id' => $id]);

        $data = $prepare->fetch();

        if ($data === false) {
            throw new $this->exception("Element id: {$id} - not found", 404);
        }

        return $this->entity::create($data);
    }

    /** @throws ExceptionInterface */
    public function create(EntityInterface $entity): EntityInterface
    {
        $entityData = $entity->getFields();
        $entityDataType = $entity->getFieldsType();
        unset($entityData['id']);

        [
            'keys' => $keys,
            'values' => $values
        ] = $this->makePDOCreateData($entityData);

        $prepare = $this->connection->prepare("INSERT INTO \"{$this->table}\" ({$keys}) VALUES ({$values});");

        try {
            $this->prepareExecute($prepare, $entityData, $entityDataType);
        } catch (PDOException $e) {
            throw new $this->exception($e->getMessage(), 422);
        }

        $insertId = (int) $this->connection->lastInsertId();
        if ($insertId > 0) {
            $entity->setId($insertId);
        } else {
            throw new $this->exception('Element doesnt create', 422);
        }

        return $entity;
    }

    /** @throws ExceptionInterface */
    public function update(string|int $id, array $data): EntityInterface
    {
        $entity = $this->getById($id);
        $entityData = $entity->getFields();
        $entityDataType = $entity->getFieldsType();

        $updateData = array_diff_assoc($data, $entityData);

        if ($updateData) {
            $updateData['updated_at'] = new Chronos(date(Connection::$DBTimeFormat));

            $setStr = $this->makePDOSetString($updateData);
            $prepare = $this->connection->prepare("UPDATE \"{$this->table}\" SET {$setStr} WHERE id = :id;");

            $updateData['id'] = (int) $id;

            $this->prepareExecute($prepare, $updateData, $entityDataType);

            if ($prepare->rowCount() < 1) {
                throw new $this->exception('Element doesnt update', 422);
            }
        }

        return $entity::createFromDbData(array_merge($entityData, $updateData));
    }

    /** @throws ExceptionInterface */
    public function delete(string|int $id): true
    {
        $entity = $this->getById($id);

        $prepare = $this->connection->prepare("DELETE FROM \"{$this->table}\" WHERE id = :id;");
        $prepare->execute(['id' => $entity->getId()]);

        if ($prepare->rowCount() < 1) {
            throw new $this->exception('Element doesnt delete', 422);
        }

        return true;
    }

    /** @return array{"keys": string, "values": string} */
    private function makePDOCreateData(array $data): array
    {
        $PDOArray = [
            'keys' => '',
            'values' => '',
        ];
        foreach ($data as $key => $value) {
            $PDOArray['keys'] .= strlen($PDOArray['keys']) > 0 ? ', ' : '';
            $PDOArray['keys'] .= $key;
            $PDOArray['values'] .= strlen($PDOArray['values']) > 0 ? ', ' : '';
            $PDOArray['values'] .= ":{$key}";
        }

        return $PDOArray;
    }

    private function makePDOSetString(array $data): string
    {
        $PDOString = '';
        foreach ($data as $key => $value) {
            $PDOString .= strlen($PDOString) > 0 ? ', ' : '';
            $PDOString .= "$key = :$key";
        }

        return $PDOString;
    }

    private function prepareExecute(PDOStatement $prepare, array $entityData, array $entityDataType): void
    {
        array_walk($entityData, function($data, $key) use ($prepare, $entityDataType) {
            $type = $entityDataType[$key] ?? ($entityDataType['default'] ?? EntitiesFiledType::getDefaultType());
            if ($type->value < 100) {
                $prepare->bindValue($key, $data, $type->value);
            } else if ($type === EntitiesFiledType::DateTime) {
                $prepare->bindValue($key, $data?->toDateTimeString());
            }
        });

        $prepare->execute();
    }
}
<?php

declare(strict_types=1);

namespace App\Entities;

use App\Connection;
use App\Enums\EntitiesFiledType;
use Cake\Chronos\Chronos;
use JsonSerializable;

class ProductEntity implements EntityInterface, JsonSerializable
{
    private array $fields = [
        'id' => 0,
        'name' => '',
        'price' => 0.0,
        'quantity' => 0,
        'created_at' => null,
        'updated_at' => null
    ];

    private array $fieldsType = [
        'id' => EntitiesFiledType::Int,
        'name' => EntitiesFiledType::String,
        'price' => EntitiesFiledType::String,
        'quantity' => EntitiesFiledType::Int,
        'created_at' => EntitiesFiledType::DateTime,
        'updated_at' => EntitiesFiledType::DateTime
    ];

    public function getId(): int
    {
        return $this->fields['id'];
    }
    public function setId(int|string $id): self
    {
        $this->fields['id'] = (int) $id;

        return $this;
    }

    public function getName(): string
    {
        return $this->fields['name'];
    }
    public function setName(string $name): self
    {
        $this->fields['name'] = $name;

        return $this;
    }

    public function getPrice(): float
    {
        return $this->fields['price'];
    }
    public function setPrice(float|string $price): self
    {
        $this->fields['price'] = (float) $price;

        return $this;
    }

    public function getQuantity(): int
    {
        return $this->fields['quantity'];
    }
    public function setQuantity(int $quantity): self
    {
        $this->fields['quantity'] = $quantity;

        return $this;
    }

    public function getCreatedTime(): ?Chronos
    {
        return $this->fields['created_at'];
    }
    private function setCreatedTime(?string $dateTime): self
    {
        if ($dateTime) {
            $this->fields['created_at'] = new Chronos($dateTime);
        }

        return $this;
    }

    public function getUpdatedTime(): ?Chronos
    {
        return $this->fields['updated_at'];
    }
    private function setUpdatedTime(?string $dateTime): self
    {
        if ($dateTime) {
            $this->fields['updated_at'] = new Chronos($dateTime);
        }

        return $this;
    }

    private function setFieldsFromDbData(array $fields): self
    {
        $fields['price'] = (float) $fields['price'];
        $fields['created_at'] = empty($fields['created_at']) ? null : new Chronos($fields['created_at']);
        $fields['updated_at'] = empty($fields['updated_at']) ? null : new Chronos($fields['updated_at']);

        $this->fields = $fields;

        return $this;
    }
    public function getFields(): array
    {
        return $this->fields;
    }
    public function getFieldsType(): array
    {
        return $this->fieldsType;
    }

    public static function createFromDbData(array $dbUserData): self
    {
        return (new self())->setFieldsFromDbData($dbUserData);
    }

    public static function create(?array $data = []): self
    {
        return (new self())
            ->setId($data['id'] ?? 0)
            ->setName($data['name'] ?? '')
            ->setPrice($data['price'] ?? 0.0)
            ->setQuantity($data['quantity'] ?? 0)
            ->setCreatedTime($data['created_at'] ?? date(Connection::$DBTimeFormat))
            ->setUpdatedTime($data['updated_at'] ?? null);
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'price' => $this->getPrice(),
            'quantity' => $this->getQuantity(),
            'createdTime' => $this->getCreatedTime()?->toDateTimeString(),
            'updatedTime' => $this->getUpdatedTime()?->toDateTimeString()
        ];
    }
}
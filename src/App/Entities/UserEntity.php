<?php

declare(strict_types=1);

namespace App\Entities;

use App\Connection;
use App\Enums\EntitiesFiledType;
use Cake\Chronos\Chronos;
use JsonSerializable;

class UserEntity implements EntityInterface, JsonSerializable
{
    private array $fields = [
        'id' => 0,
        'name' => '',
        'email' => '',
        'phone' => '',
        'password' => '',
        'is_admin' => false,
        'created_at' => null,
        'updated_at' => null
    ];

    private array $fieldsType = [
        'default' => EntitiesFiledType::String,
        'id' => EntitiesFiledType::Int,
        'is_admin' => EntitiesFiledType::Bool,
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

    public function getEmail(): string
    {
        return $this->fields['email'];
    }
    public function setEmail(string $email): self
    {
        $this->fields['email'] = $email;

        return $this;
    }

    public function getPhone(): string
    {
        return $this->fields['phone'];
    }
    public function setPhone(string $phone): self
    {
        $this->fields['phone'] = $phone;

        return $this;
    }

    public function checkPass(string $password): bool
    {
        return password_verify($password, $this->fields['password']);
    }
    public function setPassword(string $password): self
    {
        $this->fields['password'] = password_hash($password, PASSWORD_DEFAULT);

        return $this;
    }

    public function isAdmin(): bool
    {
        return $this->fields['is_admin'];
    }
    public function setIsAdmin(bool $isAdmin): self
    {
        $this->fields['is_admin'] = $isAdmin;

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
            ->setEmail($data['email'] ?? '')
            ->setPhone($data['phone'] ?? '')
            ->setPassword($data['password'] ?? '')
            ->setIsAdmin($data['is_admin'] ?? false)
            ->setCreatedTime($data['created_at'] ?? date(Connection::$DBTimeFormat))
            ->setUpdatedTime($data['updated_at'] ?? null);
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'email' => $this->getEmail(),
            'phone' => $this->getPhone(),
            'createdTime' => $this->getCreatedTime()?->toDateTimeString(),
            'updatedTime' => $this->getUpdatedTime()?->toDateTimeString()
        ];
    }
}
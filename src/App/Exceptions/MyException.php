<?php

declare(strict_types=1);

namespace App\Exceptions;

use App\Helpers\JsonHelper;
use Exception;

class MyException extends Exception implements ExceptionInterface
{
    public function getErrorTemplate(): array
    {
        return [
            'error' => [
                'code' => $this->getCode(),
                'message' => $this->getMessage()
            ]
        ];
    }

    public function getErrorTemplateJson(): string
    {
        return JsonHelper::toJson($this->getErrorTemplate());
    }
}
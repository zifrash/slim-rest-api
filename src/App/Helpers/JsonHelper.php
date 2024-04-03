<?php

declare(strict_types=1);

namespace App\Helpers;

class JsonHelper
{
    public static function toJson(mixed $data): string
    {
        return json_encode($data, JSON_PRETTY_PRINT);
    }

    public static function toArray(string $json): array
    {
        return json_decode($json, true);
    }
}
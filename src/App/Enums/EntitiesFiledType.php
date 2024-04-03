<?php

namespace App\Enums;

enum EntitiesFiledType: int
{
    case Int = 1;
    case String = 2;
    case Bool = 5;
    case DateTime = 101; // Chronos

    public static function getDefaultType(): self
    {
        return self::String;
    }
}

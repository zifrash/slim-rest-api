<?php

namespace App\Exceptions;

interface ExceptionInterface
{
    public function getErrorTemplateJson(): string;
}
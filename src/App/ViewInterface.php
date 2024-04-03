<?php

namespace App;

use Psr\Http\Message\ResponseInterface;

interface ViewInterface
{
    public function render(ResponseInterface $response, string $template, array $data): ResponseInterface;
}
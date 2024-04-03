<?php

declare(strict_types=1);

namespace App\Controllers;

use App\ViewInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class MainPageController
{
    public function __construct(
        protected ViewInterface $view
    ) {}

    public function init(Request $request, Response $response): Response
    {
        return $this->view->render($response, 'main.twig', []);
    }
}
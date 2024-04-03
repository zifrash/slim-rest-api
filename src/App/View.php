<?php

declare(strict_types=1);

namespace App;

use Psr\Http\Message\ResponseInterface;
use Twig\Environment as Twig;

readonly class View implements ViewInterface
{
    public function __construct(private Twig $view) {}

    public function render(ResponseInterface $response, string $template, array $data): ResponseInterface
    {
        $body = $this->view->render($template, $data);

        $response->getBody()->write($body);

        return $response;
    }
}

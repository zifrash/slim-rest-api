<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Helpers\JsonHelper;
use App\Repositories\UserRepository;
use App\Settings;
use App\ViewInterface;
use Exception;
use Firebase\JWT\JWT;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints as Assert;

class AuthController
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly UserRepository $repository,
        protected ViewInterface $view
    ) {}

    public function init(Request $request, Response $response): Response
    {
        return $this->view->render($response, 'auth.twig', []);
    }

    /** @throws Exception */
    public function login(Request $request, Response $response): Response
    {
        $body = $request->getParsedBody();

        if ($body === null) {
            throw new Exception('Wrong user auth data!', 422);
        }

        $error = Validation::createValidator()->validate(
            $body,
            new Assert\Collection([
                'email' => new Assert\Email(),
                'password' => new Assert\Type('string')
            ])
        );

        if (count($error) > 0) {
            throw new Exception($error->get(0)->getMessage(), 422);
        }

        $user = $this->repository->getByEmail($body['email']);
        if (!$user->checkPass($body['password'])) {
            $this->logger->log(LogLevel::ERROR, "401 login wrong password");

            $response->getBody()->write('wrong password');
            return $response->withStatus(401);
        }

        $setting = Settings::init('jwt');

        $payload = [
            'user' => [
                'id' => $user->getId(),
                'name' => $user->getName(),
                'email' => $user->getEmail(),
                'isAdmin' => $user->isAdmin()
            ],
            'iat' => time(),
            'exp' => time() + $setting->get('expTimeSec', 3600)
        ];
        $key = $setting->get('secretKey', 'NchO0yrG7WSmEMTJ8gjy');
        $alg = $setting->get('alg', 'HS256');

        $response->getBody()->write(JsonHelper::toJson([
            'token' => JWT::encode($payload, $key, $alg),
            'type' => 'Bearer',
            'exp' => $payload['exp']
        ]));

        return $response;
    }

    public function logout(Request $request, Response $response): Response
    {
        return $response;
    }

    public function refresh(Request $request, Response $response): Response
    {
        return $response;
    }

    public function me(Request $request, Response $response): Response
    {
        return $response;
    }
}
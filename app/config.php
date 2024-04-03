<?php

use Monolog\Level;
use Psr\Log\LoggerInterface;

return [
    'errorMiddleware' => [
        'displayErrorDetails' => true,
        'logErrors' => true,
        'logErrorDetails' => true,
        'logger' => LoggerInterface::class
    ],
    'logger' => [
        'name' => 'slim-project',
        'stream' => __DIR__ . "/../logs/slim-project.log",
        'level' => Level::Debug,
        'maxDayStorage' => 30
    ],
    'view' => [
        'path' => __DIR__ . '/../src/Templates',
        'settings' => [
            'cache' => false
        ]
    ],
    'connection' => [
        'app' => 'pgsql',
        'host' => 'postgres',
        'port' => 5432,
        'db' => $_ENV['DB_DATABASE'] ?? 'db',
        'user' => $_ENV['DB_USER'] ?? 'user',
        'password' => $_ENV['DB_PASSWORD'] ?? 'password'
    ],
    'jwt' => [
        'secretKey' => $_ENV['JWT_SECRET_KEY'] ?? '6J7QvDePQHhbOqFTThfk',
        'expTimeSec' => $_ENV['JWT_EXP_TIME_SEC'] ?? 3600,
        'alg' => $_ENV['JWT_ALG'] ?? 'HS256'
    ]
];
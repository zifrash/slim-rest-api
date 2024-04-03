<?php

require __DIR__ . '/../vendor/autoload.php';

use App\Settings;

$settings = Settings::init('connection');

return
[
    'paths' => [
        'migrations' => 'db/migrations',
        'seeds' => 'db/seeds'
    ],
    'environments' => [
        'default_migration_table' => 'migration',
        'default_environment' => 'production',
        'production' => [
            'adapter' => $settings->get('app'),
            'host' => $settings->get('host'),
            'name' => $settings->get('db'),
            'user' => $settings->get('user'),
            'pass' => $settings->get('password'),
            'port' => $settings->get('port'),
            'charset' => 'utf8',
        ]
    ],
    'version_order' => 'creation'
];

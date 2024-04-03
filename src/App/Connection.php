<?php

declare(strict_types=1);

namespace App;

use PDO;

class Connection
{
    private PDO $connection;
    public static ?Connection $instance = null;

    public static string $DBTimeFormat = 'Y-m-d H:i:s';

    private function __construct() {
        $settings = Settings::init('connection');

        $pdoApp = $settings->get('app', 'pgsql');
        $pdoHost = $settings->get('host', 'localhost');
        $pdoPort = $settings->get('port', 5432);
        $pdoDB = $settings->get('db', $_ENV['DB_DATABASE'] ?? 'db');
        $pdoUser = $settings->get('user', $_ENV['DB_USER'] ?? 'user');
        $pdoPassword = $settings->get('password', $_ENV['DB_PASSWORD'] ?? 'password');

        $this->connection = new PDO(
            "{$pdoApp}:host={$pdoHost};port={$pdoPort};dbname={$pdoDB};",
            $pdoUser,
            $pdoPassword,
            [PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
        );
    }

    public static function getInstance(): Connection
    {
        if(!self::$instance instanceof self) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function getConnection(): PDO
    {
        return $this->connection;
    }

}
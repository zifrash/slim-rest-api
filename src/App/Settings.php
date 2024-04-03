<?php

declare(strict_types=1);

namespace App;

use Exception;

class Settings
{
    private string $configFile = __DIR__ . '/../../app/config.php';
    private array $config;

    /** @throws Exception */
    public function __construct(?array $config = null)
    {
        if ($config !== null) {
            $this->config = $config;
        } else if (is_file($this->configFile)) {
            $this->config = require $this->configFile;
        } else {
            throw new Exception('Wrong Setting config!');
        }
    }

    /** @throws Exception */
    public static function init(?string $section = null): self
    {
        $setting = new self();

        if ($section !== null) {
            $setting = $setting->getSection($section);
        }

        return $setting;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->config[$key] ?? $default;
    }

    public function has(string $key): bool
    {
        return $this->get($key) !== null;
    }

    /** @throws Exception */
    public function getSection(string $key): self
    {
        return new self($this->get($key, []));
    }

    public function getAll(): array
    {
        return $this->config;
    }
}
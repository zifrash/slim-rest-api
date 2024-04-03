<?php

declare(strict_types=1);

use App\Settings;
use App\View;
use App\ViewInterface;
use DI\ContainerBuilder;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Level;
use Monolog\Logger;
use Monolog\Processor\WebProcessor;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        LoggerInterface::class => function (ContainerInterface $container) {
            $defaultLoggerName = 'app';
            $defaultLoggerStream = __DIR__ . "/../logs/{$defaultLoggerName}.log";
            $defaultLoggerLevel = Level::Error;
            $defaultLoggerMaxDayStorage = 30;

            $settings = Settings::init('logger');
            $loggerName = $settings->get('name', $defaultLoggerName);
            $loggerStream = $settings->get('stream', $defaultLoggerStream);
            $loggerLevel = $settings->get('level', $defaultLoggerLevel);
            $loggerMaxDayStorage = $settings->get('maxDayStorage', $defaultLoggerMaxDayStorage);

            $processor = new WebProcessor();

            $handler = new RotatingFileHandler($loggerStream, $loggerMaxDayStorage, $loggerLevel);
            $handler->setFormatter(new LineFormatter(
                allowInlineLineBreaks: true,
                ignoreEmptyContextAndExtra: true
            ));

            return new Logger($loggerName, [$handler], [$processor]);
        },
        ViewInterface::class => function(ContainerInterface $container) {
            $defaultTwigPath = __DIR__ . '/../src/Templates';
            $defaultTwigSettings = [];

            $settings = Settings::init('view');
            $twigPath = $settings->get('path', $defaultTwigPath);
            $twigSettings = $settings->get('settings', $defaultTwigSettings);

            $twigLoader = new FilesystemLoader($twigPath);
            $twig = new Environment($twigLoader, $twigSettings);

            return new View($twig);
        }
    ]);
};

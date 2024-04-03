<?php

declare(strict_types=1);

use App\Settings;
use Slim\App;

return function (App $app) {
    $container = $app->getContainer();

    $errorSettings = Settings::init('errorMiddleware');
    $errorLogger = $errorSettings->has('logger') ? $container->get($errorSettings->get('logger')) : null;
    $app->addErrorMiddleware(
        $errorSettings->get('displayErrorDetails', false),
        $errorSettings->get('logErrors', false),
        $errorSettings->get('logErrorDetails', false),
        $errorLogger
    );
};

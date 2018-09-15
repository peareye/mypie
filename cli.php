<?php
/**
 * Command Line Interface (CLI) for Slim Framework
 * Executing this file from the CLI allows one to run the app, not as HTTP Request
 */

// Set encoding
mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');

// Define the application root directory
define('ROOT_DIR', __DIR__ . '/');

// Load the bootstrap file and return $app object to get things started
$app = require ROOT_DIR . 'app/bootstrap.php';

if (PHP_SAPI == 'cli') {
    // Pop the filename from the top of the array
    $argv = $GLOBALS['argv'];
    array_shift($argv);

    // Turn argument array into route
    $pathInfo = implode('/', $argv);

    // Create mock environment and modify app
    $env = \Slim\Http\Environment::mock([
        'REQUEST_URI' => '/' . $pathInfo,
    ]);

    $container = $app->getContainer();
    $container['environment'] = $env;

    // Update error handing
    $container['errorHandler'] = function ($container) {
        return function ($request, $response, $exception) use ($container) {
            print('Error');
            exit(1);
        };
    };

    $container['notFoundHandler'] = function ($container) {
        return function ($request, $response) use ($container) {
            print('Not Found');
            exit(1);
        };
    };
} else {
    echo 'Not CLI';
    exit(1);
}

// And away we go!
$app->run();

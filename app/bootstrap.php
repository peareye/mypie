<?php
/**
 * Load Base Files and Return Application
 *
 * Set:
 * - Constants
 * - Composer autoloader
 * - Configuration
 * - Load App
 * - Dependencies
 * - Middleware
 * - Routes
 */

// Load the Composer Autoloader
require ROOT_DIR . 'vendor/autoload.php';

// Wrap bootstraping code in an anonymous function to avoid unnecessary globals
return call_user_func(
    function () {

        // Load default and local configuration settings
        require ROOT_DIR . 'config/config.default.php';

        if (file_exists(ROOT_DIR . 'config/config.local.php')) {
            require ROOT_DIR . 'config/config.local.php';
        }

        // Set error reporting level
        if ($config['production'] === true) {
            ini_set('display_errors', 'Off');
            error_reporting(0);
            $config['displayErrorDetails'] = false;
        } else {
            // Development
            error_reporting(-1);
            $config['displayErrorDetails'] = true;
        }

        // Create the application
        $app = new Slim\App(['settings' => $config]);

        // Load dependencies
        require ROOT_DIR . 'config/dependencies.php';

        // Load middleware - for future development

        // Load routes
        require ROOT_DIR . 'config/routesAdmin.php';
        require ROOT_DIR . 'config/routes.php';

        return $app;
    }
);

<?php
/**
 * Application Routes
 */

//
// Public (unsecured) routes
//

// 
$app->get('/sampleurl', function ($request, $response, $args) {
    return $this->container->view->render($response, 'home.html');
})->setName('sampleUrl');

// Home page '/' is always the last route, the default
$app->get('/', function ($request, $response, $args) {
    return $this->container->view->render($response, 'home.html');
})->setName('home');

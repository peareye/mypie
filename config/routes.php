<?php
/**
 * Application Routes
 */

//
// Public (unsecured) routes
//

// Submit contact message
$app->post('/contact-message', function ($request, $response, $args) {
    return (new Piton\Controllers\ContactController($this))->submitMessage($request, $response, $args);
})->setName('submitMessage');

// Thank you page for contact messages
$app->get('/thank-you', function ($request, $response, $args) {
    return (new Piton\Controllers\ContactController($this))->thankYou($request, $response, $args);
})->setName('thankYou');

// Show Single Menu
$app->get('/menu/{date:\d\d-[a-zA-Z]{3}-\d\d}', function ($request, $response, $args) {
    return (new Piton\Controllers\IndexController($this))->showMenu($request, $response, $args);
})->setName('showMenu');

// Show Menu Archive
$app->get('/menu/archive/page[/{page:\d*}]', function ($request, $response, $args) {
    return (new Piton\Controllers\IndexController($this))->showMenuArchive($request, $response, $args);
})->setName('showMenuArchive');

// Show Menu Board
$app->get('/menuboard/{id:[0-9]+}', function ($request, $response, $args) {
    return (new Piton\Controllers\IndexController($this))->showMenuBoard($request, $response, $args);
})->setName('showMenuBoard');

// Supplier detail
$app->get('/supplier/{name}', function ($request, $response, $args) {
    return (new Piton\Controllers\IndexController($this))->showSupplier($request, $response, $args);
})->setName('showSupplier');

// Home page '/home' in case someone tries to load the home tempate (keyword)
$app->get('/home', function ($request, $response, $args) {
    return (new Piton\Controllers\IndexController($this))->homePage($request, $response, $args);
});

// Load dynamic page by /url. Keep as second to last route
$app->get('/{url}', function ($request, $response, $args) {
    return (new Piton\Controllers\IndexController($this))->showPage($request, $response, $args);
})->setName('showPage');

// Home page '/' is always the last route, the default
$app->get('/', function ($request, $response, $args) {
    return (new Piton\Controllers\IndexController($this))->homePage($request, $response, $args);
})->setName('home');

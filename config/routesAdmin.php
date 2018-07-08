<?php
/**
 * Administration Application Routes
 */

//
// Private secured routes
//

$app->group('/admin', function () {

    // Admin home
    $this->get('/home', function ($request, $response, $args) {
        return (new Piton\Controllers\AdminController($this))->home($request, $response, $args);
    })->setName('adminHome');

    // Show Users
    $this->get('/users', function ($request, $response, $args) {
        return (new Piton\Controllers\AdminController($this))->showUsers($request, $response, $args);
    })->setName('showUsers');

    // Save Users
    $this->post('/saveusers', function ($request, $response, $args) {
        return (new Piton\Controllers\AdminController($this))->saveUsers($request, $response, $args);
    })->setName('saveUsers');

    // Remove User
    $this->get('/removeuser/{id:[0-9]{1,}}', function ($request, $response, $args) {
        return (new Piton\Controllers\AdminController($this))->removeUser($request, $response, $args);
    })->setName('removeUser');

    // Show All Pages
    $this->get('/pages', function ($request, $response, $args) {
        return (new Piton\Controllers\AdminPageController($this))->showPages($request, $response, $args);
    })->setName('showPages');

    // Edit Page, or Create Page
    $this->get('/editpage[/{id:[0-9]{0,}}]', function ($request, $response, $args) {
        return (new Piton\Controllers\AdminPageController($this))->editPage($request, $response, $args);
    })->setName('editPage');

    // Save Page
    $this->post('/savepage', function ($request, $response, $args) {
        return (new Piton\Controllers\AdminPageController($this))->savePage($request, $response, $args);
    })->setName('savePage');

    // Delete Page
    $this->get('/deletepage/{id:[0-9]{0,}}', function ($request, $response, $args) {
        return (new Piton\Controllers\AdminPageController($this))->deletePage($request, $response, $args);
    })->setName('deletePage');

    // Edit Pagelet, or Create Pagelet
    $this->get('/editpagelet[/{id:[0-9]{0,}}]', function ($request, $response, $args) {
        return (new Piton\Controllers\AdminPageController($this))->editPagelet($request, $response, $args);
    })->setName('editPagelet');

    // Save Pagelet
    $this->post('/savepagelet', function ($request, $response, $args) {
        return (new Piton\Controllers\AdminPageController($this))->savePagelet($request, $response, $args);
    })->setName('savePagelet');

    // Delete Pagelet
    $this->get('/deletepagelet/{id:[0-9]{0,}}', function ($request, $response, $args) {
        return (new Piton\Controllers\AdminPageController($this))->deletePagelet($request, $response, $args);
    })->setName('deletePagelet');

    // Show list of Menus
    $this->get('/menus', function ($request, $response, $args) {
        return (new Piton\Controllers\AdminMenuController($this))->showMenus($request, $response, $args);
    })->setName('showMenus');

    // Show Single Menu
    $this->get('/menu/{id:[0-9]{0,}}', function ($request, $response, $args) {
        return (new Piton\Controllers\AdminMenuController($this))->showSingleMenu($request, $response, $args);
    })->setName('showSingleMenu');

    // Edit Menu
    $this->get('/editmenu[/{id:[0-9]{0,}}]', function ($request, $response, $args) {
        return (new Piton\Controllers\AdminMenuController($this))->editMenu($request, $response, $args);
    })->setName('editMenu');

    // Copy Edit Menu
    $this->get('/copyeditmenu[/{id:[0-9]{0,}}]', function ($request, $response, $args) {
        return (new Piton\Controllers\AdminMenuController($this))->copyEditMenu($request, $response, $args);
    })->setName('copyEditMenu');

    // Save Menu
    $this->post('/savemenu', function ($request, $response, $args) {
        return (new Piton\Controllers\AdminMenuController($this))->saveMenu($request, $response, $args);
    })->setName('saveMenu');

    // Delete Menu
    $this->get('/deletemenu/{id:[0-9]{0,}}', function ($request, $response, $args) {
        return (new Piton\Controllers\AdminMenuController($this))->deleteMenu($request, $response, $args);
    })->setName('deleteMenu');

    // Set Sold Out Menu Item Flag
    $this->get('/soldoutmenuitem/{id:[0-9]{0,}}', function ($request, $response, $args) {
        return (new Piton\Controllers\AdminMenuController($this))->soldOutMenuItemStatus($request, $response, $args);
    })->setName('soldOutMenuItem');

    // Save all menu item defaults
    $this->post('/savemenuitemdefaults', function ($request, $response, $args) {
        return (new Piton\Controllers\AdminMenuController($this))->saveMenuItemDefaults($request, $response, $args);
    })->setName('saveMenuItemDefaults');
})->add(function ($request, $response, $next) {
    // Authentication
    $security = $this->securityHandler;

    if (!$security->isAuthenticated()) {
        // Failed authentication, redirect away
        $notFound = $this->notFoundHandler;

        return $notFound($request, $response);
    }

    // Next call
    return $next($request, $response);
})->add(function ($request, $response, $next) {
    // Add http header to prevent back button access to admin
    $newResponse = $response->withAddedHeader("Cache-Control", "private, no-cache, no-store, must-revalidate");

    // Next call
    return $next($request, $newResponse);
});

//
// Public unsecured routes
//

// Login page with form to submit email
$app->get('/letmein', function ($request, $response, $args) {
    return (new Piton\Controllers\LoginController($this))->showLoginForm($request, $response, $args);
})->setName('showLoginForm');

// Accept and validate email, and send login token
$app->post('/requestlogintoken/', function ($request, $response, $args) {
    return (new Piton\Controllers\LoginController($this))->requestLoginToken($request, $response, $args);
})->setName('requestLoginToken');

// Accept and validate login token and set session
$app->get('/logintoken/{token:[a-zA-Z0-9]{64}}', function ($request, $response, $args) {
    return (new Piton\Controllers\LoginController($this))->processLoginToken($request, $response, $args);
})->setName('processLoginToken');

// Logout
$app->get('/logout', function ($request, $response, $args) {
    return (new Piton\Controllers\LoginController($this))->logout($request, $response, $args);
})->setName('logout');

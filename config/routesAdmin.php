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

    // User routes
    $this->group('/user', function () {
        // Show Users
        $this->get('[/]', function ($request, $response, $args) {
            return (new Piton\Controllers\AdminController($this))->showUsers($request, $response, $args);
        })->setName('showUsers');

        // Save Users
        $this->post('/save', function ($request, $response, $args) {
            return (new Piton\Controllers\AdminController($this))->saveUsers($request, $response, $args);
        })->setName('saveUsers');

        // Change super user status for admins
        $this->get('/changerole/{role:[A,S]}', function ($request, $response, $args) {
            return (new Piton\Controllers\AdminController($this))->changeUserRole($request, $response, $args);
        })->setName('changeUserRole');

        // Validate user does not exist
        $this->get('/validateemail', function ($request, $response, $args) {
            return (new Piton\Controllers\AdminController($this))->validateUserEmail($request, $response, $args);
        })->setName('validateUserEmail');
    })->add(function ($request, $response, $next) {
        $security = $this->securityHandler;

        if (!$security->isAuthorized('A')) {
            return $response->withRedirect($this->router->pathFor('adminHome'));
        }

        // Next call
        return $next($request, $response);
    });

    // Page routes
    $this->group('/page', function () {
        // Show All Pages
        $this->get('[/]', function ($request, $response, $args) {
            return (new Piton\Controllers\AdminPageController($this))->showPages($request, $response, $args);
        })->setName('showPages');

        // Edit Page, or Create Page
        $this->get('/edit[/{id:[0-9]{0,}}]', function ($request, $response, $args) {
            return (new Piton\Controllers\AdminPageController($this))->editPage($request, $response, $args);
        })->setName('editPage');

        // Save Page
        $this->post('/save', function ($request, $response, $args) {
            return (new Piton\Controllers\AdminPageController($this))->savePage($request, $response, $args);
        })->setName('savePage');

        // Edit Pagelet, or Create Pagelet
        $this->get('/editpagelet[/{id:[0-9]{0,}}]', function ($request, $response, $args) {
            return (new Piton\Controllers\AdminPageController($this))->editPagelet($request, $response, $args);
        })->setName('editPagelet');

        // Save Pagelet
        $this->post('/savepagelet', function ($request, $response, $args) {
            return (new Piton\Controllers\AdminPageController($this))->savePagelet($request, $response, $args);
        })->setName('savePagelet');

        // Delete page routes
        $this->group('/delete', function () {
            // Delete Page
            $this->get('/{id:[0-9]{0,}}', function ($request, $response, $args) {
                return (new Piton\Controllers\AdminPageController($this))->deletePage($request, $response, $args);
            })->setName('deletePage');

            // Delete Pagelet
            $this->get('/pagelet/{id:[0-9]{0,}}', function ($request, $response, $args) {
                return (new Piton\Controllers\AdminPageController($this))->deletePagelet($request, $response, $args);
            })->setName('deletePagelet');
        })->add(function ($request, $response, $next) {
            $security = $this->securityHandler;

            if (!$security->isAuthorized('S')) {
                return $response->withRedirect($this->router->pathFor('adminHome'));
            }

            // Next call
            return $next($request, $response);
        });
    })->add(function ($request, $response, $next) {
        $security = $this->securityHandler;

        if (!$security->isAuthorized('A')) {
            return $response->withRedirect($this->router->pathFor('adminHome'));
        }

        // Next call
        return $next($request, $response);
    });

    // Menu routes
    $this->group('/menu', function () {
        // Show list of Menus
        $this->get('[/]', function ($request, $response, $args) {
            return (new Piton\Controllers\AdminMenuController($this))->showMenus($request, $response, $args);
        })->setName('showMenus');

        // Edit Menu
        $this->get('/edit[/{id:[0-9]{0,}}]', function ($request, $response, $args) {
            return (new Piton\Controllers\AdminMenuController($this))->editMenu($request, $response, $args);
        })->setName('editMenu');

        // Copy Edit Menu
        $this->get('/copy[/{id:[0-9]{0,}}]', function ($request, $response, $args) {
            return (new Piton\Controllers\AdminMenuController($this))->copyEditMenu($request, $response, $args);
        })->setName('copyEditMenu');

        // Save Menu
        $this->post('/save', function ($request, $response, $args) {
            return (new Piton\Controllers\AdminMenuController($this))->saveMenu($request, $response, $args);
        })->setName('saveMenu');

        // Delete Menu
        $this->get('/delete/{id:[0-9]{0,}}', function ($request, $response, $args) {
            return (new Piton\Controllers\AdminMenuController($this))->deleteMenu($request, $response, $args);
        })->setName('deleteMenu');

        // Set Sold Out Menu Item Flag
        $this->get('/soldoutitem/{id:[0-9]{0,}}', function ($request, $response, $args) {
            return (new Piton\Controllers\AdminMenuController($this))->soldOutMenuItemStatus($request, $response, $args);
        })->setName('soldOutMenuItem');

        // Save all menu item defaults
        $this->post('/saveitemdefaults', function ($request, $response, $args) {
            return (new Piton\Controllers\AdminMenuController($this))->saveMenuItemDefaults($request, $response, $args);
        })->setName('saveMenuItemDefaults');
    });

    // Supplier routes
    $this->group('/supplier', function () {
        // Supplier management
        $this->get('[/]', function ($request, $response, $args) {
            return (new Piton\Controllers\AdminSupplierController($this))->editSupplier($request, $response, $args);
        })->setName('supplierHome');

        // Supplier edit
        $this->get('/edit/{id:[0-9]{0,}}', function ($request, $response, $args) {
            return (new Piton\Controllers\AdminSupplierController($this))->editSupplier($request, $response, $args);
        })->setName('editSupplier');

        // Supplier save
        $this->post('/save', function ($request, $response, $args) {
            return (new Piton\Controllers\AdminSupplierController($this))->saveSupplier($request, $response, $args);
        })->setName('saveSupplier');

        // Supplier delete
        $this->get('/delete/{id:[0-9]{0,}}', function ($request, $response, $args) {
            return (new Piton\Controllers\AdminSupplierController($this))->deleteSupplier($request, $response, $args);
        })->setName('deleteSupplier');
    })->add(function ($request, $response, $next) {
        $security = $this->securityHandler;

        if (!$security->isAuthorized('A')) {
            return $response->withRedirect($this->router->pathFor('adminHome'));
        }

        // Next call
        return $next($request, $response);
    });
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

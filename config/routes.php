<?php
/**
 * Application Routes
 */

//
// Public (unsecured) routes
//

/* * Example Route

The '/sampleurl' below is an example of a URL route segment, right after the domain. This can be anything,
but should mean something related to the page and page title for good SEO

The 'home.html' last parameter in the middle line ($this->view->render) is the name of the template to load.
You can create sub-folders in /templates, and if you do just add the folder path to the template name,
as in 'subfolder/home.html'

The last argument 'sampleUrl' is a special name for this route. The ->setName() function is optional, but by giving
it a name, allows us to easily reference this route in <a href="></a> anchors. You use it like this:
<a href="{{ pathFor('sampleUrl') }}">My Anchor</a>

$app->get('/sampleurl', function ($request, $response, $args) {
    return $this->view->render($response, 'home.html');
})->setName('sampleUrl');

*/

// Show Single Menu
$app->get('/menu/{id:[0-9]{0,}}', function ($request, $response, $args) {
    return (new Piton\Controllers\IndexController($this))->showMenu($request, $response, $args);
})->setName('showMenu');

// Load dynamic page by /url. Keep as second to last route
$app->get('/{url}', function ($request, $response, $args) {
    return (new Piton\Controllers\IndexController($this))->showPage($request, $response, $args);
})->setName('showPage');

// Home page '/' is always the last route, the default
$app->get('/', function ($request, $response, $args) {
    return (new Piton\Controllers\IndexController($this))->homePage($request, $response, $args);
})->setName('home');

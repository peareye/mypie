<?php
/**
 * Admin Controller
 */
namespace Piton\Controllers;

class AdminController extends BaseController
{
    /**
     * Admin Home Page
     */
    public function home($request, $response, $args)
    {
        return $this->container->view->render($response, '@admin/home.html');
    }
}

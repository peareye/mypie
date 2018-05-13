<?php
/**
 * Index Controller
 *
 * Primary visitor facing controller
 */
namespace Piton\Controllers;

class IndexController extends BaseController
{
    /**
     * Show Home Page
     *
     */
    public function homePage($request, $response, $args)
    {
        // Get dependencies
        $mapper = $this->container->dataMapper;
        $MenuMapper = $mapper('MenuMapper');
        $MenuItemMapper = $mapper('MenuItemMapper');

        // Assume menus expire end of date of effective date
        // Get the next active menu as of 'now'
        $menu = $MenuMapper->getCurrentActiveMenu();

        // Did we find a menu to display?
        if ($menu->id) {
            $menu->items = $MenuItemMapper->findItemsByMenuId($menu->id);
        }

        $page['menu'] = $menu;

        $this->container->view->render($response, '_home.html', ['page' => $page]);
    }

    /**
     * Show Dynamic Page
     *
     */
    public function showPage($request, $response, $args)
    {
        // Get dependencies
        $mapper = $this->container->dataMapper;
        $PageMapper = $mapper('PageMapper');

        // Fetch pages
        $page = $PageMapper->findPageSetById($args['url']);

        // Send 404 if not found
        if (!$page) {
            return $this->notFound($request, $response);
        }

        // Make sure the .html file extension is there
        $template = preg_replace('/\.html$/i', '', $page->template);
        $template = $template . '.html';

        $this->container->view->render($response, $template, ['page' => $page]);
    }

    /**
     * Show Single Menu
     *
     * @param int menu ID
     *
     */
    public function showMenu($request, $response, $args)
    {
        // Get dependencies
        $mapper = $this->container->dataMapper;
        $MenuMapper = $mapper('MenuMapper');
        $MenuItemMapper = $mapper('MenuItemMapper');

        // Fetch menu header
        $menu = $MenuMapper->findById($args['id']);

        // Send 404 if menu not found
        if (!$menu) {
            return $this->notFound($request, $response);
        }

        // Get menu item details
        $menu->items = $MenuItemMapper->findItemsByMenuId($args['id']);

        $this->container->view->render($response, '_menuSample.html', ['menu' => $menu]);
    }
}

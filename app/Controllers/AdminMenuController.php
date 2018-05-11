<?php
/**
 * Admin Menu Controller
 */
namespace Piton\Controllers;

class AdminMenuController extends BaseController
{
    /**
     * Show Menus
     *
     * Show menu archive
     */
    public function showMenus($request, $response, $args)
    {
        // Get dependencies
        $mapper = $this->container->dataMapper;
        $MenuMapper = $mapper('MenuMapper');

        // Fetch menus
        $menus = $MenuMapper->getRecentMenus();

        return $this->container->view->render($response, '@admin/menuList.html', ['menus' => $menus]);
    }

    /**
     * Show Single Menus
     *
     * Show menu archive
     */
    public function showSingleMenu($request, $response, $args)
    {
        // Get dependencies
        $mapper = $this->container->dataMapper;
        $MenuMapper = $mapper('MenuMapper');
        $MenuItemMapper = $mapper('MenuItemMapper');

        // Fetch menu header
        $menu = $MenuMapper->findById($args['id']);

        if ($menu->id) {
            // Fetch menu iems
            $menu->items = $MenuItemMapper->findItemsByMenuId($args['id']);
        } else {
            $menu->menuNotFound = true;
        }


        return $this->container->view->render($response, '@admin/menu.html', ['menu' => $menu]);
    }

    /**
     * Edit Menu
     *
     * Create new menu
     */
    public function editMenu($request, $response, $args)
    {
        // Get dependencies
        $mapper = $this->container->dataMapper;
        $MenuMapper = $mapper('MenuMapper');
        $MenuItemMapper = $mapper('MenuItemMapper');

        // Fetch menu, or create new menu
        if ($args['id']) {
            $menu = $MenuMapper->findById($args['id']);
            $menu->items = $MenuItemMapper->findItemsByMenuId($args['id']);
        } else {
            $menu = $MenuMapper->make();
        }

        return $this->container->view->render($response, '@admin/editMenu.html', ['menu' => $menu]);
    }

    /**
     * Save Menu
     *
     * Create new menu, or update existing menu
     */
    public function saveMenu($request, $response, $args)
    {
        // Get dependencies
        $mapper = $this->container->dataMapper;
        $MenuMapper = $mapper('MenuMapper');

        // Create menu
        $menu = $MenuMapper->make();
        $menu->id = $request->getParsedBodyParam('id');
        $menu->date = $request->getParsedBodyParam('date');

        // Save
        $menu = $MenuMapper->save($menu);

        // Redirect back to show menu
        return $response->withRedirect($this->container->router->pathFor('editMenuItems', ['id' => $menu->id]));
    }

    /**
     * Delete Menu
     *
     * Delete menu. SQL Foreign Key Constraints cascade to menu item records
     */
    public function deleteMenu($request, $response, $args)
    {
        // Get dependencies
        $mapper = $this->container->dataMapper;
        $MenuMapper = $mapper('MenuMapper');

        // Delete page
        $menu = $MenuMapper->make();
        $menu->id = $args['id'];
        $MenuMapper->delete($menu);

        // Redirect back to show menus
        return $response->withRedirect($this->container->router->pathFor('showMenus'));
    }

    /**
     * Edit Menu Items
     *
     * Create new menu items, or edit
     */
    public function editMenuItems($request, $response, $args)
    {
        // Get dependencies
        $mapper = $this->container->dataMapper;
        $MenuMapper = $mapper('MenuMapper');

        // Get menu date record
        $menu = $MenuMapper->findById($args['id']);

        return $this->container->view->render($response, '@admin/editMenuItems.html', ['menu' => $menu]);
    }

    /**
     * Save Menu Items
     *
     * Create new menu items, or update existing menu items
     */
    public function saveMenuItems($request, $response, $args)
    {
        // Get dependencies
        $mapper = $this->container->dataMapper;
        $MenuItemMapper = $mapper('MenuItemMapper');

        // Capture menu ID to use for all items
        $menuId = $request->getParsedBodyParam('id');
        $items = $request->getParsedBodyParam('items');
// print_r($_POST);die();

        // Loop through items array
// print_r($items['description']);
        foreach ($items['description'] as $key => $row) {
            // Only save if there is at least a description
// echo "0, ";
            if (!empty(trim($row))) {
// echo "1, ";
                // Create menu item object
                $menuItem = $MenuItemMapper->make();
                $menuItem->id = $items['menu_item_id'][$key];
                $menuItem->menu_id = $menuId;
                $menuItem->section = $items['section'][$key];
                $menuItem->type = $items['type'][$key];
                $menuItem->description = $items['description'][$key];
                $menuItem->price = $items['price'][$key];
// print_r($menuItem);
                // Save
                $MenuItemMapper->save($menuItem);
                unset($menuItem);
            }
        }
// die('after save');
        // Redirect back to show menu
        return $response->withRedirect($this->container->router->pathFor('showMenus'));
    }
}

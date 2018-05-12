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
        $menus = $MenuMapper->getMenusInDescDateOrder();

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
     * Create new menu, or edit existing menu
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
     * Save new menu, or update existing menu, along with all menu item records
     */
    public function saveMenu($request, $response, $args)
    {
        // Get dependencies
        $mapper = $this->container->dataMapper;
        $MenuMapper = $mapper('MenuMapper');
        $MenuItemMapper = $mapper('MenuItemMapper');

        // Create menu and get the menu ID, if provided
        $menu = $MenuMapper->make();
        $menu->id = $request->getParsedBodyParam('id');
        $menu->date = $request->getParsedBodyParam('date');
        $menu->location = $request->getParsedBodyParam('location');

        // Was there a date provided? If not, reload page
        if (!$menu->date) {
            // TODO Reload page with validation error
            die('TODO validation no date');
        }

        // Save menu record and get menu ID
        $menu = $MenuMapper->save($menu);

        // Verify id is set
        if (!$menu->id) {
            // TODO Reload page with validation error
            die('TODO validation no menu ID');
        }

        // Get items to save
        $items = $request->getParsedBodyParam('items');

        // Loop through items array
        $sectionSortKey = [];
        foreach ($items['description'] as $key => $row) {
            // Only save if there is at least a description
            if (!empty(trim($row))) {
                // Set sort index by section to keep entered rows in order
                if (!array_key_exists($items['section'][$key], $sectionSortKey)) {
                    $sectionSortKey[$items['section'][$key]] = 1;
                } else {
                    $sectionSortKey[$items['section'][$key]] += 1;
                }

                // Create menu item object
                $menuItem = $MenuItemMapper->make();
                $menuItem->id = $items['menu_item_id'][$key];
                $menuItem->menu_id = $menu->id;
                $menuItem->section = $items['section'][$key];
                $menuItem->sort = $sectionSortKey[$items['section'][$key]];
                $menuItem->type = $items['type'][$key];
                $menuItem->description = $items['description'][$key];
                $menuItem->price = $items['price'][$key];
                $menuItem->sold_out = $items['sold_out'][$key];

                // Save item
                $MenuItemMapper->save($menuItem);
                unset($menuItem);
            }
        }

        // Redirect back to show menu
        return $response->withRedirect($this->container->router->pathFor('showSingleMenu', ['id' => $menu->id]));
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

        // Delete item
        $menu = $MenuMapper->make();
        $menu->id = $args['id'];
        $MenuMapper->delete($menu);

        // Redirect back to show menus
        return $response->withRedirect($this->container->router->pathFor('showMenus'));
    }

    /**
     * Delete Menu Item
     *
     * Delete menu item
     */
    public function deleteMenuItem($request, $response, $args)
    {
        // Get dependencies
        $mapper = $this->container->dataMapper;
        $MenuItemMapper = $mapper('MenuItemMapper');

        // Delete item
        $menuItem = $MenuItemMapper->make();
        $menuItem->id = $args['id'];
        $MenuItemMapper->delete($menuItem);

        if ($request->isXhr()) {
            // Set the response XHR type and return
            $r = $response->withHeader('Content-Type', 'application/json');
            return $r->write(json_encode(['status' => 'success']));
        } else {
            // Redirect back to show menus
            return $response->withRedirect($this->container->router->pathFor('showMenus'));
        }
    }
}

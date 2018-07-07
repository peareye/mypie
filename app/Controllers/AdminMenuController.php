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
        $Pagination = $this->container->get('adminPagination');
        $MenuItemDefaultMapper = $mapper('MenuItemDefaultMapper');

        // Get the page number and setup pagination
        $pageNumber = ($request->getParam('page')) ?: 1;
        $Pagination->setPagePath($this->container->router->pathFor('showMenus'));
        $Pagination->setPaginationTemplateName('includes/_pagination.html');
        $Pagination->setCurrentPageNumber($pageNumber);

        // Fetch menus
        $data['menus'] = $MenuMapper->getMenusInDescDateOrder($Pagination->getRowsPerPage(), $Pagination->getOffset());

        // Get total row count and add extension
        $Pagination->setTotalRowsFound($MenuMapper->foundRows());
        $this->container->view->addExtension($Pagination);

        // Fetch menu defaults
        $data['defaults'] = $MenuItemDefaultMapper->find();

        // If menu default rows were not found, create at least one record
        if (!isset($data['defaults'])) {
            $data['defaults'][] = $MenuItemDefaultMapper->make();
        }

        return $this->container->view->render($response, '@admin/pages/menuList.html', $data);
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

        if (isset($menu->id)) {
            // Fetch menu iems
            $menu->items = $MenuItemMapper->findItemsByMenuId($args['id']);
        }

        return $this->container->view->render($response, '@admin/pages/menu.html', ['menu' => $menu]);
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
        $MenuItemDefaultMapper = $mapper('MenuItemDefaultMapper');

        // Fetch menu, or create new menu
        if (isset($args['id'])) {
            $menu = $MenuMapper->findById($args['id']);
            $menu->items = $MenuItemMapper->findItemsByMenuId($args['id']);
        } else {
            $menu = $MenuMapper->make();
        }

        // Get defaults
        $menu->defaults = $MenuItemDefaultMapper->find();

        return $this->container->view->render($response, '@admin/pages/editMenu.html', ['menu' => $menu]);
    }

    /**
     * Copy Edit Menu
     *
     * Copies and loads existing menu, but unsets date, location, and ID's
     */
    public function copyEditMenu($request, $response, $args)
    {
        // Get dependencies
        $mapper = $this->container->dataMapper;
        $MenuMapper = $mapper('MenuMapper');
        $MenuItemMapper = $mapper('MenuItemMapper');
        $MenuItemDefaultMapper = $mapper('MenuItemDefaultMapper');

        // Fetch menu to copy
        $menu = $MenuMapper->findById($args['id']);
        $menu->items = $MenuItemMapper->findItemsByMenuId($args['id']);

        // Clean up copied menu
        unset($menu->id);
        unset($menu->date);
        unset($menu->location);

        foreach ($menu->items as $key => $row) {
            unset($menu->items[$key]->id);
            unset($menu->items[$key]->menu_id);
            unset($menu->items[$key]->sort);
            $menu->items[$key]->sold_out = 'N';
        }

        // Get defaults
        $menu->defaults = $MenuItemDefaultMapper->find();

        return $this->container->view->render($response, '@admin/pages/editMenu.html', ['menu' => $menu]);
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
        $menu->date = filter_var($request->getParsedBodyParam('date'), FILTER_SANITIZE_STRING);
        $menu->date = date("Y-m-d", strtotime($menu->date));
        $menu->location = filter_var($request->getParsedBodyParam('location'), FILTER_SANITIZE_STRING);

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

        // Redirect back to show menus
        return $response->withRedirect($this->container->router->pathFor('adminHome'));
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
        return $response->withRedirect($this->container->router->pathFor('adminHome'));
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

    /**
     * Sell Out Menu Item Flag
     *
     * Set sold out status flag on item
     */
    public function soldOutMenuItemStatus($request, $response, $args)
    {
        // Get dependencies
        $mapper = $this->container->dataMapper;
        $MenuItemMapper = $mapper('MenuItemMapper');

        // Make item, set status, and update
        $menuItem = $MenuItemMapper->make();
        $menuItem->id = $args['id'];

        if ($status = $request->getQueryParam('status')) {
            if ($status === 'N') {
                $menuItem->sold_out = 'N';
            } elseif ($status === 'Y') {
                $menuItem->sold_out = 'Y';
            }
        }

        $menuItem = $MenuItemMapper->update($menuItem);

        if ($request->isXhr()) {
            // Set the response XHR type and return
            $r = $response->withHeader('Content-Type', 'application/json');
            return $r->write(json_encode(['status' => 'success', 'menuItem' => $menuItem]));
        } else {
            // Redirect back to show menus
            return $response->withRedirect($this->container->router->pathFor('adminHome'));
        }
    }

    /**
     * Save Menu Item Defaults
     *
     */
    public function saveMenuItemDefaults($request, $response, $args)
    {
        // Get dependencies
        $mapper = $this->container->get('dataMapper');
        $MenuItemDefaultMapper = $mapper('MenuItemDefaultMapper');

        // Get items to save
        $defaults = $request->getParsedBodyParam('defaults');

        // Loop through defaults and process rows
        foreach ($defaults as $row) {
            // If delete flag set without an ID, then ignore
            if (isset($row['deletable']) && $row['deletable'] === 'delete' && empty($row['menu_item_default_id'])) {
                continue;
            }

            // Create menu item default object
            $menuItemDefault = $MenuItemDefaultMapper->make();

            // If delete flag is set with an ID, then delete row from database
            if (isset($row['deletable']) && $row['deletable'] === 'delete' && is_numeric($row['menu_item_default_id'])) {
                $menuItemDefault->id = (int) $row['menu_item_default_id'];
                $MenuItemDefaultMapper->delete($menuItemDefault);
                unset($menuItemDefault);
                continue;
            }

            // Only save if there is at least a kind description
            if (!empty(trim($row['kind']))) {
                $menuItemDefault->id = $row['menu_item_default_id'];
                $menuItemDefault->kind = trim($row['kind']);
                $menuItemDefault->price = $row['price'];

                // Save item default
                $MenuItemDefaultMapper->save($menuItemDefault);
                unset($menuItemDefault);
            }
        }

        // Redirect
        return $response->withRedirect($this->container->router->pathFor('showMenus'));
    }
}

<?php
/**
 * Admin Controller
 */
namespace Piton\Controllers;

class AdminController extends BaseController
{
    /**
     * Admin Home Page
     *
     */
    public function home($request, $response, $args)
    {
        // Get dependencies
        $mapper = $this->container->dataMapper;
        $MenuMapper = $mapper('MenuMapper');
        $MenuItemMapper = $mapper('MenuItemMapper');

        // Get most recently updated menu and assign to data array
        $menu = $MenuMapper->getCurrentActiveMenu();

        // Did we find a menu to display?
        if (isset($menu->id)) {
            $menu->items = $MenuItemMapper->findItemsByMenuId($menu->id);
        }

        $page['menu'] = $menu;

        // Get the top most recent menus by date
        $page['menus'] = $MenuMapper->getMenusInDescDateOrder(4);

        return $this->container->view->render($response, '@admin/pages/home.html', $page);
    }

    /**
     * Show All Users
     *
     */
    public function showUsers($request, $response, $args)
    {
        // Get dependencies
        $config = $this->container->settings;
        $mapper = $this->container->dataMapper;
        $UserMapper = $mapper('UserMapper');

        // Get default user account
        $users['default'] = $config['user']['email'];

        // Fetch users
        $userList = $UserMapper->find();

        // Reduce array
        if ($userList) {
            foreach ($userList as $row) {
                $users['other'][] = ['id' => $row->id, 'email' => $row->email];
            }
        }

        return $this->container->view->render($response, '@admin/pages/users.html', ['users' => $users]);
    }

    /**
     * Save Users
     *
     * Save all email addresses, ignoring duplicates
     */
    public function saveUsers($request, $response, $args)
    {
        // Get dependencies
        $mapper = $this->container->dataMapper;
        $UserMapper = $mapper('UserMapper');
        $users = $request->getParsedBodyParam('email');

        // Save users
        foreach ($users as $user) {
            if (!empty($user)) {
                $User = $UserMapper->make();
                $User->email = strtolower(trim($user));
                $UserMapper->save($User);
            }
        }

        // Redirect back to list of users
        return $response->withRedirect($this->container->router->pathFor('showUsers'));
    }

    /**
     * Remove User
     *
     * Remove user email to deny access
     */
    public function removeUser($request, $response, $args)
    {
        // Get dependencies
        $mapper = $this->container->dataMapper;
        $UserMapper = $mapper('UserMapper');

        // Delete user
        $User = $UserMapper->make();
        $User->id = $args['id'];
        $UserMapper->delete($User);

        // Redirect back to list of users
        return $response->withRedirect($this->container->router->pathFor('showUsers'));
    }
}

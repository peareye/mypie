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
        $mapper = $this->container->get('dataMapper');
        $MenuMapper = $mapper('MenuMapper');
        $MenuItemMapper = $mapper('MenuItemMapper');

        // Get today's menus
        $todaysMenus = $MenuMapper->getTodaysMenus();

        // Did we find a menu to display?
        if (is_array($todaysMenus)) {
            foreach ($todaysMenus as $key => $menu) {
                $todaysMenus[$key]->items = $MenuItemMapper->findItemsByMenuId($menu->id);
            }
        }

        $page['todaysMenus'] = $todaysMenus;

        // Get the top most recent menus by date
        $page['newMenus'] = $MenuMapper->getMenusInDescDateOrder(4);

        return $this->container->view->render($response, '@admin/pages/home.html', ['page' => $page]);
    }

    /**
     * Show All Users
     *
     */
    public function showUsers($request, $response, $args)
    {
        // Get dependencies
        $config = $this->container->get('settings');
        $mapper = $this->container->get('dataMapper');
        $UserMapper = $mapper('UserMapper');

        // Get super admin users from config
        $users['superAdmins'] = $config['user']['adminEmail'];

        // Fetch users
        $users['other'] = $UserMapper->find();

        return $this->container->view->render($response, '@admin/pages/users.html', ['page' => $users]);
    }

    /**
     * Save Users
     *
     * Save all email addresses, ignoring duplicates
     */
    public function saveUsers($request, $response, $args)
    {
        // Get dependencies
        $mapper = $this->container->get('dataMapper');
        $UserMapper = $mapper('UserMapper');
        $allUsers = $request->getParsedBodyParam('user');

        // Loop through users and process rows
        foreach ($allUsers as $row) {
            // If delete flag set without an ID, then ignore
            if (isset($row['deletable']) && $row['deletable'] === 'delete' && empty($row['id'])) {
                continue;
            }

            // Create user object
            $user = $UserMapper->make();

            // If delete flag is set with an ID, then delete row from database
            if (isset($row['deletable']) && $row['deletable'] === 'delete' && is_numeric($row['id'])) {
                // Do not delete record #1 to avoid locking out of the application
                if ($row['id'] == 1) {
                    continue;
                }

                $user->id = (int) $row['id'];
                $UserMapper->delete($user);
                unset($user);
                continue;
            }


            // Save row if there is an email
            if (!empty(trim($row['email']))) {
                $user->id = $row['id'];
                $user->email = strtolower(trim($row['email']));
                $user->admin = isset($row['admin']) ? 'Y' : 'N';

                // Keep user #1 as admin
                if ($row['id'] == 1) {
                    $user->admin = 'Y';
                }

                // Save
                $UserMapper->save($user);
                unset($user);
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

    /**
     * Change User Role
     *
     * Elevate or demote user role in session
     */
    public function changeUserRole($request, $response, $args)
    {
        // Dependencies
        $session = $this->container->get('sessionHandler');

        // Get current role, should be at least an Admin
        $currentRole = $session->getData('role');

        if (!($currentRole === 'A' || $currentRole === 'S')) {
            return $response->withRedirect($this->container->router->pathFor('showUsers'));
        }

        // Request should also be only A or S
        if (isset($args['role']) && !($args['role'] === 'A' || $args['role'] === 'S')) {
            return $response->withRedirect($this->container->router->pathFor('showUsers'));
        }

        $session->setData('role', $args['role']);
        return $response->withRedirect($this->container->router->pathFor('showUsers'));
    }
}

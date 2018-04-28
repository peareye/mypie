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
        return $this->container->view->render($response, '@admin/home.html');
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
                $users['other'][] = $row['email'];
            }
        }

        return $this->container->view->render($response, '@admin/users.html', ['users' => $users]);
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
                $UserObj = $UserMapper->make();
                $UserObj->email = strtolower(trim($user));
                $UserMapper->save($UserObj);
            }
        }

        // Redirect back to list of users
        return $response->withRedirect($this->container->router->pathFor('showUsers'));
    }
}

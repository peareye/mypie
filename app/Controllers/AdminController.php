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
                $users['other'][] = ['id' => $row['id'], 'email' => $row['email']];
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

    /**
     * Show Pages
     *
     * Show page header records
     */
    public function showPages($request, $response, $args)
    {
        // Get dependencies
        $mapper = $this->container->dataMapper;
        $Page = $mapper('PageMapper');

        // Fetch pages
        $pages = $Page->find();

        return $this->container->view->render($response, '@admin/pages.html', ['pages' => $pages]);
    }

    /**
     * Edit Page
     *
     * Create new page, or edit existing page
     */
    public function editPage($request, $response, $args)
    {
        // Get dependencies
        $mapper = $this->container->dataMapper;
        $Page = $mapper('PageMapper');

        // Fetch page, or create blank array
        if ($args['id']) {
            $page = $Page->findById($args['id']);
        } else {
            $page = [];
        }

        return $this->container->view->render($response, '@admin/editpage.html', ['page' => $page]);
    }

    /**
     * Save Page
     *
     * Create new page, or update existing page
     */
    public function savePage($request, $response, $args)
    {
        // Get dependencies
        $mapper = $this->container->dataMapper;
        $PageMapper = $mapper('PageMapper');

        // Create page
        $page = $PageMapper->make();
        $page->id = $request->getParsedBodyParam('id');
        $page->title = $request->getParsedBodyParam('title');
        $page->url = $request->getParsedBodyParam('url');
        $page->meta_description = $request->getParsedBodyParam('meta_description');

        // Save
        $page = $PageMapper->save($page);

        // Redirect back to show page
        return $response->withRedirect($this->container->router->pathFor('editPage', ['id' => $page->id]));
    }
}

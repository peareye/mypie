<?php
/**
 * Admin Page Controller
 */
namespace Piton\Controllers;

class AdminPageController extends BaseController
{
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

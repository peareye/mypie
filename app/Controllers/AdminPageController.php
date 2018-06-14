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
     * Show pages with child pagelets
     */
    public function showPages($request, $response, $args)
    {
        // Get dependencies
        $mapper = $this->container->dataMapper;
        $PageMapper = $mapper('PageMapper');
        $PageletMapper = $mapper('PageletMapper');

        // Fetch pages
        $pages = $PageMapper->find();

        // If we found pages, then loop through to get pagelets
        if ($pages) {
            foreach ($pages as $key => $row) {
                $pages[$key]->pagelets = $this->indexPageletKeys($PageletMapper->findPageletsByPageId($row->id));
            }
        }

        return $this->container->view->render($response, '@admin/pages/pages.html', ['pages' => $pages]);
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
        $PageMapper = $mapper('PageMapper');

        // Fetch page, or create blank array
        if ($args['id']) {
            $page = $PageMapper->findById($args['id']);
        } else {
            $page = $PageMapper->make();
        }

        return $this->container->view->render($response, '@admin/pages/editPage.html', ['page' => $page]);
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
        $page->template = $request->getParsedBodyParam('template');
        $page->meta_description = $request->getParsedBodyParam('meta_description');

        // Prep URL
        $page->url = strtolower(trim($request->getParsedBodyParam('url')));
        $page->url = preg_replace('/[^a-z0-9\s-]/', '', $page->url);
        $page->url = preg_replace('/[\s-]+/', ' ', $page->url);
        $page->url = preg_replace('/[\s]/', '-', $page->url);

        // Save
        $page = $PageMapper->save($page);

        // Redirect back to show page
        return $response->withRedirect($this->container->router->pathFor('showPages'));
    }

    /**
     * Delete Page
     *
     * Delete page. SQL Foreign Key Constraints cascade to pagelet records
     */
    public function deletePage($request, $response, $args)
    {
        // Get dependencies
        $mapper = $this->container->dataMapper;
        $PageMapper = $mapper('PageMapper');

        // Delete page
        $page = $PageMapper->make();
        $page->id = $args['id'];
        $page = $PageMapper->delete($page);

        // Redirect back to show pages
        return $response->withRedirect($this->container->router->pathFor('showPages'));
    }

    /**
     * Edit Pagelet Content
     *
     * Create new pagelet, or edit existing pagelet
     * Query by pagelet.id, or start new content by passing in the page_id
     */
    public function editPagelet($request, $response, $args)
    {
        // Get dependencies
        $mapper = $this->container->dataMapper;
        $PageletMapper = $mapper('PageletMapper');

        // Fetch page, or create blank array
        if ($args['id']) {
            $pagelet = $PageletMapper->findById($args['id']);
        } else {
            $pagelet = $PageletMapper->make();
        }

        // Pass in page ID if missing (new pagelet content)
        if (empty($pagelet->page_id)) {
            $pagelet->page_id = $request->getQueryParam('page_id');
        }

        return $this->container->view->render($response, '@admin/pages/editPagelet.html', ['pagelet' => $pagelet]);
    }

    /**
     * Save Pagelet Content
     *
     * Create new page, or update existing page
     */
    public function savePagelet($request, $response, $args)
    {
        // Get dependencies
        $mapper = $this->container->dataMapper;
        $PageletMapper = $mapper('PageletMapper');
        $markdown = $this->container->markdownParser;

        // Create page
        $page = $PageletMapper->make();
        $page->id = $request->getParsedBodyParam('id');
        $page->page_id = $request->getParsedBodyParam('page_id');
        $page->name = $request->getParsedBodyParam('name');
        $page->content = $request->getParsedBodyParam('content');
        $page->content_html = $markdown->text($request->getParsedBodyParam('content'));

        // Save
        $page = $PageletMapper->save($page);

        // Redirect back to show page
        return $response->withRedirect($this->container->router->pathFor('showPages'));
    }

    /**
     * Delete Pagelet
     *
     * Delete pagelet
     */
    public function deletePagelet($request, $response, $args)
    {
        // Get dependencies
        $mapper = $this->container->dataMapper;
        $PageletMapper = $mapper('PageletMapper');

        // Delete pagelet
        $pagelet = $PageletMapper->make();
        $pagelet->id = $args['id'];
        $pagelet = $PageletMapper->delete($pagelet);

        // Redirect back to show pages
        return $response->withRedirect($this->container->router->pathFor('showPages'));
    }
}

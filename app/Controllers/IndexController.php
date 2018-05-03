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
}

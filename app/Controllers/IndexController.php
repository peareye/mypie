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
     *
     */
    public function showPage($request, $response, $args)
    {
        // Get dependencies
        $mapper = $this->container->dataMapper;
        $PageMapper = $mapper('PageMapper');

        // Fetch pages
        $page = $PageMapper->findPageSetById($args['url']);
        $template = (isset($page['template'])) ? $page['template'] : 'home';
        $template .= '.html';

        $this->container->view->render($response, $template, ['page' => $page]);
    }
}

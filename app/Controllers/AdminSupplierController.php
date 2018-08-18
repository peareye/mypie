<?php
/**
 * Admin Supplier Controller
 */
namespace Piton\Controllers;

class AdminSupplierController extends BaseController
{
    /**
     * Edit Supplier
     *
     * View Suppliers, create new supplier, or edit existing supplier
     */
    public function editSupplier($request, $response, $args)
    {
        // Get dependencies
        $mapper = $this->container->dataMapper;
        $SupplierMapper = $mapper('SupplierMapper');

        // Fetch supplier, or create empty object
        if (isset($args['id'])) {
            $page['supplier'] = $SupplierMapper->findById($args['id']);
        } else {
            $page['supplier'] = $SupplierMapper->make();
        }

        // TODO Fetch supplier records for sidebar

        return $this->container->view->render($response, '@admin/pages/suppliers.html', ['page' => $page]);
    }

    /**
     * Save Supplier
     *
     * Create new supplier, or update existing supplier
     */
    public function saveSupplier($request, $response, $args)
    {
        // Get dependencies
        $mapper = $this->container->dataMapper;
        $SupplierMapper = $mapper('SupplierMapper');
        $markdown = $this->container->markdownParser;

        // Create page
        $supplier = $SupplierMapper->make();
        $supplier->id = $request->getParsedBodyParam('id');
        $supplier->name = $request->getParsedBodyParam('name');
        $supplier->phone = $request->getParsedBodyParam('phone');
        $supplier->content = $request->getParsedBodyParam('content');
        $supplier->content_html = $markdown->text($request->getParsedBodyParam('content_html'));
        $supplier->logo = $request->getParsedBodyParam('logo');
        $supplier->published = $request->getParsedBodyParam('published');

        // Prep URL
        $supplier->url = strtolower(trim($request->getParsedBodyParam('url')));
        $supplier->url = preg_replace('/[^a-z0-9\s-]/', '', $supplier->url);
        $supplier->url = preg_replace('/[\s-]+/', ' ', $supplier->url);
        $supplier->url = preg_replace('/[\s]/', '-', $supplier->url);

        // Save
        $supplier = $SupplierMapper->save($supplier);

        // Redirect
        return $response->withRedirect($this->container->router->pathFor('supplierHome'));
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
}

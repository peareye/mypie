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

        // Fetch supplier records for sidebar
        $page['supplierList'] = $SupplierMapper->findSuppliersInDescDateOrder();

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
        $supplier->supplier_url = $request->getParsedBodyParam('supplier_url');
        $supplier->phone = $request->getParsedBodyParam('phone');
        $supplier->content = $request->getParsedBodyParam('content');
        $supplier->content_html = $markdown->text($request->getParsedBodyParam('content'));
        $supplier->logo = $request->getParsedBodyParam('logo');
        // $supplier->published = ($request->getParsedBodyParam('published')) ? 'Y' : 'N';

        // Prep supplier URL based on supplier name
        $supplier->url = strtolower(trim($request->getParsedBodyParam('name')));
        $supplier->url = preg_replace('/[^a-z0-9\s-]/', '', $supplier->url);
        $supplier->url = preg_replace('/[\s-]+/', ' ', $supplier->url);
        $supplier->url = preg_replace('/[\s]/', '-', $supplier->url);

        // Save
        $supplier = $SupplierMapper->save($supplier);

        // Redirect
        return $response->withRedirect($this->container->router->pathFor('supplierHome'));
    }

    /**
     * Delete Supplier
     *
     * Delete supplier record
     */
    public function deleteSupplier($request, $response, $args)
    {
        // Get dependencies
        $mapper = $this->container->dataMapper;
        $SupplierMapper = $mapper('SupplierMapper');

        // Delete page
        $supplier = $SupplierMapper->make();
        $supplier->id = $args['id'];
        $SupplierMapper->delete($supplier);

        // Redirect
        return $response->withRedirect($this->container->router->pathFor('supplierHome'));
    }

    /**
     * Publish / Unpublish Supplier
     *
     * Toggle published flag via Ajax for suppliers
     */
    public function toggleSupplierPublishedFlag($request, $response, $args)
    {
        // Get dependencies
        $mapper = $this->container->dataMapper;
        $SupplierMapper = $mapper('SupplierMapper');

        $supplier = $SupplierMapper->make();
        $supplier->id = $args['id'];
        $supplier->published = $args['flag'];
        $supplier = $SupplierMapper->save($supplier);

        if ($request->isXhr()) {
            // Set the response XHR type and return
            $r = $response->withHeader('Content-Type', 'application/json');
            return $r->write(json_encode(['status' => 'success', 'publishedStatus' => $supplier->published]));
        } else {
            // Redirect
            return $response->withRedirect($this->container->router->pathFor('supplierHome'));
        }
    }
}

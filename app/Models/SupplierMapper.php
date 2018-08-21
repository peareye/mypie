<?php
/**
 * Supplier Mapper
 */
namespace Piton\Models;

class SupplierMapper extends DataMapperAbstract
{
    protected $table = 'supplier';
    protected $modifiableColumns = ['name', 'url', 'supplier_url', 'phone', 'content', 'content_html', 'logo', 'published'];


    /**
     * Find Suppliers
     *
     * Gets suppliers in date created descending order
     * @param none
     * @return mixed
     */
    public function findSuppliersInDescDateOrder()
    {
        $this->makeSelect();
        $this->sql .= ' order by created_date desc';

        return $this->find();
    }

    /**
     * Find Published Suppliers
     *
     * @param none
     * @return mixed
     */
    public function findPublishedSuppliers()
    {
        $this->makeSelect();
        $this->sql .= ' where published = \'Y\' order by name';

        return $this->find();
    }

    /**
     * Find Named Supplier by URL
     *
     * @param str URL
     * @return mixed
     */
    public function findSupplierByName($url)
    {
        $this->makeSelect();
        $this->sql .= ' where url = ?';
        $this->bindValues[] = $url;

        return $this->findRow();
    }
}

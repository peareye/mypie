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
}

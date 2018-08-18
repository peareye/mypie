<?php
/**
 * Supplier Mapper
 */
namespace Piton\Models;

class SupplierMapper extends DataMapperAbstract
{
    protected $table = 'supplier';
    protected $modifiableColumns = ['name', 'url', 'phone', 'content', 'content_html', 'logo', 'published'];
}

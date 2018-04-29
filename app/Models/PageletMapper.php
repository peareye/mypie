<?php
/**
 * Pagelet Mapper
 */
namespace Piton\Models;

class PageletMapper extends DataMapperAbstract
{
    protected $table = 'pagelet';
    protected $modifiableColumns = ['page_id', 'name', 'content', 'content_html'];
}

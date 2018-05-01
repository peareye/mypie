<?php
/**
 * Pagelet Mapper
 */
namespace Piton\Models;

class PageletMapper extends DataMapperAbstract
{
    protected $table = 'pagelet';
    protected $modifiableColumns = ['page_id', 'name', 'content', 'content_html'];

    /**
     * Get Pagelet by ID
     *
     * Returns part of the page header record
     * @param int, pagelet ID
     */
    public function findById($pageletId)
    {
        $this->sql = 'select p.title, p.url, pl.* from page p join pagelet pl on p.id = pl.page_id where pl.id = ?';
        $this->bindValues[] = $pageletId;

        return $this->findRow();
    }
}

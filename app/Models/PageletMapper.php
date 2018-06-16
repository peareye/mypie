<?php
/**
 * Pagelet Mapper
 */
namespace Piton\Models;

class PageletMapper extends DataMapperAbstract
{
    protected $table = 'pagelet';
    protected $modifiableColumns = ['page_id', 'title', 'name', 'content', 'content_html'];

    /**
     * Get Pagelets by Page ID
     *
     * Returns array of domain objects
     * @param int Page ID
     * @return array
     */
    public function findPageletsByPageId($id)
    {
        $this->makeSelect();
        $this->sql .= ' where page_id = ?';
        $this->bindValues[] = $id;

        return $this->find();
    }
}

<?php
/**
 * Menu Mapper
 */
namespace Piton\Models;

class MenuMapper extends DataMapperAbstract
{
    protected $table = 'menu';
    protected $modifiableColumns = ['date'];

    /**
     * Get Menus in Descending Ordery by Date
     *
     * Returns an array of Domain Objects (one for each record)
     * @return Array
     */
    public function getRecentMenus()
    {
        // Make select
        $this->makeSelect();
        $this->sql .= ' order by date desc';

        return $this->find();
    }
}

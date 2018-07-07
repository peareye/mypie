<?php
/**
 * Menu Item Default Mapper
 *
 * For basic key-value records
 */
namespace Piton\Models;

class MenuItemDefaultMapper extends DataMapperAbstract
{
    protected $table = 'menu_item_default';
    protected $modifiableColumns = ['kind', 'price'];
    protected $who = false;

    /**
     * Get Table Rows
     *
     * Returns all table rows, sorted by kind
     *
     * Returns an array of Domain Objects (one for each record)
     * @return Array
     */
    public function find()
    {
        $this->makeSelect();
        $this->sql .= ' order by kind';

        return parent::find();
    }
}

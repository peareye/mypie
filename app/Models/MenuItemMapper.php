<?php
/**
 * Menu Items Mapper
 */
namespace Piton\Models;

class MenuItemMapper extends DataMapperAbstract
{
    protected $table = 'menu_item';
    protected $modifiableColumns = ['menu_id', 'section', 'sort', 'type', 'description', 'price', 'sold_out'];

    /**
     * Get Menus Items
     *
     * Returns an array of menu item objects
     * @param int menu_id
     * @return Array
     */
    public function findItemsByMenuId($id)
    {
        // Make select
        $this->makeSelect();
        $this->sql .= ' where menu_id = ? order by section, sort;';
        $this->bindValues[] = $id;

        return $this->find();
    }
}

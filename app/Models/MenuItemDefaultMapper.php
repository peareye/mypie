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
}

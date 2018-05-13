<?php
/**
 * Menu Mapper
 */
namespace Piton\Models;

class MenuMapper extends DataMapperAbstract
{
    protected $table = 'menu';
    protected $modifiableColumns = ['date', 'location'];

    /**
     * Get Active Menu By Date
     *
     * Returns the current menu (menu.date >= now)
     * @return Obj
     */
    public function getCurrentActiveMenu()
    {
        // Make select
        $this->makeSelect();
        $this->sql .= ' where date >= ? order by date limit 1';
        $this->bindValues[] = date('Y-m-d');

        return $this->findRow();
    }

    /**
     * Get Menus in Descending Ordery by Date
     *
     * Returns an array of Domain Objects (one for each record)
     * @return Array
     */
    public function getMenusInDescDateOrder()
    {
        // Make select
        $this->makeSelect();
        $this->sql .= ' order by date desc';

        return $this->find();
    }

    /**
     * Get Menus For This and Next Month
     *
     * Returns an array of Domain Objects
     * @return Array
     */
    public function getMenusForThisAndNextMonth()
    {
        // Make select
        $this->makeSelect();
        $this->sql .= ' where date >= ?';

        // Set parameter
        $startDate = new \DateTime('first day of this month 00:00:00');
        $fromDate = $startDate->format('Y-m-1');
        $this->bindValues[] = $fromDate;

        return $this->find();
    }

    /**
     * Get Last Menus Entered
     *
     * Returns the last menu entered with item details
     * @return Obj
     */
    public function getLastMenu()
    {
        // Make select
        $this->makeSelect();
        $this->sql .= ' order by updated_date desc limit 1';

        $menu = $this->findRow();

        if (!$menu) {
            return null;
        }

        // TODO: Need the DIC passed in
        $MenuItemMapper = new \Piton\Models\MenuItemMapper(self::$dbh, self::$logger);
        $menu->items = $MenuItemMapper->findItemsByMenuId($menu->id);

        return $menu;
    }
}

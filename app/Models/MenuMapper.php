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
     * Get Future Menus as of This Month
     *
     * Returns an array of Domain Objects
     * @return Array
     */
    public function getFutureMenusStartingThisMonth()
    {
        // Make select
        $this->makeSelect();
        $this->sql .= ' where date >= ?';

        // Set parameter
        $startDate = new \DateTime('first day of this month 00:00:00');
        $this->bindValues[] = $startDate->format('Y-m-1');

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

    /**
     * Get Menu By Date
     *
     * Finds menu given dd-mmm-yy format
     * @param str dd-mmm-yy date
     * @return mixed
     */
    public function getMenuByDate($dateUrl)
    {
        // Format input argument as valid date
        $menuDate = \DateTime::createFromFormat('d-M-y', $dateUrl);

        // Make sure we have a valid date conversion
        if (!$menuDate) {
            return null;
        }

        // Prepare query
        $this->makeSelect();
        $this->sql .= ' where date = ?';
        $this->bindValues[] = $menuDate->format('Y-m-d');

        // Return data
        return $this->findRow();
    }
}

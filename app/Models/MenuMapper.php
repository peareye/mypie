<?php
/**
 * Menu Mapper
 */
namespace Piton\Models;

class MenuMapper extends DataMapperAbstract
{
    protected $table = 'menu';
    protected $modifiableColumns = ['date', 'location', 'pinned'];

    /**
     * Get Current Menus By Date
     *
     * Returns current menus (menu.date >= today) that DO NOT contain a location
     * @return Obj
     */
    public function getCurrentActiveMenus()
    {
        // Make select
        $this->makeSelect();
        $this->sql .= ' where `date` >= ? and `location` is not null and `pinned` is null order by `date`';
        $this->bindValues[] = date('Y-m-d');

        return $this->find();
    }

    /**
     * Get Todays Menus
     *
     * Returns menus with today's date
     * @param none
     * @return array
     */
    public function getTodaysMenus()
    {
        // Make select
        $this->makeSelect();
        $this->sql .= ' where `date` = ? and `pinned` is null order by `location`';
        $this->bindValues[] = date('Y-m-d');

        return $this->find();
    }

    /**
     * Get Pinned Menus
     *
     * Returns pinned menus regardless of today's date
     * @param none
     * @return array
     */
    public function getPinnedMenus()
    {
        // Make select
        $this->makeSelect();
        $this->sql .= ' where `pinned` = \'Y\' order by `date`';

        return $this->find();
    }

    /**
     * Get Menus in Descending Ordery by Date
     *
     * Returns an array of Domain Objects (one for each record)
     * @param int $limit Limit
     * @param int $offset Offset
     * @return Array
     */
    public function getMenusInDescDateOrder($limit = null, $offset = null)
    {
        // Make select
        $this->makeSelect();
        $this->sql .= ' where `pinned` is null order by `date` desc, `location`';

        if ($limit) {
            $this->sql .= " limit ?";
            $this->bindValues[] = $limit;
        }

        if ($offset) {
            $this->sql .= " offset ?";
            $this->bindValues[] = $offset;
        }

        return $this->find();
    }

    /**
     * Get Past Menus in Descending Ordery by Date
     *
     * Returns an array of Domain Objects (one for each record)
     * @param int $limit Limit
     * @param int $offset Offset
     * @return Array
     */
    public function getPastMenusInDescDateOrder($limit = null, $offset = null)
    {
        // Make select
        $this->makeSelect();
        $this->sql .= ' where `date` < ? and `location` is not null and `pinned` is null order by `date` desc';
        $this->bindValues[] = date('Y-m-d');

        if ($limit) {
            $this->sql .= " limit ?";
            $this->bindValues[] = $limit;
        }

        if ($offset) {
            $this->sql .= " offset ?";
            $this->bindValues[] = $offset;
        }

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
        $this->sql .= ' where `date` >= ? and `pinned` is null';

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
        $this->sql .= ' where `pinned` is null order by `updated_date` desc limit 1';

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
        $this->sql .= ' where `date` = ? and `pinned` is null';
        $this->bindValues[] = $menuDate->format('Y-m-d');

        // Return data
        return $this->findRow();
    }
}

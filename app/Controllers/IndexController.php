<?php
/**
 * Index Controller
 *
 * Primary visitor facing controller
 */
namespace Piton\Controllers;

class IndexController extends BaseController
{
    // Calendar Array
    public $calendar = [];

    /**
     * Show Home Page
     *
     */
    public function homePage($request, $response, $args)
    {
        // Get dependencies
        $mapper = $this->container->dataMapper;
        $MenuMapper = $mapper('MenuMapper');
        $PageMapper = $mapper('PageMapper');
        $PageletMapper = $mapper('PageletMapper');

        // Fetch pages and add pagelet content
        $page = $PageMapper->findPageByUrl('home');

        // Verify we have an object, if not create one
        if (!is_object($page)) {
            $page = $PageMapper->make();
        }

        if (isset($page->id)) {
            $page->pagelets = $this->indexPageletKeys($PageletMapper->findPageletsByPageId($page->id));
        }

        // Make calendar and merge in future menu dates
        $this->populateCalendar();
        $menus = $MenuMapper->getFutureMenusStartingThisMonth();
        $this->mergeMenuDatesIntoCalendar($menus);

        // Add calendar to page content
        $page->calendar = $this->calendar;

        $this->container->view->render($response, '_home.html', ['page' => $page]);
    }

    /**
     * Show Dynamic Page
     *
     */
    public function showPage($request, $response, $args)
    {
        // Get dependencies
        $mapper = $this->container->dataMapper;
        $PageMapper = $mapper('PageMapper');
        $PageletMapper = $mapper('PageletMapper');

        // Fetch pages
        $page = $PageMapper->findPageByUrl($args['url']);

        // Verify we have an object, if not create one
        if (!is_object($page)) {
            $page = $PageMapper->make();
        }

        // Send 404 if not found
        if (!isset($page->id)) {
            return $this->notFound($request, $response);
        }

        // Add pagelet content
        $page->pagelets = $this->indexPageletKeys($PageletMapper->findPageletsByPageId($page->id));

        // Make sure the .html file extension is there
        $template = preg_replace('/\.html$/i', '', $page->template);
        $template = $template . '.html';

        $this->container->view->render($response, $template, ['page' => $page]);
    }

    /**
     * Show Single Menu
     *
     * @param int menu ID
     *
     */
    public function showMenu($request, $response, $args)
    {
        // Get dependencies
        $mapper = $this->container->dataMapper;
        $MenuMapper = $mapper('MenuMapper');
        $MenuItemMapper = $mapper('MenuItemMapper');

        // Fetch menu header
        $menu = $MenuMapper->getMenuByDate($args['date']);

        // Verify we have an object, if not create one
        if (!is_object($menu)) {
            $menu = $MenuMapper->make();
        }

        // Send 404 if menu not found
        if (!isset($menu->id)) {
            return $this->notFound($request, $response);
        }

        // Get menu item details
        $menu->items = $MenuItemMapper->findItemsByMenuId($menu->id);

        $this->container->view->render($response, '_menuSample.html', ['menu' => $menu]);
    }

    /**
     * Show Menu Archive
     *
     * @param none
     *
     */
    public function showMenuArchive($request, $response, $args)
    {
        // Get dependencies
        $mapper = $this->container->get('dataMapper');
        $MenuMapper = $mapper('MenuMapper');
        $MenuItemMapper = $mapper('MenuItemMapper');
        $Pagination = $this->container->get('menuPagination');
        $Pagination->useQueryString = false;

        // Get the page number and setup pagination
        $pageNumber = ($args['page']) ?: 1;
        $Pagination->setPagePath($this->container->router->pathFor('showMenuArchive'));
        $Pagination->setPaginationTemplateName('includes/_pagination.html');
        $Pagination->setCurrentPageNumber($pageNumber);

        // Fetch past menu headers
        $menus = $MenuMapper->getPastMenusInDescDateOrder($Pagination->getRowsPerPage(), $Pagination->getOffset());

        // Get total row count and add extension
        $Pagination->setTotalRowsFound($MenuMapper->foundRows());
        $this->container->view->addExtension($Pagination);

        if (is_array($menus)) {
            foreach ($menus as $key => $row) {
                // Verify we have an menu object before fetching items
                if (isset($menus[$key]->id)) {
                    // Get menu item details
                    $menus[$key]->items = $MenuItemMapper->findItemsByMenuId($menus[$key]->id);
                }
            }
        }

        $page['pageNumber'] = $pageNumber;
        $page['menuList'] = $menus;

        return $this->container->view->render($response, 'pages/_menuArchive.html', ['page' => $page]);
    }

    /**
     * Show Menu Board
     *
     * @param int menu ID
     *
     */
    public function showMenuBoard($request, $response, $args)
    {
        // Get dependencies
        $mapper = $this->container->dataMapper;
        $MenuMapper = $mapper('MenuMapper');
        $MenuItemMapper = $mapper('MenuItemMapper');

        // Fetch menu header
        $menu = $MenuMapper->findById($args['id']);

        // Get menu item details
        if (isset($menu->id)) {
            $menu->items = $MenuItemMapper->findItemsByMenuId($menu->id);
        }

        $this->container->view->render($response, 'pages/_menu-feed.html', ['menu' => $menu]);
    }

    /**
     * Merge Menu Dates into Calendar
     *
     * Builds Calendar and Merges in Menu Dates
     * @param array Array of menu objects
     */
    protected function mergeMenuDatesIntoCalendar($menuDates)
    {
        // Merge menus, start by looping this/next months then days
        foreach ($this->calendar as $monthKey => $row) {
            foreach ($this->calendar[$monthKey]['days'] as $dateKey => $day) {
                // Initialize daily content array
                $this->calendar[$monthKey]['days'][$dateKey]['content'] = [];
                // Find matching menu dates
                foreach ($menuDates as $menu) {
                    if ($menu->date === $day['canonicalDate'] && !empty($menu->location)) {
                        $this->calendar[$monthKey]['days'][$dateKey]['content'][] = $menu->location;
                    }
                }
            }
        }

        return;
    }

    /**
     * Populate Calendar Array
     *
     * Populates two full month calendar arrays, including leading/trailing dates
     */
    protected function populateCalendar()
    {
        // Loop through two months for two calendars
        for ($i=0; $i < 2; $i++) {
            // Start with month
            $month = new \DateTime('first day of this month 00:00:00');

            // Increment month counter with each loop
            if ($i > 0) {
                $month->modify('+' . $i . ' months');
            }

            // Start with the last monday in the prior month to start filling full calendar block
            $startDate = clone $month;

            // Test if the first day of this month falls on a Monday, in which case we do not need an extra row
            // by extending into the prior month
            if ($startDate->format('D') !== 'Mon') {
                $startDate->modify('last monday of previous month');
            }

            // Get the first Sunday of the next month to close our calendar block
            $endDate = clone $month;

            // Test if the last day of this month falls on a Sunday, in which case we do not need an extra row
            // by extending into next month
            $endDate->modify('last day of this month');
            if ($endDate->format('D') !== 'Sun') {
                $endDate->modify('first sunday of next month');
            }

            $endDate->modify('+1 day');

            // Set interval to 'day' and get iterable date period object
            $dayInterval = new \DateInterval('P1D');
            $dateRange = new \DatePeriod($startDate, $dayInterval, $endDate);

            // Set month date for use in calendar label
            $this->calendar[$i]['month'] = $month->format('Y-m-1');

            // Loop iterator to build array
            $today = date('Y-m-d');
            foreach ($dateRange as $date) {
                // Our range extends beyond the current month, so we need a flag to know when we are
                // iterating within the contiguous month
                $inMonth = ($month->format('Y-m') === $date->format('Y-m')) ? true : false;

                // Assign out-of-month class, and today class, if appropriate
                $dateBoxClass = '';
                if (!$inMonth) {
                    $dateBoxClass = 'prev-month';
                }
                if ($date->format('Y-m-d') === $today) {
                    $dateBoxClass = 'today';
                }

                $this->calendar[$i]['days'][] = [
                    'canonicalDate' => $date->format('Y-m-d'),
                    'dateBoxClass' => $dateBoxClass,
                    'dateClass' => ($inMonth) ? 'calendar-day' : '',
                    'date' => $date->format('j'),
                    'content' => '',
                ];
            }
        }

        return;
    }


    /**
     * Display Supplier Detail
     *
     */
    public function showSupplier($request, $response, $args)
    {
        // Get dependencies
        $mapper = $this->container->dataMapper;
        $SupplierMapper = $mapper('SupplierMapper');

        $page['supplier'] = $SupplierMapper->findSupplierByName($args['name']);

        if (!isset($page['supplier']->id)) {
            return $this->notFound($request, $response);
        }

        $this->container->view->render($response, 'pages/_supplier.html', ['page' => $page]);
    }
}

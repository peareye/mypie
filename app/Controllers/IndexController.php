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
        $MenuItemMapper = $mapper('MenuItemMapper');
        $PageMapper = $mapper('PageMapper');
        $PageLetMapper = $mapper('PageLetMapper');

        // Fetch pages and add pagelet content
        $page = $PageMapper->findPageByUrl('home');

        if ($page->id) {
            $page->pagelets = $this->indexPageletKeys($PageLetMapper->findPageletsByPageId($page->id));
        }

        // Assume menus expire end of today. Get the next active menu as of 'now'
        $todaysMenu = $MenuMapper->getCurrentActiveMenu();

        // Did we find a menu to display? If so get menu items assign to page content
        if ($todaysMenu->id) {
            $todaysMenu->items = $MenuItemMapper->findItemsByMenuId($todaysMenu->id);
        }

        $page->menu = $todaysMenu;

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
        $PageLetMapper = $mapper('PageLetMapper');

        // Fetch pages
        $page = $PageMapper->findPageByUrl($args['url']);

        // Send 404 if not found
        if (!$page) {
            return $this->notFound($request, $response);
        }

        // Add pagelet content
        $page->pagelets = $this->indexPageletKeys($PageLetMapper->findPageletsByPageId($page->id));

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
        $menu = $MenuMapper->findById($args['id']);

        // Send 404 if menu not found
        if (!$menu) {
            return $this->notFound($request, $response);
        }

        // Get menu item details
        $menu->items = $MenuItemMapper->findItemsByMenuId($args['id']);

        $this->container->view->render($response, '_menuSample.html', ['menu' => $menu]);
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
        foreach ($this->calendar as $month => $row) {
            foreach ($this->calendar[$month]['days'] as $dateKey => $day) {
                // Find matching menu dates
                foreach ($menuDates as $menu) {
                    if ($menu->date === $day['canonicalDate'] && !empty($menu->location)) {
                        $this->calendar[$month]['days'][$dateKey]['content'] = $menu->location;
                    }
                }
            }
        }

        return;
    }

    /**
     * Populate Calendar Array
     *
     * Populates three full month calendar arrays, including leading/trailing dates
     */
    protected function populateCalendar()
    {
        // Loop through three months for three calendars
        for ($i=0; $i < 3; $i++) {
            // Start with month
            $month = new \DateTime('first day of this month 00:00:00');

            // Increment month counter with each loop
            if ($i > 0) {
                $month->modify('+' . $i . ' months');
            }

            // Start with the last monday in the prior month to start filling full calendar block
            $startDate = clone $month;
            $startDate->modify('last monday of previous month');

            // Get the first Sunday of the next month to close our calendar block
            $endDate = clone $month;
            $endDate->modify('first sunday of next month');
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
}

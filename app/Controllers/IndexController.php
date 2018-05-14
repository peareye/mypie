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

        // Fetch pages
        $page = $PageMapper->findPageSetById('home');

        // Assume menus expire end of date. Get the next active menu as of 'now'
        $todaysMenu = $MenuMapper->getCurrentActiveMenu();

        // Did we find a menu to display? If so get menu items
        if ($todaysMenu->id) {
            $todaysMenu->items = $MenuItemMapper->findItemsByMenuId($todaysMenu->id);
        }

        // Assign today's menu
        $page['menu'] = $todaysMenu;

        // Populate two month calendar
        $this->populateCalendar();

        // Get all future menus starting this month
        $menus = $MenuMapper->getFutureMenusStartingThisMonth();

        // Merge menus, start by looping this/next months then days
        foreach ($this->calendar as $month => $row) {
            foreach ($this->calendar[$month]['days'] as $dateKey => $day) {
                // Find matching menu dates
                foreach ($menus as $menu) {
                    if ($menu->date === $day['canonicalDate'] && !empty($menu->location)) {
                        $this->calendar[$month]['days'][$dateKey]['content'] = "<a href=\"#\">{$menu->location}</a>";
                    }
                }
            }
        }

        // Add calendar to page
        $page['calendar'] = $this->calendar;

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

        // Fetch pages
        $page = $PageMapper->findPageSetById($args['url']);

        // Send 404 if not found
        if (!$page) {
            return $this->notFound($request, $response);
        }

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
     * Populate Calendar
     *
     * Populates two months calendar array to $this->calendar[]
     */
    protected function populateCalendar()
    {
        // Get start and end date of period range (2 months)
        $startDate = new \DateTime('first day of this month 00:00:00');
        $endDate = new \DateTime('last day of this month 00:00:00');
        $endDate->modify('+ 1 month');

        // Set interval to 'day' and get iterable date period object
        $dayInterval = new \DateInterval('P1D');
        $dateRange = new \DatePeriod($startDate, $dayInterval, $endDate);

        // Set this and next month dates for use as titles.
        // Because $endDate is the beginning of the third month, we need to
        // do a trick to get the true next month
        $nextMonth = clone $startDate;
        $nextMonth->modify('next month');
        $this->calendar['thisMonth']['month'] = $startDate->format('Y-m-1');
        $this->calendar['nextMonth']['month'] = $nextMonth->format('Y-m-1');

        // Index to know when to flip to the nextMonth array
        $thisMonth = $startDate->format('m');
        $today = date('Y-m-d');

        foreach ($dateRange as $date) {
            $monthKey = ($date->format('m') === $thisMonth) ? 'thisMonth' : 'nextMonth';

            $this->calendar[$monthKey]['days'][] = [
                'canonicalDate' => $date->format('Y-m-d'),
                'dayClass' => ($date->format('Y-m-d') === $today) ? ' today' : '',
                'dateClass' => 'calendar-day',
                'date' => $date->format('j'),
                'content' => '',
            ];
        }

        // We need 42 array rows per months to complete a calendar
        // Get the first day of each month and determine the day of week to get
        // the offset to prepend array
        $fillerArray = [
                'canonicalDate' => '',
                'dayClass' => 'prev-month',
                'dateClass' => '',
                'date' => '',
                'content' => '',
            ];

        $firstDayOfWeekThisMonth = (int) $startDate->format('N');
        $firstDayOfWeekNextMonth = (int) $nextMonth->format('N');

        // Prepend this month
        if ($firstDayOfWeekThisMonth > 1) {
            $leadingDays = array_fill(0, $firstDayOfWeekThisMonth - 1, $fillerArray);
            $this->calendar['thisMonth']['days'] = array_merge($leadingDays, $this->calendar['thisMonth']['days']);
        }

        // Prepent next month
        if ($firstDayOfWeekNextMonth > 1) {
            $leadingDays = array_fill(0, $firstDayOfWeekNextMonth - 1, $fillerArray);
            $this->calendar['nextMonth']['days'] = array_merge($leadingDays, $this->calendar['nextMonth']['days']);
        }

        // Complete end of month by padding out to 42 days
        $this->calendar['thisMonth']['days'] = array_pad($this->calendar['thisMonth']['days'], 42, $fillerArray);
        $this->calendar['nextMonth']['days'] = array_pad($this->calendar['nextMonth']['days'], 42, $fillerArray);

        return;
    }
}

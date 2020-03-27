<?php
/**
 * Custom Extensions for Twig
 */
namespace Piton\Extensions;

use Interop\Container\ContainerInterface;

class TwigExtension extends \Twig_Extension
{
    /**
     * Twig Environment
     * @var \Twig_Environment
     */
    protected $environment;

    /**
     * @var \Slim\Interfaces\RouterInterface
     */
    private $router;

    /**
     * @var string|\Slim\Http\Uri
     */
    private $uri;

    /**
     * @var Interop\Container\ContainerInterface
     */
    private $container;

    /**
     * Application Settings
     * @var array
     */
    private $settings = [];

    /**
     * Data Cache
     * @var array
     */
    protected $cache = [];

    public function __construct(ContainerInterface $container)
    {
        $this->router = $container['router'];
        $this->uri = $container['request']->getUri();
        $this->container = $container;
        $this->settings = $container->settings;
    }

    /**
     * Identifier
     */
    public function getName()
    {
        return 'Piton';
    }

    /**
     * Initialize Extension
     */
    public function initRuntime(\Twig_Environment $environment)
    {
        $this->environment = $environment;
    }

    /**
     * Register Global variables
     */
    public function getGlobals()
    {
        return [
            'setting' => $this->settings
        ];
    }

    /**
     * Register Custom Filters
     */
    public function getFilters()
    {
        return [];
    }

    /**
     * Register Custom Functions
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('pathFor', array($this, 'pathFor')),
            new \Twig_SimpleFunction('baseUrl', array($this, 'baseUrl')),
            new \Twig_SimpleFunction('basePath', array($this, 'getBasePath')),
            new \Twig_SimpleFunction('inUrl', array($this, 'isInUrl')),
            new \Twig_SimpleFunction('checked', array($this, 'checked')),
            new \Twig_SimpleFunction('displayMenu', array($this, 'displayMenu'), array('is_safe' => array('html'))),
            new \Twig_SimpleFunction('displayPinnedMenu', array($this, 'displayPinnedMenu'), array('is_safe' => array('html'))),
            new \Twig_SimpleFunction('hasPinnedMenu', array($this, 'hasPinnedMenu')),
            new \Twig_SimpleFunction('availableMenuSections', array($this, 'availableMenuSections')),
            new \Twig_SimpleFunction('authorized', array($this, 'authorizedUser')),
            new \Twig_SimpleFunction('currentRole', array($this, 'userCurrentRole')),
            new \Twig_SimpleFunction('publishedSuppliers', array($this, 'findPublishedSuppliers')),
        ];
    }

    /**
     * Get Path for Named Route
     *
     * @param string $name Name of the route
     * @param array $data Associative array to assign to route segments
     * @param array $queryParams Query string parameters
     * @return string The desired route path without the domain, but does include the basePath
     */
    public function pathFor($name, $data = [], $queryParams = [])
    {
        return $this->router->pathFor($name, $data, $queryParams);
    }

    /**
     * Base URL
     *
     * Returns the base url including scheme, domain, port, and base path
     * @param none
     * @return string The base url
     */
    public function baseUrl()
    {
        if (is_string($this->uri)) {
            return $this->uri;
        }

        if (method_exists($this->uri, 'getBaseUrl')) {
            return $this->uri->getBaseUrl();
        }
    }

    /**
     * Base Path
     *
     * If the application is run from a directory below the project root
     * this will return the subdirectory path.
     * Use this instead of baseUrl to use relative URL's instead of absolute
     * @param void
     * @return string The base path segments
     */
    public function getBasePath()
    {
        if (method_exists($this->uri, 'getBasePath')) {
            return $this->uri->getBasePath();
        }
    }

    /**
     * In URL
     *
     * Checks if the supplied string is one of the URL segments
     * @param string $segment URL segment to find
     * @return boolean
     */
    public function isInUrl($segmentToTest = null)
    {
        // Verify we have a segment to find
        if ($segmentToTest === null) {
            return false;
        }

        // If just a slash is provided, meaning 'home', then evaluate
        if ($segmentToTest === '/' && ($this->uri->getPath() === '/' || empty($this->uri->getPath()))) {
            return true;
        } elseif ($segmentToTest === '/' && !empty($this->uri->getPath())) {
            return false;
        }

        // Clean segment of slashes
        $segmentToTest = trim($segmentToTest, '/');

        return in_array($segmentToTest, explode('/', $this->uri->getPath()));
    }

    /**
     * Set Checkbox
     *
     * If the supplied value is truthy, 1, or 'Y' returns the checked string
     * @param mixed $value
     * @return string
     */
    public function checked($value = 0)
    {
        return ($value == 1 || $value == 'Y') ? 'checked="checked"' : '';
    }

    /**
     * Display Menu
     *
     * Gets menu and menu item data for the current menu, and renders _menu.html
     * @param none
     * @return string
     */
    public function displayMenu()
    {
        // Get dependencies
        $mapper = $this->container->dataMapper;
        $MenuMapper = $mapper('MenuMapper');
        $MenuItemMapper = $mapper('MenuItemMapper');

        $menuList = $MenuMapper->getCurrentActiveMenus();

        // Did we find menus to display?
        if (is_array($menuList)) {
            foreach ($menuList as $key => $row) {
                if (isset($row->id)) {
                    $menuList[$key]->items = $MenuItemMapper->findItemsByMenuId($row->id);
                }
            }
        }

        return $this->environment->render('includes/_menu.html', ['menuList' => $menuList]);
    }

    /**
     * Is there a pinned menu to display?
     *
     * Checks and caches pinned menu data to render using displayPinnedMenu()
     * Returns true|false without returning HTML
     * @param none
     * @return bool
     */
    public function hasPinnedMenu()
    {
        // Checked cached data if already requested
        if (isset($this->cache['pinnedMenu'])) {
            return true;
        }

        $menuMapper = ($this->container->dataMapper)('MenuMapper');
        $menuItemMapper = ($this->container->dataMapper)('MenuItemMapper');
        $menuList = $menuMapper->getPinnedMenus();

        // Did we find menus to display?
        if (is_array($menuList)) {
            foreach ($menuList as $key => $row) {
                if (isset($row->id)) {
                    $menuList[$key]->items = $menuItemMapper->findItemsByMenuId($row->id);
                }
            }
        }

        if ($menuList) {
            $this->cache['pinnedMenu'] = $menuList;
            return true;
        }

        return false;
    }

    /**
     * Display Pinned Menu
     *
     * Gets menu and menu item data for the current menu, and renders _menu.html
     * @param none
     * @return string
     */
    public function displayPinnedMenu()
    {
        // Get cached data if already requested
        if (isset($this->cache['pinnedMenu'])) {
            return $this->environment->render('includes/_menu.html', ['menuList' => $this->cache['pinnedMenu'], 'isPinned' => true]);
        }

        // Get dependencies
        $mapper = $this->container->dataMapper;
        $MenuMapper = $mapper('MenuMapper');
        $MenuItemMapper = $mapper('MenuItemMapper');

        $menuList = $MenuMapper->getPinnedMenus();

        // Did we find menus to display?
        if (is_array($menuList)) {
            foreach ($menuList as $key => $row) {
                if (isset($row->id)) {
                    $menuList[$key]->items = $MenuItemMapper->findItemsByMenuId($row->id);
                }
            }
            $this->cache['pinnedMenu'] = $menuList;
        }

        return $this->environment->render('includes/_menu.html', ['menuList' => $menuList, 'isPinned' => true]);
    }

    /**
     * Available Menu Sections
     *
     * Process list of menu items, and returns array of available sections
     * @param array
     * @return array
     */
    public function availableMenuSections($menuItems)
    {
        $available = [];
        foreach ($menuItems as $menu) {
            $available[$menu->section] = true;
        }

        return $available;
    }

    /**
     * Authorized User
     *
     * Returns true|false if authorized to requested role level
     * @param str N: No privileges, Y: Admin privileges, S: Super User
     * @return bool
     */
    public function authorizedUser($requiredPermission)
    {
        $session = $this->container->get('sessionHandler');
        $userRole = $session->getData('role');
        $permissions = ['N' => 1, 'A' => 2, 'S' => 3];

        if (empty($userRole)) {
            return false;
        }

        return ($permissions[$requiredPermission] <= $permissions[$userRole]);
    }

    /**
     * Current User Role
     *
     * Returns current role from session
     * @return mixed
     */
    public function userCurrentRole()
    {
        $session = $this->container->get('sessionHandler');
        return $session->getData('role');
    }

    /**
     * Find Suppliers
     *
     * Get all published suppliers
     * @param none
     * @return mixed
     */
    public function findPublishedSuppliers()
    {
        // Get dependencies
        $mapper = $this->container->dataMapper;
        $SupplierMapper = $mapper('SupplierMapper');

        return $SupplierMapper->findPublishedSuppliers();
    }
}

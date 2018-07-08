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
            new \Twig_SimpleFunction('availableMenuSections', array($this, 'availableMenuSections')),
            new \Twig_SimpleFunction('authorized', array($this, 'authorizedUser')),
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

        // Assume menus expire end of today. Get the next active menu as of 'now'
        $todaysMenu = $MenuMapper->getCurrentActiveMenu();

        // Did we find a menu to display?
        if (isset($todaysMenu->id)) {
            $todaysMenu->items = $MenuItemMapper->findItemsByMenuId($todaysMenu->id);
        } else {
            $todaysMenu->menuNotFound = true;
        }

        return $this->environment->render('includes/_menu.html', ['menu' => $todaysMenu]);
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
}

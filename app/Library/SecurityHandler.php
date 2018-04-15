<?php
/**
 * Security Handler
 *
 * Manages Authorization and Authentication
 */
namespace Piton\Library;

class SecurityHandler
{
    /**
     * Session Handler
     *
     * @var Session Class
     */
    protected $session;

    /**
     * Constructor
     */
    public function __construct($sessionHandler)
    {
        $this->session = $sessionHandler;
    }

    /**
     * Is Authenticated
     *
     * Checks if user is currently logged in
     * @return boolean
     */
    public function isAuthenticated()
    {
        return $this->session->getData('loggedIn');
    }

    /**
     * Start Authenicated Session
     */
    public function startAuthenticatedSession()
    {
        $this->session->setData(['loggedIn' => true]);
    }

    /**
     * End Authenticated Session
     */
    public function endAuthenticatedSession()
    {
        $this->session->destroy();
    }
}

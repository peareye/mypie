<?php
/**
 * Domain Model Abstract
 *
 * Base class for all domain models
 */
namespace Piton\Models;

abstract class DomainObjectAbstract
{
    // This $id avoids an error when the __get() magic method in DomainObjectAbstract is called
    // on a non-existent property
    public $id;

    /**
     * Get Object Property
     *
     * Applies only to private and protected properties
     */
    public function __get($key)
    {
        return isset($this->$key) ?: false;
    }

    /**
     * Set Object Property
     *
     * Applies only to private and protected properties
     */
    public function __set($key, $value)
    {
        $this->$key = $value;
    }
}

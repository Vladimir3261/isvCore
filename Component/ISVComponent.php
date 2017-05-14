<?php
namespace isv\Component;
use isv\EventManager\EventManager;

/**
 * Class ISVComponent provide all components in system
 *  @version 1.1
 * @package isv\Component
 */
abstract class ISVComponent extends EventManager  implements ISVComponentInterface
{
    /**
     * constructor call init method this method is required 
     * to implement in child classes
     */
    public function __construct()
    {
        $this->init();
    }
}
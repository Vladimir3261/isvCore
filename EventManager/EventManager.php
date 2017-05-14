<?php
/**
 * Created by PhpStorm.
 * User: vladimir
 * Date: 08.04.16
 * Time: 11:42
 */

namespace isv\EventManager;
use isv\Exception\CoreException;

/**
 *
 * Class EventManager
 * @package isv\EventManager
 * @version 1.1
 */
class EventManager
{
    /**
     * This variable contain all events listeners on application
     * @var array
     */
    private static $listeners = [];

    /**
     * @param string $key
     * @param mixed $value
     * @param bool $reload
     * @return bool
     * @throws CoreException
     */
    public static function addEventManager($key, $value, $reload=false)
    {
        try {
            if (isset(self::$listeners[$key]) && !$reload) {
                throw new CoreException('The event ' . $key . ' Already set.', 57);
            }
        } catch (CoreException $e) {
            $e->reset();exit(1);
        }
        self::$listeners[$key] = $value;
        return true;
    }

    /**
     * Runtime remove event listeners
     * @param string $key
     */
    public static function removeEventManager($key)
    {
        unset(self::$listeners[$key]);
    }

    /**
     * Event manager Init on application starting
     * @param array array $EM
     */
    public static function initEventManager(array $EM)
    {
        self::$listeners = $EM;
    }

    /**
     * @return array
     */
    public static function getListeners()
    {
        return self::$listeners;
    }

    /**
     * Main event manager logic this method looking registered events for
     * called protected method, call events before after and change arguments for called method
     * @param $name
     * @param $arguments
     * @return mixed
     * @throws CoreException
     */
    public function __call($name, $arguments)
    {
        // Registered listeners array
        $listeners = EventManager::getListeners();
        // get called method class
        $class = get_class($this);
        // get full listener name for called method
        $name = substr($name, 1);
        $listener = $class.'::'.$name;
        // check if exists in listeners array called method
        try
        {
            if(!key_exists($listener, $listeners))
            {
                throw new CoreException('Access method is not public', 7756);
            }
        }
        catch (CoreException $ex) {
            $ex->reset();exit(1);
        }

        isset($listeners[$listener]['before']) ? $before = call_user_func_array($listeners[$listener]['before'], $arguments)
            : $before = $arguments;
        if($before === false){ return false;}
        $result = call_user_func_array([$this, $name], $before);
        !is_array($result) ? $result = [$result] : false ;
        if(isset($listeners[$listener]['after']))
        {
            return call_user_func($listeners[$listener]['after'], $result);
        }
        else
        {
            return $result;
        }
    }
}
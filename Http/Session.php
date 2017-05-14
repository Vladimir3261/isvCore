<?php
/**
 * Created by PhpStorm.
 * User: vladimir
 * Date: 23.07.16
 * Time: 18:21
 */

namespace isv\Http;
use isv\IS;

/**
 * Implement basic session containers for CMS
 * Class Session
 * @package isv\Http
 * @version 1.1
 */
class Session
{
    /**
     * Session manager instance (current class)
     * @var null
     */
    private static $instance = NULL;

    /**
     * Current working container (session key)
     * @var null $container
     */
    private static $container = NULL;

    /**
     * current working container array key name
     * @var $containerName
     */
    private static $containerName;

    /**
     * This is singleton
     * Session constructor.
     */
    private function __construct(){}

    /**
     * Clone denied
     */
    private function __clone(){}

    /**
     * @return Session
     */
    public static function instance()
    {
        if (session_status() == PHP_SESSION_NONE) {
            if(IS::app()->getConfig('config')['sessionPath'])
                session_save_path();
            @session_start();
        }
        if( self::$instance===NULL )
        {
            self::$instance = new self;
        }
        return self::$instance;
    }

    /**
     * @param null $container
     * @return null
     */
    public function getData($container=NULL)
    {
        if($container)
        {
            return isset($_SESSION[$container]) ? $_SESSION[$container] : NULL;
        }
        else
        {
            return isset($_SESSION[self::$containerName]) ? $_SESSION[self::$containerName] : NULL;
        }
    }

    /**
     * @param $containerName
     * @return Session
     */
    public function container($containerName)
    {
         self::$container = isset($_SESSION[$containerName]) ? $_SESSION[$containerName] : NULL;
         self::$containerName = $containerName;
         return self::$instance;
    }

    /**
     * @param $containerName
     */
    public function createContainer($containerName)
    {
        self::$containerName = $containerName;
        $_SESSION[$containerName] = [];
        self::$container = $_SESSION[$containerName];
    }


    /**
     * Get and set variables to current container
     * @param $name
     * @param $arguments
     * @return null
     */
    public function __call($name, $arguments)
    {
       if($arguments)
            return $_SESSION[self::$containerName][$name] = $arguments[0];
        else
            return isset($_SESSION[self::$containerName][$name]) ? $_SESSION[self::$containerName][$name] : NULL;
    }

    /**
     * Remove element from selected container
     * @param $container
     * @param $element
     */
    public function removeElement($container, $element)
    {
        unset($_SESSION[$container][$element]);
    }

    /**
     * remove Container
     * @param $container
     */
    public function removeContainer($container)
    {
        unset($_SESSION[$container]);
    }

    /**
     * Reload container data
     * @param $container
     * @param $data
     */
    public function loadContainer($container, $data)
    {
        $_SESSION[$container] = $data;
    }

    /**
     * FLASH MESSAGES!!
     * @param $key
     * @param $message
     */
    public function setFlash($key, $message)
    {
        $_SESSION['flash_messages'][$key] = $message;
    }

    /**
     * FLASH MESSAGES!!
     * @param $key
     * @return bool
     */
    public function getFlash($key)
    {
        if( isset($_SESSION['flash_messages'][$key]) )
        {
            $return = $_SESSION['flash_messages'][$key];
            unset($_SESSION['flash_messages'][$key]);
            return $return;
        }
        else
        {
            return false;
        }
    }

    public function flash($key)
    {
        return isset($_SESSION['flash_messages'][$key]);
    }

    /**
     * destroy all session
     * @return bool
     */
    public function kill()
    {
        return session_destroy();
    }
}
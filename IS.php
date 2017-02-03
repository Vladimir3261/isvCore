<?php
namespace isv;
use isv\Component\ISVComponent;
use isv\Exception\CoreException;
use isv\Helper\Breadcrumbs;
use isv\Http\Request;
use isv\Router\Router;
use isv\Http\Session;
use Component\AuthComponent;
use isv\EventManager\EventManager;
/**
 * Class IS The main file of CMS defines a global repository for all core applications
 *  can take to preserve and recover any type of data is the
 * @version 1.1
 * @package isv
 */
class IS
{
    /**
     * @var object $instance application instance
     */
    private static $instance;
    /**
     * @var mixed $var can be used like data storage in application runtime
     */
    private static $var;
    /**
     * @var string $template view file path template
     */
    private static $template;
    /**
     * @var mixed $config configuration of application
     */
    private static $config;
    /**
     * @var null|Request all info about current request
     */
    private static $request = null;

    private static $user = null;

    /**
     * Current page breadcrumbs
     * @var null | array
     */
    private static $breadcrumbs = NULL;


    /**
     * setter function
     *
     * @param string $key
     * @param mixed $var
     * @param boolean $reload
     * @throws CoreException
     * @return mixed
     * This static method set new keys to $var array or rewrite the
     * key value if flag reload == true in default $reload == false
     *
     */
    public  function set($key, $var, $reload = false)
    {
        try {
            if (isset(self::$var[$key]) && !$reload) {
                throw new CoreException('The variable ' . $key . ' Already set.', 55);
            }
        } catch (CoreException $e) {
            $e->reset();exit(1);
        }
        self::$var[$key] = $var;
        return true;
    }
    /**
     * @param string $key
     * @return mixed | null this method returns the vars from $var
     * array using the key
     *
     */
    public function get($key)
    {
        if (!isset(self::$var[$key])) {
            return null;
        }
        return self::$var[$key];
    }

    /**
     * @param string
     * @return void save path to view file
     *
     */
    static function setTemplate($template)
    {
        self::$template = $template;
    }

    /**
     * @return string path to view file
     */
    static function getTemplate()
    {
        return self::$template;
    }

    /**
     * @param string $key
     * @return array | bool this method returns the vars from $config
     * array using the key
     */
    public function getConfig($key)
    {
        if (isset(self::$config[$key])) {
            return self::$config[$key];
        }
        else {
            return false;
        }
    }

    /**
     * This method needs for set application configuration without starting
     * application for console commands like doctrine
     * @param $file string config file path
     */
    public function setConfig($file)
    {
        self::$config = require_once $file;
    }
    /**
     * function request return all information about current request
     * @see Request
     * @return Request
     */
    public function request()
    {
        if(!isset(self::$request) || self::$request === null){
            self::$request = new Request();
            return self::$request;
        }
        return self::$request;
    }
    /**
     * constructor is private we cant't call it
    */
    private function __construct()
    {
        /*
         * this function working just in this class
         */
    }

    /**
     * Singleton
    */
    private function __clone()
    {
        // clone denied
    }
    /**
    *
    * @return IS  Main application class
    */
    public static function app()
    {
         // check the actual instance
        if (self::$instance === null)
        {
         // create new instance of class, if instance not already created
            self::$instance = new self();
        }
         // return current instance of application class
        return self::$instance;
    }

    /**
     *
     * @param string $component
     * @return ISVComponent
     * @throws CoreException
     */
    public function component($component)
    {
        $class = '\Component\\'.ucfirst($component).'Component';
        $pluginsConfig = static::getConfig('plugins');
        $config = $pluginsConfig['components']; // Plugins config array

        if(key_exists($class, $config))
        {
            $pluginClass = $config[$class];
            if( class_exists($pluginClass) && in_array('Component\\'.ucfirst($component).'Component', class_parents($pluginClass)) )
            {
                $class = $pluginClass;
            }
        }
        try
        {
            if(!class_exists($class))
            {
                throw new CoreException('component '.$component.'Component not exists', 8114);
            }
        }
        catch (CoreException $e)
        {
            $e->display();
        }
        return new $class;
    }

    /**
     *
     * @return Session
     */
    public function session()
    {
         return Session::instance();
    }

    /**
     * @return AuthComponent
     */
    public function user()
    {
        if(self::$user === null)
        {
            self::$user = $this->component('auth');
        }
        return self::$user;
    }

    /**
     * Breadcrumbs class data
     * @return Breadcrumbs|null
     */
    public function breadcrumbs()
    {
        if(self::$breadcrumbs === null)
            self::$breadcrumbs = new Breadcrumbs();
        return self::$breadcrumbs;
    }

    /**
     * Start the application load Router and include needle controllers
     * call the controller action
     */
    public function start()
    {
        set_error_handler([$this, 'exception_error_handler']);
        session_start();
        $configFiles = scandir('../config');
        $cfg = [];
        foreach($configFiles as $config)
        {
            if(is_file('../config/'.$config))
            {
                $conf = include_once '../config/'.$config;
                $cfg = array_merge($cfg, $conf);
            }
        }
        self::$config = $cfg;
        isset(self::$config['EventManager']) ? $EM = self::$config['EventManager'] : $EM = [];
        EventManager::initEventManager($EM);
        $router = new Router();
        $router->load();
    }

    /**
     * Handle server errors.
     * Note: Fatal errors can't be handled by this function
     * @param $severity int
     * @param $message string
     */
    public function exception_error_handler($severity, $message)
    {
        if (!(error_reporting() & $severity)) {
            return;
        }
        try{
            throw new CoreException("Server error: ".$message, $severity);
        }catch (CoreException $e){
            $e->reset();
        }
    }

}
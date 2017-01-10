<?php

namespace isv\Router;
use isv\Exception\RouterException;
use isv\IS;

/**
 * Class Router. receives data from the address bar and searches
 * and the connection request from the respective Controllers url
 * @package isv\Router
 * @version 1.1
 */
Class Router 
{
    /**
     * @var array|null the router configuration array from router.config.php file
     */
    private $config = null;

    /**
     * @var array|null current request string
     */
    private $route = null;

    /**
     * Router constructor. inject router.config array to this class
     */
    public function __construct()
    {
        if(IS::app()->getConfig('router'))
        {
            $this->config = IS::app()->getConfig('router');
        }
        $this->route = IS::app()->request()->getRoute();
        $this->route[(strlen($this->route)-1)] === '/' ? $this->route = substr($this->route, 0,-1) : false;
    }

    /**
     * function load include controllers and actions used
     * router.config.php file params
     * @throws RouterException
     * @return void
     */
     public function load()
    {
        $loaded = FALSE;
        $configRoutes = array_keys($this->config);
        foreach($configRoutes as $configRoute)
        {
            $configParts = explode('/{', $configRoute);
            $static = $configParts[0];
            if( substr($this->route, 0, strlen($static)) === $static ) {
                $config = explode('/', $configRoute);
                $real   = explode('/', $this->route);
                $params = [];
                foreach ($config as $k=>$part) {
                    $params[str_replace(['{', '}'], ['', ''], $part)] = isset($real[$k]) ? $real[$k] : NULL;
                }
                $controller = $this->config[$configRoute];
                $action = isset($params['action']) ? $params['action'].'Action' : 'indexAction';
                try{
                    if(!$this->isController($controller) || !$this->isAction($controller, $action))
                        throw new RouterException("Main router loader can't load $controller::$action", 9908);
                    $this->includes($controller, $action, $params);
                }catch (RouterException $e){
                    $e->pageNotFound();
                }
                $loaded = TRUE;
                break;
            } else {
                continue;
            }
        }
        if(!$loaded)
            $this->loadDefault();
    }
    
    /**
     * function check action check to callable action in controller class
     * @param $controller
     * @param $action
     * @return bool
     */
    private function isAction($controller, $action)
    {
        return is_callable(array('\Controller\\'.$controller, $action));
    }

    /**
     * @param string $controller
     * @return bool check ti isset controller
     */
    private function isController($controller)
    {
        return class_exists('\Controller\\'.$controller);
    }

    /**
     * @since v2.0
     * validator function check input url params to regexp
     * @param $out
     * @param $regexp
     * @return int
     */
    /*private function isValid($out, $regexp)
    {
        return preg_match($regexp, $out);
    }*/

    /**
     * function includes include controller and create new instance of controller
     * class after call actions of that controller
     *
     * @param $controller string controller class
     * @param $action   string   controller method (action)
     * @throws RouterException
     * @param $params   mixed    params of controller file
     * @return void
     */
    public function includes($controller, $action, $params)
    {
        IS::app()->set('controller', $controller);
        IS::app()->set('action', $action);
        $fullControllerName = '\Controller\\'.$controller;
        $controllerInstance = new $fullControllerName($params);
        $controllerInstance->$action();
    }

    /**
     * method loadDefault include the controllers and
     * call actions based parse of request string use default
     * application logic implements in this method
     *@throws RouterException
     * @return void
     */
    public function loadDefault()
    {
        $route = $this->route;
        $route = explode('/', $route);
        $emptyRoutes = [''];
        $routeArray = array_diff($route, $emptyRoutes);
        $controller = isset($routeArray[0]) ? ucfirst($routeArray[0]).'Controller' : 'IndexController';
        ($controller === 'Controller') ? $controller = 'IndexController' : false ;
        try
        {
            if(!$this->isController($controller))
            {
                throw new RouterException('Default loader can\'t find controller with name '.$controller, 5489);
            }           
        }
        catch (RouterException $e)
        {
            $e->pageNotFound();
            exit(1);
        }
        array_shift($routeArray);
        isset($routeArray[0]) ? $action = $routeArray[0].'Action' : $action = 'indexAction';
        try
        {
            if(!$this->isAction($controller,$action))
            {
                throw new RouterException('Controller '.$controller.' not have action '.$action, 1187);
            }         
        } catch(RouterException $e)
        {
            $e->pageNotFound();
            exit(1);
        }
        array_shift($routeArray);
        $this->includes($controller,$action,$routeArray);
    }
}
<?php
namespace isv\Controller;
use isv\EventManager\EventManager;
use isv\Http\Header;

/**
 * Class ControllerBase the base controller for all controllers in system
 * Class ControllerBase
 * @package isv\Controller
 * @version 1.1
 */
Abstract Class ControllerBase extends  EventManager
{
    /**
     * @var array $params
     */
    private $params;

    /**
     * ControllerBase constructor. Takes parameters when creating an instance
     * of the class and stores them in the $ params to access them
     * from the controller that extends the base class
     * @param array $arr
     */
    public function __construct($arr)
    {
        $this->params = $arr;
        $this->init();
    }

    /**
     * Custom controller init
     */
    protected function init(){}

    /**
     * indexAction method require to declared
     * @return mixed
     */
    abstract function indexAction();

    /**
     * User Redirect to $redirectLink
     * @param $redirectLink
     * @return  void
     */
    protected function redirect($redirectLink)
    {
        Header::send(302);
        Header::location($redirectLink);
    }

    /**
     * Controller params from url address bar
     * @param null $index
     * @return array|mixed|null
     */
    public function params($index=null)
    {
        if(!$index){
            return $this->params;
        }else{
            return isset($this->params[$index]) ? $this->params[$index] : null;
        }
    }
}
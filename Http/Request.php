<?php
/**
 * Created by PhpStorm.
 * User: vladimir
 * Date: 05.02.16
 * Time: 12:58
 */

namespace isv\Http;
/**
 * Class contain information about current request
 * Class Request
 * @package isv\Http
 * @version 1.1
 */
class Request
{
    /**
     * getMethod function return request method
     * @return string
     */

    public function getMethod()
    {
        if (isset($_SERVER['REQUEST_METHOD'])) {
            return strtoupper($_SERVER['REQUEST_METHOD']);
        }
        return 'GET';
    }
    
    public function postData($key=NULL)
    {
        if($key)
            return isset($_POST[$key]) ? $_POST[$key] : NULL;
        else
            return $_POST;
    }

    /**
     * function getRequest
     * @return string request string
     */
    public function getRequest()
    {
        return $_SERVER['REQUEST_URI'];
    }

    /**
     * function getRoute get the http query string or '/' if not isset $_GET['route']
     * @return string
     */
    public function getRoute()
    {
        /**
         * @todo this method need to modified
         */
        if(isset($_GET['route']) && $_GET['route'])
        {
            return $_GET['route'];
        }
        return $this->getRequest();
    }

    /**
     * function isHttps return protocol HTTP or HTTPS
     */
    public function isHttps()
    {
        return isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'];
    }
    /**
     * function isPost check the request to post
     * @return bool
     */
    public function isPost()
    {
        return $this->getMethod() === 'POST';
    }
    /**
     * function isAjax check request to xmlHttpRequest
     * @return bool
     */
    public function isAjax()
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';
    }

    public function refer()
    {
        return isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '/';
    }
}
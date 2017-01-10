<?php
namespace isv\Errors;
use isv\Http\Header;

/**
 * This class provide all errors in system and include controllers
 *
 * @author isv
 * @package isv\Errors
 * @version 1.1
 */
class Errors
{
    /**
     * This method include errors controllers and action in system
     * @param type string
     * @param type string
     * @param type array | null
     * @return void
     */
    private static function includes($controller, $action, $params=null)
    {
        if(is_callable(array($controller, $action)))
        {
            $errorController = new $controller($params);
            $errorController->$action();
        }
        else
        {
            echo 'An error occurred please contact with site administrator for fix this issue';
            exit(1);
        }
    }
    /**
     * Set 404 Page not found error
     */
    public static function pageNotFound()
    {
        self::includes('\Controller\Errors\ErrorController', 'indexAction');
        Header::send(404);
    }

    /**
     * Page rendering errors Internal Server Error
     */
    public static function viewError()
    {
        self::includes('\Controller\Errors\ErrorController', 'servererorAction');
        Header::send(500);
    }

    /**
     * Bad Request
     */
    public static function badRequest()
    {
        self::includes('\Controller\Errors\ErrorController', 'badrequestAction');
        Header::send(400);
    }
}

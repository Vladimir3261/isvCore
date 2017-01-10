<?php
/**
 * Created by PhpStorm.
 * User: vladimir
 * Date: 12.02.16
 * Time: 17:40
 */

namespace isv\Exception;
use isv\Errors\Errors;

/**
 * Exceptions for router
 * Class RouterException
 * @package isv\Exception
 */
class RouterException extends ISVException
{
    public function pageNotFound()
    {
        if(!DEBUG){
            Errors::pageNotFound();
        }else{
           parent::display();
        }
    }

    public function badRequest()
    {
        if(!DEBUG){
            Errors::badRequest();
        }else{
            parent::display();
        }
    }
}
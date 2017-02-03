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
 * Class CoreException
 * @package isv\Exception
 */
class CoreException extends ISVException
{
    public function reset()
    {
        if(!DEBUG){
            Errors::serverError();
        }else{
           parent::display(); 
        }
        exit(1);
    }
}
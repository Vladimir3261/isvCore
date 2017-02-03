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
 * Class ViewException
 * @package isv\Exception
 */
class ViewException extends ISVException
{
    public function invalidParams()
    {
        if(!DEBUG)
        {
            Errors::viewError();
        }else
        {
           parent::display(); 
        }
    }
}
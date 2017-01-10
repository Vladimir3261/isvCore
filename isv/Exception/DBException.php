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
 * Class DBException
 * @package isv\Exception
 */
class DBException extends ISVException
{
    public function __construct($message=NULL, $code=0)
    {
        parent::__construct($message, $code);
        $this->dbError();exit(1);
    }

    public function dbError()
    {
        if(!DEBUG)
        {
            Errors::viewError();
        }
        else
        {
           parent::display(); 
        }
    }
}
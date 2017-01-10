<?php
/**
 * Created by PhpStorm.
 * User: vladimir
 * Date: 16.07.16
 * Time: 23:23
 */

namespace isv\Developer;

/**
 * Developer Tools
 * Class Developer
 * @package isv\Developer
 */
class Developer
{
    public static function dump($data, $kill=NULL)
    {
        echo '<pre>';
        var_dump($data);
        echo '<pre>';
        if($kill)
            exit(1);
    }
}
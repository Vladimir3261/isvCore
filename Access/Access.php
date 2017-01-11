<?php
/**
 * Created by PhpStorm.
 * User: vladimir
 * Date: 10.08.16
 * Time: 17:11
 */

namespace isv\Access;

/**
 * Class Access
 * @package isv\Access
 */
class Access
{
    const ADMIN = 1;

    public static function isAdmin()
    {
        return true;
    }
}
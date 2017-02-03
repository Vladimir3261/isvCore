<?php
/**
 * Created by PhpStorm.
 * User: vladimir
 * Date: 12/4/16
 * Time: 4:43 PM
 */

namespace isv\Helper;


class Random
{
    public static function randomPassword($length)
    {
        $a = str_split("abcdefghijklmnopqrstuvwxyABCDEFGHIJKLMNOPQRSTUVWXY0123456789");
        shuffle($a);
        return substr( implode($a), 0, $length );
    }
}
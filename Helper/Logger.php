<?php
/**
 * Created by PhpStorm.
 * User: vladimir
 * Date: 12/5/16
 * Time: 3:58 PM
 */

namespace isv\Helper;


use isv\IS;

class Logger
{
    public static function log($file, $data)
    {
        $conf = IS::app()->getConfig('config');
        $logDir = isset($conf['logs']) ? $conf['logs'] : 'logs';
        $filePath = ROOTDIR.DIRSEP.$logDir.DIRSEP.$file.'.log';
        $str = is_array($data) ? var_export($data, true) : $data;
        file_put_contents($filePath, '['.date('d-m-Y H:i:s').']  '.$str."\r\n", FILE_APPEND);
    }
}
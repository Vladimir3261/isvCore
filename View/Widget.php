<?php
/**
 * Created by PhpStorm.
 * User: vladimir
 * Date: 06.04.16
 * Time: 16:14
 */

namespace isv\View;
use isv\Exception\CoreException;
use isv\IS;

/**
 * Class Widget provide all widgets in system...
 * Class Widget
 * @package isv\view
 * @version 1.1
 */
abstract class Widget
{
    protected $params = null;

    /**
     * Widget constructor. save supplied params to $params for access to them from extend class
     * after call required method main.
     * @param $params
     */
    public function __construct($params)
    {
        $this->params = $params;
        $this->main();
    }

    /**
     * rendering widget template with assigned params, from Widgets directory
     * @param $fileName
     * @param null $array
     * @throws CoreException
     */
    protected function render($fileName, $array = null)
    {
        $file = ROOTDIR.DIRSEP.'Widgets'.DIRSEP.'views'.DIRSEP.IS::app()->getConfig('config')['template'].DIRSEP.$fileName.'.php';
        if(is_array($array) && count($array))
        {
            foreach($array as $k=>$v)
            {
                $$k = $v;
            }
        }
        try
        {
            if(!is_file($file))
                throw new CoreException("Widget file $file not exists in Widgets/views directory", 1153);
        }
        catch(CoreException $e)
        {
            $e->reset();exit(1);
        }
        include $file;
    }

    /**
     * @param null $params
     * Show widget
     * @return bool
     * @throws \Exception
     */
    public static function show($params=null)
    {
        $class = get_called_class();
        try
        {
            if( new $class($params) )
                throw new \Exception('Can\'t fin class '.$class);
            return true;
        }catch (\Exception $e){
            return false;
        }
    }

    /**
     * abstract function required to declare this method run the widget code
     * @return mixed
     */
    abstract public function main();

    /**
     * @return string
     * @param $filename
     * @param null|array $params
     */
    public function img($filename, $params=NULL)
    {
        $filename = is_file(ROOTDIR.DIRSEP.IS::app()->getConfig('config')['publicDir'].$filename) ? $filename : '/nophoto.jpg';
        $str = '<img src="'. $this->getSiteLink($filename) .'" ';
        if($params && is_array($params)){
            foreach($params as $k=>$v){
                $str.=$k.'="'. $v .'" ';
            }
        }
        $str.='/>';
        return $str."\r\n";
    }

    /**
     * get full link to site for access from web. For include css and js files to page
     * @param $link string
     * @return string
     */
    public function getSiteLink($link)
    {
        $config = IS::app()->getConfig('config');
        IS::app()->request()->isHttps() ? $path = 'https://' : $path = 'http://';
        return $path.$config['host'].'/'.$link;
    }
}
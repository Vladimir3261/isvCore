<?php
/**
 * Created by PhpStorm.
 * User: vladimir
 * Date: 18.08.16
 * Time: 16:26
 */

namespace isv\Helper;

/**
 * Breadcrumbs class for using in controllers.
 * Class Breadcrumbs
 * @package isv\Helper
 */
class Breadcrumbs
{
    /**
     * Breadcrumbs array with names of links and links
     * This array can be get using method get of this class
     * Note! This array can't be saved though any site pages
     * @var array
     */
    public  $breadcrumbs = [];

    /**
     * Add new breadcrumb to breadcrumbs storage array
     * @param $name
     * @param $link
     */
    public  function add($name, $link)
    {
        $this->breadcrumbs[$name] = $link;
    }

    /**
     * Get All stored breadcrumbs at current call moment
     * @return array
     */
    public function get()
    {
        return $this->breadcrumbs;
    }

    /**
     * Remove breadcrumb.
     * if you need remove breadcrumb call this method before calling get method
     * this function just delete key from breadcrumbs array
     * @param $key
     */
    public function remove($key)
    {
        unset($this->breadcrumbs[$key]);
    }
}
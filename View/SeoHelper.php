<?php
/**
 * Created by PhpStorm.
 * User: vladimir
 * Date: 06.04.16
 * Time: 15:15
 */

namespace isv\View;

/**
 * ISV FRAMEWORK SEO helper for seo meta tags
 * Class SeoHelper
 * @package isv\view
 * @version 1.1
 */
class SeoHelper
{
    public static function title($title){
        return '<title>'.$title.'</title>'."\r\n";
    }

    public static function description($description){
        return '<meta name="description" content="'.$description.'">'."\r\n";
    }

    public static function contentType($type){
        return '<meta http-equiv="Content-Type" content="'.$type.'" />'."\r\n";
    }

    public static function keywords($keywords){
        return '<meta name="keywords" content="'.$keywords.'" />'."\r\n";
    }

    public static function author($author){
        return '<meta name="author" content="'.$author.'" />'."\r\n";
    }

    public static function copyright($copy, $lang){
        return '<meta name="copyright" lang="'.$lang.'" content="'.$copy.'" />'."\r\n";
    }

    public static function documentState($documentState){
        return '<meta name="document-state" content="'.$documentState.'" />'."\r\n";
    }

    public static function revisit($revisit){
        return '<meta name="revisit" content="'.$revisit.'" />'."\r\n";
    }

    public static function robots($robotsContent){
        return '<meta name="robots" content="'.$robotsContent.'" />'."\r\n";
    }

    public static function url($url){
        return '<meta name="url" content="'.$url.'" />'."\r\n";
    }

    public static function contentLang($lang){
        return '<meta http-equiv="content-language" content="'.$lang.'" />'."\r\n";
    }

    public static function charset($charset="UTF-8"){
        return '<meta charset="'.$charset.'">'."\r\n";
    }

}
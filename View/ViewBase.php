<?php
namespace isv\View;
use isv\Exception\ViewException;
use isv\IS;

/**
 * base class ViewBase main application class
 * that is responsible for the output pattern and patterns of access
 * to variables transfer from the controller
 * @package isv\View
 * @version 1.1
 */
class ViewBase
{
    const RESPONSE_HTML = 1;
    const RESPONSE_JSON = 2;

    protected $params;
    /**
     * @param array | null $params
     * @throws ViewException
     */

    /**
     * view files path
     * @var string
     */
    protected $viewPath;

    /**
     * current render file
     * @var string
     */
    private $viewFile;

    protected $templateName;

    protected $controller;

    protected $ext;

    private $responseType = ViewBase::RESPONSE_HTML;

    public function __construct($params=null, $responseType=ViewBase::RESPONSE_HTML)
    {
        // check input params from controller this must be array required
        try
        {
            if(!is_array($params) && $params !== null)
                throw new ViewException('View params must be array', 1109);
        }
        catch (ViewException $e)
        {
            $e->invalidParams();
            exit(1);
        }
        $this->params = $params;
        $this->responseType = $responseType;
    }

    public function response()
    {
        if($this->responseType === static::RESPONSE_JSON)
        {
            header('Content-Type: application/json');
            echo json_encode($this->params);die();
        }
        $config = IS::app()->getConfig('config');
        $templateName = IS::app()->get('templateName') ? IS::app()->get('templateName') : $config['template'];
        $this->controller = str_replace('\\', DIRSEP, strtolower(str_replace('Controller', '', IS::app()->get('controller'))));
        $action = strtolower(str_replace('Action', '', IS::app()->get('action')));
        $this->viewPath = isset($config['viewPath']) ? $config['viewPath'] : ROOTDIR.'/views';
        if( IS::app()->get('layout') )
        {
            $layoutName = IS::app()->get('layout');
        }
        else if( isset($config['layout']) )
        {
            $layoutName = $config['layout'];
        }
        else
        {
            $layoutName = 'default';
        }
        $this->templateName = isset($config['template']) ? $templateName : $template = 'default';
        $this->ext = isset($config['viewFilesExtension']) ? $config['viewFilesExtension'] : '.phtml';
        $layoutFile = $this->viewPath.DIRSEP.$this->templateName.DIRSEP.'layout'.DIRSEP.$layoutName.$this->ext;
        $this->viewFile = (IS::app()->getTemplate() !== NULL) ? $this->viewPath.DIRSEP.$this->templateName.DIRSEP.IS::app()->getTemplate().$this->ext
            : $this->viewPath.DIRSEP.$this->templateName.DIRSEP.$this->controller.DIRSEP.$action.$this->ext;
        try
        {
            if(!file_exists($this->viewFile))
                throw new ViewException("View file $this->viewFile not exists", 5666);
            if(!file_exists($layoutFile))
                throw new ViewException("View file $layoutFile not exists", 5667);
        }
        catch(ViewException $e)
        {
            $e->invalidParams();exit(1);
        }
        require_once $layoutFile;
    }

    /**
     * file return template content
     * @return void
     */
    public function content()
    {
        // convert array kes to php variables
        if($this->params !==null)
        {
            foreach($this->params as $k=>$v)
            {
                $$k = $v;
            }
        }
        // include view file
        require_once $this->viewFile;
    }

    public function render($viewFile, $params=NULL)
    {
        $file =  $this->viewPath.DIRSEP.$this->templateName.DIRSEP.$this->controller.DIRSEP.$viewFile.$this->ext;
        if($params){
            foreach($params as $k=>$v)
            {
                $$k = $v;
            }
        }
        require $file;
    }

    /**
     * include css file to render page
     * @param $link string
     * @return string
     */
    public function css($link)
    {
        $templateName = IS::app()->get('templateName') ? IS::app()->get('templateName') : IS::app()->getConfig('config')['template'];
        $v= md5_file(ROOTDIR.DIRSEP.IS::app()->getConfig('config')['publicDir'].DIRSEP.$templateName.DIRSEP.$link);
        return '<link rel="stylesheet" type="text/css" href="'. $this->getSiteLink($link) .'?v='.$v.'">'."\r\n";
    }

    /**
     * include js file to render page
     * @param $link
     * @return string
     */
    public function js($link)
    {
        $templateName = IS::app()->get('templateName') ? IS::app()->get('templateName') : IS::app()->getConfig('config')['template'];
        $v= md5_file(ROOTDIR.DIRSEP.IS::app()->getConfig('config')['publicDir'].DIRSEP.$templateName.DIRSEP.$link);
        return '<script src="'. $this->getSiteLink($link) .'?v='.$v.'"></script>'."\r\n";
    }

    /**
     * get full link to site for access from web. For include css and js files to page
     * @param $link string
     * @param $fullLink bool
     * @return string
     */
    public function getSiteLink($link, $fullLink=FALSE)
    {
        $config = IS::app()->getConfig('config');
        $templateName = IS::app()->get('templateName') ? IS::app()->get('templateName') : $config['template'];
        IS::app()->request()->isHttps() ? $path = 'https://' : $path = 'http://';
        if(!$fullLink)
            return $path.$config['host'].DIRSEP.$templateName.DIRSEP.$link;
        else
            return $path.$config['host'].DIRSEP.$link;
    }

    /**
     * set page title
     * @param null $title
     * @return string
     */
    public function title($title=null)
    {
        $title ? $t = $title : $t = IS::app()->get('title');
        if(!$t){ return ''; }
        return SeoHelper::title($t);
    }

    /**
     * set page description
     * @param $description string|null
     * @return string
     */
    public function description($description=null)
    {
        $description ? $desc = $description : $desc = IS::app()->get('description');
        if(!$desc)
        {
            return SeoHelper::description('');
        }
        return SeoHelper::description($desc);
    }

    /**
     * begin html page and set doctype and accept language
     * HTML5
     * @return string
     */
    public function beginHtml()
    {
        IS::app()->get('language') ?  $lang = IS::app()->get('language') : $lang = 'en';
        return '<!DOCTYPE html>'."\r\n".'<html lang="'.$lang.'">'."\r\n";
    }

    /**
     * @return string
     * close HTML page
     */
    public function endHtml()
    {
        return '</html>';
    }

    /**
     * @return string
     * @param $filename
     * @param null|array $params
     */
    public function img($filename, $params=NULL)
    {
        $filename = is_file(ROOTDIR.DIRSEP.IS::app()->getConfig('config')['publicDir'].DIRSEP.$filename) ? $filename : 'images/nophoto.jpg';
        $str = '<img src="'. $this->getSiteLink($filename, true) .'" ';
        if($params && is_array($params)) {
            foreach($params as $k=>$v){
                $str.=$k.'="'. $v .'" ';
            }
        }
        $str.='/>';
        return $str."\r\n";
    }
}
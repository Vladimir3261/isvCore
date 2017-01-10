<?php
/**
 * Created by PhpStorm.
 * User: vladimir
 * Date: 20.07.16
 * Time: 17:09
 */

namespace isv\Controller;
use isv\IS;

/**
 * Class AdminController
 * @package isv\Controller
 */
abstract  class AdminController extends ControllerBase
{
    public function __construct(array $arr)
    {
        parent::__construct($arr);
        $conf = IS::app()->getConfig('config');
        $layout = isset($conf['adminTemplate']) ?  $conf['adminTemplate'] : 'admin';
        IS::app()->set('layout', $layout);
        if(!IS::app()->user()->isAuth() && IS::app()->get('action') !== 'loginAction')
        {
            $this->redirect('/admin/login');exit(1);
        }
    }

    public function indexAction()
    {
        // TODO: Implement indexAction() method.
    }
}
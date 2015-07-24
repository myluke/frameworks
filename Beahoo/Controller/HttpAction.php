<?php

namespace Beahoo\Controller;

/**
 * 所有Http相关Action的父类
 */
abstract class HttpAction extends \Beahoo\Controller\Action
{
    protected $decorators = array(
        'Beahoo\Controller\Decorator\AuthDecorator' => 'auth',
        'Beahoo\Controller\Decorator\PrivilegeDecorator' => 'privilege',
    );

    protected $_viewPath;

    protected $csslist = array();

    protected $jslist = array();

    public function __construct()
    {
        $this->_viewPath = BEAHOO_ROOT . '/View/';
        
    }

    protected function setViewPath($path)
    {
        if( !empty($path) )
        {
            $this->_viewPath = $path;
        }
    }

    protected function assign($key,$value)
    {
        $this->$key = $value;
    }

    protected function addcss($css)
    {
        $this->csslist[] = $css;
    }

    protected function addjs($js)
    {
        $this->jslist[] = $js;
    }

    /**
     * @desc:渲染模版
     * $a = array('111','2222');
     * $this->assign('aa',$a);
     * $this->render('loginsuccess');
    */
    protected function render($template)
    {
        $template_file = $this->_viewPath.$template.'.phtml';
        if( !is_file($template_file) )
        {
            throw new \Exception($template_file." is not exist!");
        }

        ob_start();
        include $template_file;
        echo ob_get_clean();
        exit;
    }

    protected function display($template)
    {
        $template_file = $this->_viewPath.$template.'.phtml';
        if( !is_file($template_file) )
        {
            throw new \Exception($template_file." is not exist!");
        }
        include $template_file;
    }
}

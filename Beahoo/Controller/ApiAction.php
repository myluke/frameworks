<?php
namespace Beahoo\Controller;
/**
 * 所有不需要验证相关Action的父类
 *
 * @package Beahoo\Controller
 */
abstract class ApiAction extends Action
{
    protected $_viewPath;

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

    /**
     * @desc:渲染变量到模版,目前仅考虑最简单的渲染变量到类中供页面使用,在模版中通过$this->key 使用
    */
    protected function assign($key,$value)
    {
        $this->$key = $value;
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

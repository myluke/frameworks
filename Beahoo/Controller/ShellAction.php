<?php

namespace Beahoo\Controller;

/**
 * 所有Shell相关Action的父类
 */
abstract class ShellAction extends \Beahoo\Controller\Action
{
    /**
     * 装饰器
     *
     * @var array $decorators
     */
    protected $decorators = array(
        'Beahoo\Controller\Decorator\LogDecorator' => 'Log',
    );
}

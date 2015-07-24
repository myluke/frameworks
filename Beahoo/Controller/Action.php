<?php

namespace Beahoo\Controller;

abstract class Action
{
    protected $decorators = array();

    protected $resetdecoraters = array();

    public function build(Action $action = null)
    {
        if ($action === null) {
            $action = $this;
        }

        $decorators = array();

        for (
            $class = get_class($action);
            $vars  = get_class_vars($class);
            $class = get_parent_class($class)) {

            if (!empty($vars['resetdecorators']))
            {
                $vars['decorators'] = $vars['resetdecorators'];
            }

            if (empty($vars['decorators'])) {
                continue;
            }

            foreach ($vars['decorators'] as $decorator => $name) {
                if (is_int($decorator)) {
                    list($decorator, $name) = array($name, null);
                }

                if (!isset($decorators[$decorator])) {
                    $decorators[$decorator] = $name;
                }
            }

            if(!empty($vars['resetdecorators']))
            {
                break;
            }
        }

        $core = $action;

        $decorators = array_reverse($decorators);

        foreach ($decorators as $decorator => $name) {
            if ($name === false) {
                continue;
            }

            $decorator = $this->load($decorator, $action);
            $action = $this->build($decorator);

            if ($name) {
                $core->{$name} = $decorator;
            }
        }

        return $action;
    }

    protected function load($class, $args = null)
    {
        if (!class_exists($class)) {
            throw new \Beahoo\Exception\ClassNotFoundException($class);
        }

        if ($args === null) {
            return new $class;
        }

        if (!is_array($args)) {
            $args = array($args);
        }

        $reflection = new \ReflectionClass($class);

        return $reflection->newInstanceArgs($args);
    }

    abstract public function execute(Request $request, Response $response);
}

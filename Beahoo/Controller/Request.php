<?php

namespace Beahoo\Controller;

abstract class Request
{
    protected $serverArgs;

    public function getServerArgs()
    {
        return $this->serverArgs;
    }

    public function setServerArgs(array $value)
    {
        $this->serverArgs = $value;
    }

    public function getServerArg($name, $default = null)
    {
        return $this->getArgFromArgs($name, $default, $this->serverArgs);
    }

    public function setServerArg($name, $value)
    {
        $this->serverArgs[$name] = $value;
    }

    public function hasServerArg($name)
    {
        return isset($this->serverArgs[$name]);
    }

    protected function getArgFromArgs($name, $default, array $args)
    {
        if (!isset($args[$name])) {
            return $default;
        }

        switch ($type = gettype($default)) {
            case 'array':
            case 'boolean':
            case 'double':
            case 'integer':
            case 'string':
                settype($args[$name], $type);
        }

        return $args[$name];
    }
}

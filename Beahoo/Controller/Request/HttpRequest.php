<?php

namespace Beahoo\Controller\Request;

class HttpRequest extends \Beahoo\Controller\Request
{
    const METHOD_OPTIONS = 'OPTIONS';
    const METHOD_GET     = 'GET';
    const METHOD_HEAD    = 'HEAD';
    const METHOD_POST    = 'POST';
    const METHOD_PUT     = 'PUT';
    const METHOD_DELETE  = 'DELETE';
    const METHOD_TRACE   = 'TRACE';
    const METHOD_CONNECT = 'CONNECT';

    protected $queryArgs = array();

    protected $formArgs = array();

    protected $cookieArgs = array();

    protected $fileArgs = array();

    public function getQueryArgs()
    {
        return $this->queryArgs;
    }

    public function setQueryArgs(array $value)
    {
        $this->queryArgs = $value;
    }

    public function getQueryArg($name, $default = null)
    {
        return $this->getArgFromArgs($name, $default, $this->queryArgs);
    }

    public function setQueryArg($name, $value)
    {
        $this->queryArgs[$name] = $value;
    }

    public function hasQueryArg($name)
    {
        return isset($this->queryArgs[$name]);
    }

    public function getFormArgs()
    {
        return $this->formArgs;
    }

    public function setFormArgs(array $value)
    {
        $this->formArgs = $value;
    }

    public function getFormArg($name, $default = null)
    {
        return $this->getArgFromArgs($name, $default, $this->formArgs);
    }

    public function setFormArg($name, $value)
    {
        $this->formArgs[$name] = $value;
    }

    public function hasFormArg($name)
    {
        return isset($this->formArgs[$name]);
    }

    public function getCookieArgs()
    {
        return $this->cookieArgs;
    }

    public function setCookieArgs(array $value)
    {
        $this->cookieArgs = $value;
    }

    public function getCookieArg($name, $default = null)
    {
        return $this->getArgFromArgs($name, $default, $this->cookieArgs);
    }

    public function setCookieArg($name, $value)
    {
        $this->cookieArgs[$name] = $value;
    }

    public function hasCookieArg($name)
    {
        return isset($this->cookieArgs[$name]);
    }

    public function getFileArgs()
    {
        return $this->fileArgs;
    }

    public function setFileArgs(array $value)
    {
        $this->fileArgs = $value;
    }

    public function getFileArg($name, $default = null)
    {
        return $this->getArgFromArgs($name, $default, $this->fileArgs);
    }

    public function setFileArg($name, $value)
    {
        $this->fileArgs[$name] = $value;
    }

    public function hasFileArg($name)
    {
        return isset($this->fileArgs[$name]);
    }

    public function getArg($name, $default = null)
    {
        if ($this->hasQueryArg($name)) {
            return $this->getQueryArg($name, $default);
        }

        if ($this->hasFormArg($name)) {
            return $this->getFormArg($name, $default);
        }

        if ($this->hasCookieArg($name)) {
            return $this->getCookieArg($name, $default);
        }

        return $this->getServerArg($name, $default);
    }

    public function getGPArgs()
    {
        return $this->queryArgs + $this->formArgs;
    }

    public function getMethod()
    {
        return $this->getServerArg('REQUEST_METHOD');
    }

    public function isOptions()
    {
        return $this->getMethod() == static::METHOD_OPTIONS;
    }

    public function isGet()
    {
        return $this->getMethod() == static::METHOD_GET;
    }

    public function isHead()
    {
        return $this->getMethod() == static::METHOD_HEAD;
    }

    public function isPost()
    {
        return $this->getMethod() == static::METHOD_POST;
    }

    public function isPut()
    {
        return $this->getMethod() == static::METHOD_PUT;
    }

    public function isDelete()
    {
        return $this->getMethod() == static::METHOD_DELETE;
    }

    public function isTrace()
    {
        return $this->getMethod() == static::METHOD_TRACE;
    }

    public function isConnect()
    {
        return $this->getMethod() == static::METHOD_CONNECT;
    }
    
    public function isAjax(){
        return $_SERVER["HTTP_X_REQUESTED_WITH"] == "XMLHttpRequest"; 
    }
}

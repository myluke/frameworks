<?php

namespace Beahoo\Controller\Decorator;

use Beahoo\Controller\Request;
use Beahoo\Controller\Response;
use Beahoo\Tool\Config;

/**
 * 路由装饰器
 */
class RouterDecorator extends \Beahoo\Controller\Decorator
{
    public $project     = '';
    public $controller  = '';
    public $actionName  = '';

    /**
     * 执行
     *
     * @param \Beahoo\Controller\Request $request
     * @param \Beahoo\Controller\Response $response
     *
     * @result void
     */
    public function execute(Request $request, Response $response)
    {
        $uri = $request->getServerArg('REQUEST_URI');
        $uri = ltrim(strstr($uri . '?', '?', true), '/');
        $this->project = $request->getServerArg('REQUEST_PROJECT');

        if ($action = $this->match($uri)) {
            $this->getLastAction()->setAction($action);
        }

        $request->setServerArg('project', $this->project);
        $request->setServerArg('controller', $this->controller);
        $request->setServerArg('action', $this->actionName);
        
        parent::execute($request, $response);
    }

    /**
     * 匹配
     *
     * @param string $uri
     *
     * @result string
     */
    protected function match($uri)
    {
        if(!empty($this->project))
        {
            $project = $this->project;
        }
        elseif(strpos($uri, '/')) {
            $project = substr($uri, 0, strpos($uri, '/'));
            $uri = substr($uri, strpos($uri, '/')+1);
        } else {
            $project = $uri;
            $uri = "";
        }
        
        if (!$project) {
            $project = Config::read('decorator.router.default.project');
        }
        $project       = ucfirst($project); 
        $this->project = strtolower($project);
        $prefix = Config::read('decorator.router.prefix.'.$project);
        if(empty($prefix))
        {
            $prefix = Config::read('decorator.router.default_prefix');
        }
        $prefix .= '\\' . $project;

        return $this->getClass($uri, $prefix);
    }

    /**
     * 获取action
     *
     * @param string $router
     *
     * @result string
     */
    protected function getClass($router, $prefix)
    {   
        $default = Config::read('decorator.router.default');

        $path = explode('/', ltrim($router, '/'));

        $controller = $this->controller = !empty($path[0]) ? $path[0] : strtolower($default['controller']);
        $action     = $this->actionName = !empty($path[1]) ? $path[1] : strtolower($default['action']);
        $controller = ucfirst($controller);
        $action     = ucfirst($action . 'Action');

        if (PHP_SAPI == 'cli') {
            return "{$prefix}\\{$controller}\\Shell\\{$action}";
        }

        return "{$prefix}\\{$controller}\\{$action}";
    }
}

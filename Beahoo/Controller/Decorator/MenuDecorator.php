<?php

namespace Beahoo\Controller\Decorator;

use Beahoo\Controller\Request;
use Beahoo\Controller\Response;

/**
 * 菜单装饰器
 */
class MenuDecorator extends \Beahoo\Controller\Decorator
{
    
    protected $decorators = array(
        'Beahoo\Controller\Decorator\PrivilegeDecorator' => 'privilege',
    );
    
    public $moduleName;
    public $controllerName;
    public $actionName;
    public $config;
    public $uri;

    public function getUserMenu()
    {   
        $resource = $this->getMenu();
        $userMenu = array();
        foreach ($resource as $keyModule => $keyModuleValue){
            $userSubMenu = array();
            if($this->checkModuleMenu($keyModule)){
                foreach ($keyModuleValue['submenu'] as $keyMenu => $keyMenuName){
                    if($this->checkSubMenu($keyMenu)){
                        if($this->checkSubMenuChecked($keyMenu)){
                            $userSubMenu[$keyMenu]['checked'] = 1;
                        }
                        $userSubMenu[$keyMenu]['name'] = $keyMenuName;
                    }
                }
                if(!empty($userSubMenu)){
                    $userMenu[$keyModule] = $keyModuleValue;
                    if($keyModule == $this->moduleName){
                        $userMenu[$keyModule]['checked'] = 1;
                    }
                    $userMenu[$keyModule]['submenu'] = $userSubMenu;
                }
            }
        }
        return $userMenu;
    }

    protected function checkModuleMenu($moduleName)
    {
        $privilege = $this->privilege;
        return $privilege::checkModule($moduleName);
    }

    protected function checkSubMenu($keyMenu)
    {
        $privilege = $this->privilege;
        $urlArr         = explode('/', $keyMenu);
        $moduleName     = isset($urlArr[0]) ? $urlArr[0] : $this->config['project'];
        $controllerName = isset($urlArr[1]) ? $urlArr[1] : $this->config['controller'];
        $actionName     = isset($urlArr[2]) ? $urlArr[2] : $this->config['action'];
        return $privilege::checkPrivilege($moduleName, $controllerName, $actionName);
    }

    protected function checkSubMenuChecked($keyMenu)
    {
        $tmpArr     = explode('/', $keyMenu);
        $count      = count($tmpArr);
        $module     = isset($tmpArr[0]) ? $tmpArr[0] : $this->config['project'];
        $controller = isset($tmpArr[1]) ? $tmpArr[1] : $this->config['controller'];
        $action     = isset($tmpArr[2]) ? $tmpArr[2] : $this->config['action'];
        if($count == 3){
            return (($module == $this->moduleName) && ($controller == $this->controllerName) && ($this->actionName == $action));
        }else{
            return (($module == $this->moduleName) && ($controller == $this->controllerName));
        }
        
    }

    protected function getMenu()
    {   
        return \Beahoo\Tool\Config::read('menu');
    }
    
    protected function getConfig()
    {
        return \Beahoo\Tool\Config::read('decorator.router.default');
    }

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
        $this->moduleName       = ucfirst($request->getServerArg('project'));
        $this->controllerName   = ucfirst($request->getServerArg('controller'));
        $this->actionName       = ucfirst($request->getServerArg('action'));
        $this->uri              = $request->getServerArg('PHP_SELF');
        $this->config           = $this->getConfig();
        parent::execute($request, $response);
    }
}

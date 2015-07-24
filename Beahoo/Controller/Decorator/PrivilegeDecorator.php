<?php

namespace Beahoo\Controller\Decorator;

use Beahoo\Controller\Decorator;
use Beahoo\Controller\Request;
use Beahoo\Controller\Response;
use Beahoo\Exception;
use Beahoo\Model\Role;
use Beahoo\Model\User;
use Beahoo\Model\User_role;
use Beahoo\Tool\FunTool;

/**
 * 权限装饰器
 *
 * @package K7659\Controller\Decorator
 */
class PrivilegeDecorator extends Decorator
{
    /**
     * getPrivilegeByConfig
     * 通过配置文件数组转换后得到权限
     * @param array $config
     * @return array
     */
    public static function getPrivilegeByConfig($config)
    {
        $privilege = array();
        foreach ($config as $key => $val) {
            $keyArr = explode('.', $key);
            if (isset($keyArr[0]) && isset($keyArr[1])) {
                if (isset($keyArr[2])) {
                    $privilege[$keyArr[0]][$keyArr[1]][] = $keyArr[2];
                } else {
                    $privilege[$keyArr[0]][$keyArr[1]] = $keyArr[1];
                }
            }
        }
        return $privilege;
    }

    /**
     * checkModule
     * 检查module是否有权限
     * @param string $category
     * @param string $action
     * @return bool
     */
    public static function checkModule($moduleName)
    {
        $userPrivilege = self::getUserPrivilege();
        return self::__checkModule($userPrivilege, $moduleName);
    }

    public static function __checkModule($userPrivilege, $moduleName)
    {
        return array_key_exists($moduleName, $userPrivilege);
    }

    /**
     * checkController
     * 检查controller是否有权限
     * @param string $category
     * @param string $action
     * @return bool
     */
    public static function checkController($moduleName, $controllerName)
    {
        $userPrivilege = self::getUserPrivilege();
        return self::__checkController($userPrivilege, $moduleName, $controllerName);
    }

    public static function __checkController($userPrivilege, $moduleName, $controllerName)
    {
        if (self::__checkModule($userPrivilege, $moduleName))
        {
            if (array_key_exists($controllerName, $userPrivilege[$moduleName]) || array_key_exists('_all', $userPrivilege[$moduleName]))
            {
                return TRUE;
            }
        }
        return FALSE;
    }

    /**
     * checkAction
     * 检查action是否有权限
     * @param string $category
     * @param string $action
     * @return bool
     */
    public static function checkPrivilege($moduleName, $controllerName, $actionName)
    {   
        $userPrivilege = self::getUserPrivilege();
        return self::_checkPrivilege($userPrivilege, $moduleName, $controllerName, $actionName);
    }

    public static function _checkPrivilege($userPrivilege, $moduleName, $controllerName, $actionName)
    {
        if (self::__checkModule($userPrivilege, $moduleName))
        {
            if (array_key_exists('_all', $userPrivilege[$moduleName]))
            {
                return true;
            }
            if (!empty($userPrivilege[$moduleName][$controllerName]))
            {
                if (in_array('_all', $userPrivilege[$moduleName][$controllerName]))
                {
                    return TRUE;
                }
                return in_array($actionName, $userPrivilege[$moduleName][$controllerName]);
            }
        }
        else
        {
            return false;
        }
    }

    public static function getResource()
    {
        return \Beahoo\Tool\Config::read('privilege');
    }

    /**
     * 执行
     *
     * @param \Beahoo\Controller\Request $request
     * @param \Beahoo\Controller\Response $response
     *
     * @result void
     */
    public static function getUserPrivilege()
    {
        $userPrivilege = array();
        // get roleId by userid
        $roleId = self::getUserRole();
        if(empty($roleId))
        {
            return $userPrivilege;
        }
        //get userPrivilege by roleId         
        $userPrivilege = self::_getPrivilege($roleId);
        if (empty($userPrivilege)) {
            return false;
        }
        return $userPrivilege;
    }

    public static function _getPrivilege($roleId)
    {
        $data = $tmp = array();
        $role = new Role();
        $sql = "SELECT privileges FROM role";
        $sql .= " WHERE enabled=1 AND id in ({$roleId})";
        $query  = $role->getDb()->query($sql);
        $info   = $query->fetchAll(\PDO::FETCH_ASSOC);

        if(empty($info)){
            return $data;
        }else{
            foreach ($info as $v){
                $tmp[] = FunTool::base_decode($v['privileges']);
            }
            foreach ($tmp as $t){
                $data = array_merge_recursive($t,$data);
            }
            $data['Dashboard']['Main'][] = 'Index';
            //$data['Statistics']['Main'][] = 'Index';
            $data['User']['User'][] = array('Login','Logout');
            return $data;
        }
    }

    /*
     * 或许用户所属权限
     */
    public static function getUserRole()
    {
        $roleId     = '';
        $username   = $_SESSION['username'];

        $user       = new User();
        $role       = new User_role();

        $userinfo   = $user->getUserInfoByAttr(array('username'=>$username));
        if(empty($userinfo))
        {
            return $roleId;
        }
        $uid        = $userinfo[0]['id'];
        $rs         = $role->getUserRole($uid);
        if(empty($rs)){
            return $roleId;
        }
        $tmp = array();
        foreach ($rs as $val){
            $tmp[] = $val['role_id'];
        }
        $roleId = implode(',', $tmp);
        return $roleId;
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
        $moduleName         = ucfirst($request->getServerArg('project'));
        $controllerName     = ucfirst($request->getServerArg('controller'));
        $actionName         = ucfirst($request->getServerArg('action'));

        if (!self::checkPrivilege($moduleName, $controllerName, $actionName))
        {
            throw new Exception('您没有访问权限', 403);
        }
        parent::execute($request, $response);
    }
}

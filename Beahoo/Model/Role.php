<?php

namespace Beahoo\Model;

use Beahoo\Tool\FunTool;

class Role extends Base
{
    
    protected $dbname = 'mysql';
    
    /**
     * 获取角色列表      
     */
    public function getRoleList($all = false)
    {
        $sql    = "SELECT * FROM role WHERE 1=1";
        if(!$all){
            $sql .= " AND enabled = 1";
        }
        $sql .=" ORDER BY id ASC";
        $query  = $this->getDb()->query($sql);
        $rs     = $query->fetchAll(\PDO::FETCH_ASSOC);
        return $rs;
    }
    
    /**
     * 用户组信息By ID      
     */
    public function getRoleInfo($ids)
    {
        if(is_array($ids)){
            $condition = "id in (".implode($ids, ',').")";
        }else{
            $condition = "id={$ids}";
        }
        $sql    = "SELECT * FROM role WHERE {$condition}";
        $query  = $this->getDb()->query($sql); 
        $rs     = $query->fetchAll(\PDO::FETCH_ASSOC);
        return is_array($ids) ? $rs : $rs[0];
    }
    
    /**
     * 用户组总数      
     */
    public function getTotal($kw = ''){
        $sql    = "SELECT count(*) AS total FROM role where 1=1";
        if(!empty($kw)){
            $sql .=" and name like '{$kw}%'";
        }
        $query  = $this->getDb()->query($sql);
        $rs     = $query->fetch(\PDO::FETCH_ASSOC);
        return $rs['total'];
    }

    /**
     * 用户组添加      
     */
    public function add($data){
        $addtime    = time();
        $privileges = FunTool::base_encode($data['resource']);
        $sql = "INSERT INTO role (name,privileges,sign,addtime)";
        $sql .=" value ('{$data['rolename']}','{$privileges}','{$data['sign']}',{$addtime})";
        $this->getDb()->exec($sql);
        $id = $this->getDb()->lastInsertId();
        return $id;
    }
    
    /**
     * 用户组修改      
     */
    public function modify($rid,$data)
    {
        $condition = '';
        $privileges = FunTool::base_encode($data['resource']);
        $condition .= "name='{$data['rolename']}',privileges='{$privileges}',sign='{$data['sign']}',enabled={$data['enabled']} WHERE id={$rid}";
        $sql = "UPDATE role SET {$condition}";
        $this->getDb()->exec($sql);
    }
    
    /**
     * 用户组删除      
     */
    public function del($rid){
        $sql = "DELETE FROM role WHERE id={$rid}";
        return $this->getDb()->exec($sql);
    }
    
}
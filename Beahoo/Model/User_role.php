<?php

namespace Beahoo\Model;

class User_role extends Base
{
    
    protected $dbname = 'mysql';
    
    /**
     * 获取列表      
     */
    public function getUserRoleList()
    {
        $sql    = "SELECT * FROM user_role";
        $query  = $this->getDb()->query($sql);
        $rs     = $query->fetchAll(\PDO::FETCH_ASSOC);
        return $rs;
    }
    
    public function getUserRole($ids)
    {
        if(is_array($ids)){
            $condition = "user_id in (".implode($ids, ',').")";
        }else{
            $condition = "user_id = {$ids}";
        }
        $sql    = "SELECT * FROM user_role WHERE {$condition}";
        $query  = $this->getDb()->query($sql);
        $rs     = $query->fetchAll(\PDO::FETCH_ASSOC);
        return $rs;
    }

    /**
     * 添加信息     
     */
    public function add($data){
        $sql = "INSERT INTO user_role (user_id,role_id)";
        $sql .=" value ({$data['user_id']},{$data['role_id']})";
        $this->getDb()->exec($sql);
    }
    
    /**
     * 用户组修改      
     */
    public function modify($data){
        $this->del($data['user_id']);
        $this->add($data);
    }
    
    /**
     * 用户组删除      
     */
    public function del($uid){
        $sql = "DELETE FROM user_role WHERE user_id={$uid}";
        return $this->getDb()->exec($sql);
    }
    
}
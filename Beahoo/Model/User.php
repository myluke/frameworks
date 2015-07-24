<?php

namespace Beahoo\Model;

use Beahoo\Tool\FunTool;

class User extends Base
{
    
    protected $dbname = 'mysql_user';

    /**
     * 用户信息By ID      
     */
    public function getUserInfo($ids)
    {
        if(is_array($ids)){
            $condition = "id in (".implode($ids, ',').")";
        }else{
            $condition = "id={$ids}";
        }
        $sql    = "SELECT * FROM user_list WHERE {$condition}";
        $query  = $this->getDb()->query($sql); 
        $rs     = $query->fetchAll(\PDO::FETCH_ASSOC);
        return is_array($ids) ? $rs : $rs[0];
    }
    
    /**
     * 用户信息By ID
     * $data 为属性和值 键值对     
     */
    public function getUserInfoByAttr($data)
    {
        if(empty($data)) return FALSE;
        $condition = '';
        foreach ($data as $k => $v){
            $condition .=" and {$k}='{$v}'";
        }
        $sql = "SELECT * FROM user_list WHERE 1=1 {$condition}";
        $query  = $this->getDb()->query($sql); 
        $rs     = $query->fetchAll(\PDO::FETCH_ASSOC);
        return $rs;
    }
    
    /**
     * 用户总数      
     */
    public function getTotal($kw = ''){
        $sql    = "SELECT count(*) AS total FROM user_list where 1=1";
        if(!empty($kw)){
            $sql .=" and (username like '{$kw}%' or email like '{$kw}%')";
        }
        $query  = $this->getDb()->query($sql);
        $rs     = $query->fetch(\PDO::FETCH_ASSOC);
        return $rs['total'];
    }

    /**
     * 用户添加      
     */
    public function add($data){
        $addtime    = time();
        $sql = "INSERT INTO user_list (username,sign,addtime,email)";
        $sql .=" value ('{$data['username']}','{$data['sign']}',{$addtime},'{$data['username']}@kuaiyong.net')";
        $this->getDb()->exec($sql);
        $id = $this->getDb()->lastInsertId();
        return $id;
    }
    
    /**
     * 用户修改      
     */
    public function modify($uid,$data)
    {
        $condition = "sign='{$data['sign']}' WHERE id={$uid}";
        $sql = "UPDATE user_list SET {$condition}";
        $this->getDb()->exec($sql);
    }
    
    /**
     * 用户删除      
     */
    public function del($uid){
        $sql = "DELETE FROM user_list WHERE id={$uid}";
        return $this->getDb()->exec($sql);
    }

    public static function getUser()
    {
        $user = array(
            'uid'=>$_SESSION['uid'],
            'username'=>$_SESSION['username'],
            'email'=>$_SESSION['email'],
            'tel'=>$_SESSION['tel'],
            'addtime'=>$_SESSION['addtime'],
        );
        return $user;
    }
    
}
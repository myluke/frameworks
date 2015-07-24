<?php

/**
 * Description of FunTool
 *
 * @author hepenghui
 */
namespace Beahoo\Tool;

class FunTool {
    
    /*
     * 加密算法
     */
    public static function hash($pwd){
        return md5(trim($pwd));
    }
    
    /**
     * base_decode
     * base64解码函数
     * @param $str
     * @return array
     */
    public static function base_decode($str)
    {
        return unserialize(base64_decode($str));
    }
    
    /**
     * base_encode
     * base64编码函数
     * @param array $array
     * @return string
     */
    public static function base_encode($array){
        return base64_encode(serialize($array));
    }
    
   
    /**      
     * 重定向浏览器到指定的 URL      
     *      
     * @param string $url 要重定向的 url      
     * @param int $delay 等待多少秒以后跳转      
     * @param bool $js 指示是否返回用于跳转的 JavaScript 代码      
     * @param bool $jsWrapped 指示返回 JavaScript 代码时是否使用 <mce:script type="text/javascript"><!-- 
     标签进行包装      
     * @param bool $return 指示是否返回生成的 JavaScript 代码      
     */        
    public static function redirect($url, $delay = 0, $js = false, $jsWrapped = true, $return = false)         
    {         
        $delay = (int)$delay;         
        if (!$js) {         
            if (headers_sent() || $delay > 0) {         
                echo "<<<EOT         
                        <html>         
                        <head>         
                        <meta http-equiv='refresh' content='{$delay};URL={$url}' />         
                        </head>         
                        </html>         
                    EOT";         
                exit;         
            } else {         
                header("Location: {$url}");         
                exit;         
            }         
        }         

        $out = '';         
        if ($jsWrapped) {         
            $out .= '<script language="JavaScript" type="text/javascript">';         
        }         
        $url = rawurlencode($url);         
        if ($delay > 0) {         
            $out .= "window.setTimeOut(function () { document.location='{$url}'; }, {$delay});";         
        } else {         
            $out .= "document.location='{$url}';";         
        }         
        if ($jsWrapped) {         
            $out .= '  
    // --></mce:script>';         
        }         

        if ($return) {         
            return $out;         
        }         

        echo $out;
        exit;
    }       

    /**
     * 
     * @param string $email 要验证的邮箱
     * @return  bool    
     */
    public static function checkEmail($email)
    {
        $chars = "/^([a-z0-9+_]|\\-|\\.)+@(([a-z0-9_]|\\-)+\\.)+[a-z]{2,6}\$/i";
        if (strpos($email, '@') !== false && strpos($email, '.') !== false){
            if (preg_match($chars, $email)){
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    /**
     *计算文件大小
     */
    public static function formatBytes($bytes)
    {
        if($bytes >= 1073741824){
            $bytes = round($bytes/1073741824 * 100) /100 ."GB";
        }else if($bytes >= 1048576){
            $bytes = round($bytes/1048576 * 100) /100 ."MB";
        }else if($bytes >= 1024){
            $bytes = round($bytes/1024 * 100) /100 ."KB";
        }else{
            $bytes .= 'B';
        }
        return $bytes;
    }
    
     /**
     * 
     * @param 合并平台和下载链接
     * @return  array    
     */
    public static function merge_platform_downurl($platform,$downurl)
    {
        $return = array();
        if(!empty($platform) && $downurl)
        {
            foreach ($platform as $k=>$v)
            {
                $return[$k]['platform'] = $v;
                $return[$k]['downloadUrl']  = $downurl[$k];
            }
        }
        return $return;
    }
}

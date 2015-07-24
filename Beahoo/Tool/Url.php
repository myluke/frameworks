<?php

namespace Beahoo\Tool;

class Url
{
    public static function getCurentUrl($newparams = array(), $unsetkeys = array())
    {
        $params = $_GET;
        if(!empty($unsetkeys))
        {
            $params = array_diff_key($params, array_flip($unsetkeys));
        }
        $params = array_merge($params, $newparams);
        return $_SERVER['SCRIPT_URL'] . "?" . http_build_query($params);
    }
}
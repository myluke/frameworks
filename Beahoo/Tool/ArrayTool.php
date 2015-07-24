<?php

namespace Beahoo\Tool;

class ArrayTool
{
    public static function sortByField($arr, $field, $fieldValues)
    {
        $fieldValues = array_flip($fieldValues);
        foreach($arr as $item)
        {
            $weight[] = $fieldValues[$item[$field]];
        }
        array_multisort($weight, SORT_ASC, $arr);
        return $arr;
    }

    public static function assocSlice($arr, $start, $count)
    {
        if(empty($arr))
        {
            return array();
        }

        $keys = array_slice(array_keys($arr), $start, $count);
        $result = array();
        foreach($keys as $key)
        {
            $result[$key] = $arr[$key];
        }
        return $result;
    }

    public static function list2Map($arr, $kField = null, $vField = null)
    {
        if (!is_array($arr) || empty($arr))
        {
            return array();
        }

        $ret = array();

        if($kField === null)
        {
            foreach($arr as $v)
            {
                $ret[$v] = 1;
            }
            return $ret;
        }

        if($vField === null)
        {
            foreach ($arr as $v)
            {
                $ret[$v[$kField]] = $v;
            }
        }
        else
        {
            foreach ($arr as $v)
            {
                $ret[$v[$kField]] = $v[$vField];
            }
        }
        return $ret;
    }

    public static function getFields($objs, $key)
    {
        if(empty($objs))
        {
            return array();
        }

        $ids = array();
        if (is_array($objs))
        {
            foreach($objs as $obj)
            {
                if (is_array($obj))
                {
                    $ids[] = $obj[$key];
                }
                else if (is_object($obj))
                {
                    $ids[] = $obj->$key;
                }
                else
                {
                    $ids[] = $obj;
                }
            }
        }
        return $ids;
    }
}
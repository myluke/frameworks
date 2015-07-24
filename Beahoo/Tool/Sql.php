<?php
namespace Beahoo\Tool;

/**
 * sql语句拼装
 */

class Sql
{
    /**
     * 插入单条数据
     *  1.数据未插入时，插入数据
     *  2.数据已经插入时，更新数据
     *
     * @param updateField 如果命中唯一键,要更新的字段,例如 array('mtime', 'score')
     * @param changeField 如果命中唯一键,要增减的字段,例如 array('score' => 10)
     */
    public static function insert($table, $insertfield, $updateField = array(), $changeField = array(), $notEscFields=array())
    {
        $sql = "insert into $table (";

        $flag = 0;
        foreach($insertfield as $k => $v)
        {
            $sql .= ($flag==0?"":", ").$k;
            $flag = 1;
        }

        $sql .= ") values(";
        $flag = 0;
        foreach($insertfield as $k => $v)
        {
            $v = (empty($notEscFields) || !in_array($k,$notEscFields)) ? ("'".$v."'") : $v;
            $sql .= ($flag==0?"":", "). $v;
            $flag = 1;
        }
        $sql .= ")";

        if (!empty($updateField) || !empty($changeField))
        {
            $sql .= " ON DUPLICATE KEY UPDATE ";
            $flag = 0;
            foreach ($updateField as $k)
            {
                $v = (empty($notEscFields) || !in_array($k,$notEscFields)) ? ("'".$insertfield[$k]."'") : $insertfield[$k];
                $sql .= ($flag==0?"":",").$k." = ".$v;
                $flag = 1;
            }

            foreach ($changeField as $k => $v)
            {
                if (is_numeric($v))
                {
                    $sql .= ($flag==0?"":",").$k."=".$k.($v >=  0 ? "+" : "").($v);
                }
                elseif (is_array($v))
                {
                    $sql .= ($flag==0?"":",").$k."=".$v['sql'];
                }
                else
                {
                    $sql .= ($flag==0?"":",").$k."=CONCAT(".$k.",'".$v."')";
                }
                $flag = 1;
            }
        }
        return $sql;
    }

    /**
     * 插入多条数据
     *
     * @param updateField 如果命中唯一键,要更新的字段,例如 array('mtime', 'score')
     * @param changeField 如果命中唯一键,要增减的字段,例如 array('score' => 10)
     */
    public static function batchInsert($table, $insertFieldList, $updateField = array(), $changeField = array(), $notEscFields=array())
    {
        $tmpArr = $insertFieldList[0];

        $sql = "insert into $table (";
        $flag = 0;
        foreach($tmpArr as $k => $v)
        {
            $sql .= ($flag==0?"":", ").$k;
            $flag = 1;
        }
        $sql .= ") values";
        foreach ($insertFieldList as $varr)
        {
            $flag = 0;
            $sql.="(";
            foreach($varr as $k => $v)
            {
                $v = (empty($notEscFields) || !in_array($k,$notEscFields)) ? ("'".$v."'") : $v;
                $sql .= ($flag==0?"":", ").$v;
                $flag = 1;
            }
            $sql .= "),";
        }
        $sql = trim($sql,' ,');

        if (!empty($updateField) || !empty($changeField))
        {
            $sql .= " ON DUPLICATE KEY UPDATE ";
            $flag = 0;
            foreach ($updateField as $k)
            {
                $sql .= ($flag==0?"":", ").$k." = VALUES(".$k.")";
                $flag = 1;
            }

            foreach ($changeField as $k => $v)
            {
                if (is_numeric($v))
                {
                    $sql .= ($flag==0?"":", ").$k."=".$k.($v >= 0 ? "+" : "").($v);
                }
                elseif (is_array($v))
                {
                    $sql .= $v['sql'];
                }
                else
                {
                    $sql .= ($flag==0?"":", ").$k."=CONCAT(".$k.",'".$v."')";
                }
                $flag = 1;
            }
        }

        return $sql;
    }

    /**
     * 删除
     */
    public static function delete($table, $where)
    {
        $where = self::where($where);

        if(empty($where))
        {
            //没有限制条件，不能删除，避免灾难
            return "";
        }

        $sql = "delete from ".$table." where ".$where;
        return $sql;
    }

    /**
     * 更新
     */
    public static function update($table, $updateField, $changeField = array(), $where, $notEscFields=array())
    {
        $sql = "update $table set ";
        $flag = 0;
        foreach($updateField as $k => $v)
        {
            $v = (empty($notEscFields) || !in_array($k,$notEscFields)) ? ("'".$v."'") : $v;
            $sql .= ($flag==0?"":", ").$k." = ".$v;
            $flag = 1;
        }
        foreach($changeField as $k => $v)
        {
            if (is_numeric($v))
            {
                $sql .= sprintf("%s %s=%s%s%s", ($flag==0 ? "" : ","),
                    $k, $k, ($v >= 0 ? "+" : " "), $v);
                $flag = 1;
            }
            else
            {
                $sql .= sprintf("%s %s=CONCAT(%s,'%s')", ($flag==0 ? "" : ","),
                    $k, $k, $v);
                $flag = 1;
            }
        }

        $where = self::where($where);

        if(empty($where))
        {
            //没有限制条件，不能批量更新，避免灾难
            assert(0);
        }
        $sql .= " where ".$where;

        return $sql;
    }

    /**
     * 查找
     */
    public static function select($table, $selectField, $where, $order = '', $start = 0, $num = 0, $forceMaster = false, $forceIndex = '')
    {
        if (is_array($selectField))
        {
            $select = implode(",", $selectField);
        }
        else if (empty($selectField))
        {
            $select = '*';
        }
        else
        {
            $select = $selectField;
        }

        $where = self::where($where);

//		if($foundRows)
//		{
//			$sql = "select SQL_CALC_FOUND_ROWS ".$select." from ".$table;
//		}
//		else
//		{
//			$sql = "select ".$select." from ".$table;
//		}

        $sql = "select ".$select." from ".$table;

        if(!empty($forceIndex))
        {
            $sql .= ' ' . $forceIndex;
        }

        if(0 != strlen($where))
        {
            $sql .= " where ".$where;
        }
        $sql .= " ".$order;

        if($num != 0)
        {
            $sql .= " limit ".intval($start).", ".intval($num);
        }

        if($forceMaster)
        {
            $sql .= " /*master*/";
        }

        return $sql;
    }

    public static function loadData($table, $file, $fields, $fieldSeparator, $lineSeparator)
    {
        return "LOAD DATA LOCAL INFILE '${file}' INTO TABLE `${table}` FIELDS TERMINATED BY '${fieldSeparator}' LINES TERMINATED BY '${lineSeparator}' (${fields})";
    }

    public static function exportData($dbConfig, $table, $selectField, $where, $fileName)
    {
        if(is_array($selectField))
        {
            $selectField = implode(',', $selectField);
        }

        $where = self::where($where);
        if(empty($where))
        {
            $where = '1=1';
        }

        $cmd = sprintf("mysql -h%s -P%d -u'%s' -p'%s' -e \"use %s; select %s from %s where %s\" -s -N >> %s",
            $dbConfig['host'],
            $dbConfig['port'],
            $dbConfig['user'],
            $dbConfig['password'],
            $dbConfig['dbname'],
            $selectField,
            $table,
            $where,
            $fileName);

        return $cmd;
    }

    public static function where($where)
    {
        if (is_array($where) && !empty($where))
        {
            $whereStr = "";
            foreach($where as $k=>$v)
            {
                if(0 != strlen($whereStr))
                {
                    $whereStr .= " and ";
                }
                if(is_array($v))
                {
                    foreach($v as $i=>$item)
                    {
                        $v[$i] = $item;
                    }
                    $whereStr .= $k." in ('". implode("', '", $v) ."')";
                }
                else
                {
                    $whereStr .= $k." = '".$v."'";
                }
            }
            $where = $whereStr;
        }
        if (empty($where))
        {
            $where = "";
        }
        return $where;
    }
}
?>
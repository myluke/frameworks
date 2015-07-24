<?php

namespace Beahoo\Tool;

/**
 * 数据库CURD
 */
class One
{
    /**
     * @var \PDO
     */
    private	$pdo;

	public function	__construct($pdo)
	{
        $this->pdo = $pdo;
	}

    public function setPdo($pdo)
    {
        $this->pdo = $pdo;
    }

	public function	insert($kind, $insertField , $updateField =	array(), $changeField =	array(), $notEscFields=array())
	{
		if (empty($insertField))
        {
            return false;
        }
		$sql = Sql::insert($kind, $insertField, $updateField, $changeField, $notEscFields);
        return $this->sQuery($sql);
	}

	public function batchInsert($kind, $insertField, $updateField = array(), $changeField = array(), $notEscFields=array())
	{
		$sql = Sql::batchInsert($kind,	$insertField, $updateField, $changeField, $notEscFields);
        return $this->sQuery($sql);
	}

	public function	delete($kind , $where)
	{
		if(is_array($where))
		{
			$sql = Sql::delete($kind, $where);
		}
		else if(is_string($where))
		{
			if(strlen($where) == 0)
			{
				return false;   //不允许删除所有
			}
			$sql = "delete from	".$kind." where	".$where;
		}
		else
		{
            return false;
		}
        return $this->sQuery($sql);
	}

	public function	update($kind, $updateField, $changeField = array() , $where, $notEscFields=array())
	{
		$sql = Sql::update($kind ,	$updateField , $changeField	, $where, $notEscFields);
		if(0 ==	strlen($sql))
		{
			return false;
		}
        return $this->sQuery($sql);
	}

	public function	select($kind, $selectField, $whereField=array(), $order='', $start=0, $num=0, $cacheTime=0, $forceMaster=false, $forceIndex='')
	{
		$sql = Sql::select($kind, $selectField, $whereField, $order, $start, $num, $forceMaster, $forceIndex);
        return $this->sQuery($sql);
	}

    public function exec($sql)
    {
        return $this->sQuery($sql);
    }

    private function sQuery($sql)
    {
        $pstmt = $this->pdo->prepare($sql);
        $res = $pstmt->execute();
        if(false === $res)
        {
            throw new \Exception("excute '$sql' error - " . $pstmt->errorCode() .":". $pstmt->errorInfo(), $pstmt->errorCode());
        }

        $data =  $pstmt->columnCount() ? $pstmt->fetchAll(\PDO::FETCH_ASSOC) : array();
        return new Dbresult($data, count($data), $this->pdo->lastInsertId(), $pstmt->rowCount());
    }
}

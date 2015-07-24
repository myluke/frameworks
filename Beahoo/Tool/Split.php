<?php

namespace Beahoo\Tool;

/**
 * 分表数据库CURD
 */
class Split
{
	private	$pdo;

	public function	__construct($pdo)
	{
        $this->pdo = $pdo;
	}

	public function	insert($kind, $splitId,	$insertField, $updateField = array(), $changeField = array())
	{
		if (empty($insertField))
        {
            return false;
        }
		$sql = Sql::insert($kind, $insertField, $updateField, $changeField);
        return $this->sQuery($sql, $kind, $splitId);
	}
	
	public function batchInsert($kind, $splitId, $insertField, $updateField = array(), $changeField = array())
	{
		$sql = Sql::batchInsert($kind, $insertField, $updateField, $changeField);
        return $this->sQuery($sql, $kind, $splitId);
	}

	public function	delete($kind, $splitId,	$where)
	{
		if (is_array($where))
		{
			$sql = Sql::delete($kind, $where);
		}
		elseif (is_string($where))
		{
			if(0 ==	strlen($where))
			{
				return false;
			}
			$sql = 'delete from	' . $kind . ' where	' . $where;
		}
		else
		{
			return false;
		}

		if(0 ==	strlen($sql))
		{
			return false;
		}
        return $this->sQuery($sql, $kind, $splitId);
	}

	public function	update($kind, $splitId,	$updateField, $changeField = array(), $whereField =	array())
	{
		$sql = Sql::update($kind, $updateField, $changeField, $whereField);
		if(0 ==	strlen($sql))
		{
			return false;
		}
        return $this->sQuery($sql, $kind, $splitId);
	}

	public function select($kind, $splitId,	$selectField, $where = NULL, $order	= "", $start=0,	$num=0,	$cacheTime=0, $forceMaster=false)
	{
		$sql = Sql::select($kind, $selectField, $where, $order, $start, $num, $forceMaster);
        return $this->sQuery($sql, $kind, $splitId);
	}

    private function sQuery($sql, $kind, $splitId)
    {
        $sql = preg_replace("/$kind/", $kind . '_' . ($splitId % Config::get('split-table', $kind)), $sql, 1);

        $pstmt = $this->pdo->prepare($sql);
        $res = $pstmt->execute();
        if(false === $res)
        {
            throw new \Exception("excute '$sql' error - " . $pstmt->errorCode() .":". $pstmt->errorInfo(), $pstmt->errorCode());
        }

        $data =  $pstmt->columnCount() ? $pstmt->fetchAll(PDO::FETCH_ASSOC) : array();
        return new Dbresult($data, count($data), $this->pdo->lastInsertId(), $pstmt->rowCount());
    }
}

<?php

namespace Beahoo\Model;

use Beahoo\Exception;
use Beahoo\Tool\Config;
use Beahoo\Tool\One;

abstract class Base
{
    /**
     * 实例
     *
     * @var $instance array
     */
    protected static $instances;

    protected $dbname = 'mysql';
    /**
     * 创建连接
     *
     * @return \PDO
     */
    public function getDb()
    {
        if (isset(static::$instances[$this->dbname])) {
            return static::$instances[$this->dbname];
        }

        $config = Config::read($this->dbname);
        if(empty($config))
        {
            throw new Exception("no " . $this->dbname . " conf");
        }

        $dsn = 'mysql:' . implode(';', array(
                'host='    . $config['host'],
                'port='    . $config['port'],
                'dbname='  . $config['dbname'],
                'charset=' . $config['charset'],
            ));

        $options = array(
            \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES {$config['charset']}",
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
        );

        static::$instances[$this->dbname] = new \PDO(
            $dsn, $config['username'], $config['password'], $options
        );

        return static::$instances[$this->dbname];
    }

    /**
     * @var \Beahoo\Tool\One
     */
    private static $one;

    public function getOne()
    {
        $pdo = $this->getDb(true);
        if(isset(self::$one))
        {
            self::$one->setPdo($pdo);
        }
        else
        {
            self::$one = new One($pdo);
        }
        return self::$one;
    }
}
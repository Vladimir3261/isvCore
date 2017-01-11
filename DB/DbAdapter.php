<?php
/**
 * Created by PhpStorm.
 * User: vladimir
 * Date: 23.05.16
 * Time: 15:09
 */

namespace isv\DB;

use isv\Exception\DBException;
use isv\IS;
use PDO;
use PDOException;

/**
 * @version 1.0
 * Class DbAdapter
 * @package isv\Db
 */
class DbAdapter
{
    /**
     * Database configuration array
     * @var array|null
     */
    private $config;

    /**
     * Database adapter instance
     * @var $Adapter
     */
    private $Adapter;

    /**
     * Set current strategy (select database engine)
     * DbAdapter constructor.
     * @param string $strategy
     */
    public function __construct($strategy='MYSQL')
    {
        if(IS::app()->get($strategy)){
            $this->Adapter = IS::app()->get($strategy);
        }else{
            $this->config = IS::app()->getConfig('db');
            $this->Adapter = $this->$strategy();
            IS::app()->set($strategy, $this->Adapter);
        }
    }

    /**
     * Get created adapter
     * @return PDO
     */
    public function Adapter()
    {
        return $this->Adapter;
    }
    public function MYSQL()
    {
        try
        {
            if( !isset($this->config['MYSQL']) )
                throw new DBException('MySQL driver for PDO not configured', 998);
        }
        catch(DBException $e)
        {
            $e->dbError();exit(1);
        }
        $host = $this->config['MYSQL']['host'];
        $db = $this->config['MYSQL']['dbname'];
        $charset = $this->config['MYSQL']['charset'];
        $dsn = $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
        $opt = array(
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        );
        try
        {
            return  new PDO($dsn, $this->config['MYSQL']['user'], $this->config['MYSQL']['password'], $opt);
        }
        catch (PDOException $PDO)
        {
            throw new DBException($PDO->getMessage(), $PDO->getCode());
        }
    }

    public function MongoDB()
    {
        return false;
    }

    public function SQLLITE()
    {
        return false;
    }
}
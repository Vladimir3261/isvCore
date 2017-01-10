<?php
/**
 * Created by PhpStorm.
 * User: vladimir
 * Date: 23.05.16
 * Time: 13:00
 */

namespace isv\DB;
use PDO;

/**
 * Custom query builder
 * Class ISQuery
 * @package isv\Db
 */
class ISQuery implements ModelInterface
{
    /**
     * Current query created using methods of this class
     * @var array
     */
    private $query = [];

    private $table;

    private $Adapter = null;

    /**
     * @param $table string
     * Create new query
     */
    public function __construct($table)
    {
        $this->Adapter = $this->getAdapter();
        $this->table = $table;
        $this->query['sql'] = "SELECT * FROM $this->table ";
        return $this;
    }

    /**
     * add fields to query if fields not assigned this class will be return all records in DB
     * @param array $fields
     * @return $this
     */
    public function fields(array $fields)
    {
        $this->query['sql'] = str_replace('*', implode(', ',$fields), $this->query['sql']);
        return $this;
    }

    /**
     * Where statement params for sql query
     * @param array $where
     * @return $this
     */
    public function where(array $where)
    {
        $this->query['sql'] .= 'WHERE ';
        foreach($where as $field => $value)
        {
            $placeholder = str_replace('.', '', $field);
            if (strstr('IN',$field))
            $this->query['sql'] .= $field.' :'.$placeholder.' AND ';
            else
                $this->query['sql'] .= $field.' = :'.$placeholder.' AND ';
            $this->query['params'][':'.$placeholder] = $value;
        }
        if(substr($this->query['sql'], -5) === ' AND ') {$this->query['sql'] = substr($this->query['sql'], 0,-5);}
        return $this;
    }

    /**
     * Left JOIN
     * @param $table
     * @param array $join
     * @return $this
     */
    public function leftJoin($table, array $join)
    {
        $thisCol = key($join);
        $joinCol = $join[$thisCol];
        $this->query['sql'] .= " LEFT JOIN $table ON($this->table.$thisCol = $table.$joinCol) ";
        return $this;
    }

    /**
     * Right JOIN
     * @param $table
     * @param array $join
     * @return $this
     */
    public function rightJoin($table, array $join)
    {
        $thisCol = key($join);
        $joinCol = $join[$thisCol];
        $this->query['sql'] .= " RIGHT JOIN $table ON($this->table.$thisCol = $table.$joinCol) ";
        return $this;
    }

    /**
     * Inner JOIN
     * @param $table
     * @param array $join
     * @return $this
     */
    public function innerJoin($table, array $join)
    {
        $thisCol = key($join);
        $joinCol = $join[$thisCol];
        $this->query['sql'] .= " INNER JOIN $table ON($this->table.$thisCol = $table.$joinCol) ";
        return $this;
    }

    /**
     * order by one column ASC or DESC
     * @param array $order
     * @return $this
     */
    public function order(array $order)
    {
        $this->query['sql'] .= ' ORDER by '.key($order).' '.$order[key($order)];
        return $this;
    }

    /**
     * Set limit for sql fetch
     * @param array $limit
     * @return $this
     */
    public function limit(array $limit)
    {
        $this->query['sql'] .= ' LIMIT '.$limit[0].','.$limit[1];
        return $this;
    }

    /**
     * Add Like params to sql query
     * @param array $like
     * @return $this
     */
    public function like(array $like)
    {
        $field = key($like);
        $value = $like[$field];
        if( strpos($this->query['sql'], 'WHERE') === false)
        {
            $this->query['sql'] .= " WHERE $field LIKE(:$field)";
        }
        else
        {
            $this->query['sql'] .= " AND $field LIKE(:$field)";
        }
        $this->query['params'][':'.$field] = $value;
        return $this;
    }

    /**
     * Group query results by table field
     * @param $group
     * @return $this
     */
    public function group($group)
    {
        $this->query['sql'] .= ' GROUP by '.key($group).' '.$group[key($group)];
        return $this;
    }

    /**
     * @return array
     */
    public function execute()
    {   //var_dump($this->query['params']);
        $stmt = $this->Adapter->prepare($this->query['sql']);
        if(isset($this->query['params']))
            $stmt->execute($this->query['params']);
        else
            $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Now this system work just with MySQL.
     * @return PDO
     * Return current adapter
     * @since 2.0  PostgreSQL SqlLite MongoDB
     *
     */
    public function getAdapter()
    {
        if($this->Adapter !== null)
        {
            return $this->Adapter;
        }
        $db = new DbAdapter();
        return $db->Adapter();
    }

    /**
     * @param int $table
     * @return  void
     */
    public function reset ($table=0)
    {
        $this->query=[];
        if ($table)
            $this->table=$table;
        $this->query['sql']='SELECT * FROM '.$this->table.' ';
        $this->query['params'] = [];
    }
}
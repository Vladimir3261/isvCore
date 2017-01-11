<?php
namespace isv\DB;
use isv\Form\Form;
use isv\IS;
use PDO;

/**
 * Class ModelBase main class for models now framework use Doctrine ORM
 * for provide entities and work with database this class not active in
 * version 1.1 but you can use it for other ORM or Active record systems
 * or you can use components like DoctrineComponent.
 * Class ModelBase
 * @package isv\DB
 * @version 1.1
 *
 */
abstract class ModelBase implements ModelBaseInterface
{
    const FETCH_ARRAY = 0;

    const FETCH_OBJ = 1;
    /**
     * set PDO fetch Mode
     * @var int
     */
    private $pdoFetch = PDO::FETCH_ASSOC;

    /**
     * Set custom data mode array or objects..
     * @var int
     */
    private $cmsFetchMode = 0;

    /**
     * Primary key field name in selected table
     * @var int
     */
    private $primaryName = 0;

    /**
     * Database adapter for different DB engines in v1.0 works just with Mysql database
     * @since 2.0
     * @var PDO
     */
    protected $Adapter = null;

    /**
     * All model queries and validating errors in this array
     * @var array
     */
    protected $errors = [];

    /**
     * New object for database insert or loaded record
     * @var bool
     */
    public $insert = true;

    /**
     * Table name
     * @var string
     */
    protected $table;

    /**
     * Current fetch page
     * @var int
     */
    protected $currentPage = 0;

    /**
     * Count pages in select results
     * @var int
     */
    protected $pageCount = 0;

    /**
     * ModelBase constructor. Find DB row using primary kwy just int and load
     * all row data from database to current model instance properties.
     * @param int $id
     */
    public function __construct($id=0)
    {
        $this->Adapter = $this->getAdapter();
        $this->primaryName = $this->getPrimary();
        if($id)
        {
            $data = is_int($id) ? $this->findByPk($id) : $this->findOne($id);
            if ($data)
            {
                foreach ($data as $field => $value)
                {
                    $this->$field = $value;
                }
                $this->insert = false;
            }
        }
    }

    /**
     * This method mus be implement in child classes
     */
    abstract public function getPrimary();

    /**
     * info for generate model form
     */
    abstract public function forms();

    /**
     * Get array rules for validate class data
     * @return bool
     */
    abstract public function validateData();


    /**
     * Find one row using just primary key. This method load object properties
     * @param $primary
     * @return mixed
     */
    public function findByPk($primary)
    {
        $stmt = $this->Adapter->prepare("SELECT * FROM $this->table WHERE $this->primaryName=:id");
        $stmt->execute(['id' => $primary]);
        return $stmt->fetch($this->pdoFetch);
    }

    /**
     * Find all rows in database with function argument params
     * @param null|array $params
     * @param string $fields
     * @return mixed
     */
    public function findAll($fields = '*', $params = null)
    {
        if(is_array($fields)){$params = $fields; $fields = '*';}
        if(!$params)
        {
            $stmt = $this->Adapter->prepare("SELECT $fields  FROM $this->table");
            $stmt->execute();
        }
        else
        {
            $readyQuery = $this->BuildQuery($params, $fields);
            $stmt = $this->Adapter->prepare($readyQuery['sql']);
            $stmt->execute($readyQuery['params']);
        }
        if(!$this->cmsFetchMode)
        {
            return  $stmt->fetchAll($this->pdoFetch);
        }
        else
        {
            return  $this->dataConvert($stmt->fetchAll($this->pdoFetch));
        }
    }

    /**
     * find one row in database using WHERE params
     * @param string $params
     * @param string $fields
     * @return mixed
     */
    public function findOne($fields = '*', $params = null)
    {
        if(is_array($fields)){$params = $fields; $fields = '*';}
        if(!$params)
        {
            $stmt = $this->Adapter->prepare("SELECT $fields  FROM $this->table");
            $stmt->execute();
        }
        else
        {
            $readyQuery = $this->BuildQuery($params, $fields);
            $stmt = $this->Adapter->prepare($readyQuery['sql']);
            $stmt->execute($readyQuery['params']);
        }
        if(!$this->cmsFetchMode)
        {
            return  $stmt->fetch($this->pdoFetch);
        }
        else
        {
            return  $this->dataConvert($stmt->fetch($this->pdoFetch));
        }
    }

    /**
     * Delete row from database using primary key
     * @param $primary int
     * @return bool
     */
    public function delete($primary=NULL)
    {
        $primaryName = $this->primaryName;
        $primary = ($primary) ? $primary : $this->$primaryName;
        $stmt = $this->Adapter->prepare("DELETE FROM $this->table WHERE $this->primaryName=:id");
        return $stmt->execute(['id' => $primary]);
    }

    /**
     * Only one param
     * @TODO multiple params for deleting
     * @param array $params
     * @return bool
     */
    public function deleteAll($params = NULL)
    {
        if(!$params)
        {
            $stmt = $this->Adapter->prepare("DELETE FROM $this->table");
            return $stmt->execute();
        }
        else
        {
            $placeholders = [];
            foreach($params as $field => $value)
            {
                array_push($placeholders, $field.' = :'.$field);
            }
            $where = implode(" AND ", $placeholders);
            $stmt = $this->Adapter->prepare("DELETE FROM  $this->table WHERE $where");
            return $stmt->execute($params);
        }
    }

    public function beforeSave()
    {
        return true;
    }

    /**
     * Save function insert new row to database or update current object field
     * @return bool|string
     *
     */
    public function save()
    {
        if (!$this->beforeSave()){return false;}

        $id = $this->primaryName;
        if($this->$id)
        {
            $stmt = $this->Adapter->prepare("UPDATE $this->table SET ".$this->getPrepared()['update']." WHERE $this->primaryName=".$this->$id);
            if($stmt->execute($this->getPrepared()['values'])){
                return true;
            }
            $this->errors[] = $stmt->errorInfo();
            return false;
        }
        else
        {
            $stmt = $this->Adapter->prepare("INSERT INTO $this->table( ".$this->getPrepared()['set']." ) values( ".implode(", ", array_keys($this->getPrepared()['values']))." )");
            if($stmt->execute($this->getPrepared()['values'])){
                return $this->Adapter->lastInsertId('id');
            }
            $this->errors[] = $stmt->errorInfo();
            return false;
        }
    }

    /**
     * generate prepared array for use in PDO insert/update
     * @return mixed
     */
    private function getPrepared()
    {
        $array = $this->ToArray();
        unset($array['currentPage']);
        unset($array['pageCount']);
        unset($array['errors']);
        unset($array['insert']);
        unset($array['table']);
        unset($array['Adapter']);
        unset($array['pdoFetch']);
        unset($array['cmsFetchMode']);
        unset($array[$this->primaryName]);
        unset($array['primaryName']);
        $prepared['update'] = '';
        foreach($array as $k=>$v)
        {
            $prepared['values'][':'.$k] = $v;
            $prepared['update'] .= '`'.$k.'`'."=".':'.$k.',';
        }
        if ($prepared['update']{strlen($prepared['update'])-1} == ',')
        {
            $prepared['update'] = substr($prepared['update'],0,-1);
        }
        $prepared['set'] = implode(', ', array_keys($array));
        return $prepared;
    }

    /**
     * This method prepare query string and query params(PDO placeholders)
     * using user data
     * @param array $params
     * @param string $fields
     * @return array
     */
    private function BuildQuery(array $params, $fields)
    {
        $reserved = ['ORDER by', 'LIMIT', 'GROUP by'];
        $sql = "SELECT $fields FROM $this->table WHERE ";
        $detailedParams = '';
        $paramsArray = [];
        $enableWhere = 0;
       foreach($params as $k=>$v)
       {
           if(!in_array($k, $reserved))
           {
               if(strstr($v, 'IN('))
               {
                   $sql .= $k .' '.$v.' AND ';
                   $enableWhere = 1;
               }
               else
               {
                   $sql .= $k.' = :'.$k.' AND ';
                   $paramsArray[':'.$k]  = $v;
               }
           }
           else
           {
               $detailedParams .= $k.' '.$v.' ';
           }
       }
        if(substr($sql, -5) === ' AND ')
        {
            $sql = substr($sql, 0,-5);
        }
        $sql .= ' '.$detailedParams;

        if (!count($paramsArray) && !$enableWhere)
            $sql = str_replace('WHERE', '', $sql);
        // Pagination implementation
        if($this->pageCount)
        {
            $countQuery = preg_replace('/SELECT(.+?)FROM/is', 'SELECT COUNT(*) FROM',$sql);
            $CQ = $this->Adapter->prepare($countQuery);
            $CQ->execute($params);
            IS::app()->set('queryCount', $CQ->fetchColumn() );
            $sql .= 'LIMIT '.(int)$this->currentPage.', '.(int)$this->pageCount;
        }

        return ['sql' => $sql, 'params' => $paramsArray];
    }

    /**
     * Set Mode for PDO data fetching
     * @param int $mode
     */
    public function SetPdoFetchMode($mode = PDO::FETCH_ASSOC)
    {
        $this->pdoFetch = $mode;
    }

    /**
     * Set mode for convert database result to objects or array
     * @param $mode
     */
    public function CMSFetchMode($mode)
    {
        $this->cmsFetchMode = $mode;
    }

    /**
     * Convert PDO arrays to Model instances
     * @param $array
     * @return array
     */
    private function dataConvert($array)
    {
        $result = false;
        if(count($array) && isset($array[0]) && is_array($array[0]))
        {
            $class = get_class($this);
            foreach($array as $arr)
            {
                $result[] = new $class((int) $arr[$this->primaryName]);
            }
        }
        else if(count($array))
        {
            $class = get_class($this);
            $result = new $class((int) $array[$this->primaryName]);
        }
        return $result;
    }

    /**
     * get all model PDO and validate errors for user. this method just return
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * convert object to array of instance data
     * @return mixed
     */
    private function ToArray()
    {
        $array = [];
        foreach($this as $k => $v)
        {
            $array[$k] = $v;
        }
        return $array;
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
     * Validate data before save
     * @return bool
     */
    public function valid()
    {
        return $this->validateData();
    }

    /**
     * return new form for current model fields
     * @return Form
     */
    public function getFrom()
    {
        return new Form($this->ToArray(), $this->forms());
    }

    /**
     * Load user data form array like $_POST
     * @param $array
     * @return bool
     */
    public function load($array)
    {
        foreach($array as $k => $v)
        {
               if( property_exists($this, $k) )
                   $this->$k = $v;
        }
        foreach($this->forms() as $col => $rules)
        {
            if($rules['type'] !== 'checkbox')
                continue;
            else
                $this->$col = isset($array[$col]) ? 1 : 0;
        }
        return $this->valid();
    }

    /**
     * Create pagination
     * @param $count
     */
    public function setPaginate($count)
    {
        $this->pageCount = $count;
        $this->currentPage = ( IS::app()->get('page') ) ? (IS::app()->get('page')*$this->pageCount)-$this->pageCount : 0;
    }
}
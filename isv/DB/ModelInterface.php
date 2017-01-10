<?php
/**
 * Created by PhpStorm.
 * User: vladimir
 * Date: 19.05.16
 * Time: 16:39
 */

namespace isv\DB;
/**
 * Main interface for working with databases
 * Interface ModelInterface
 * @package isv\DB
 * @version 1.1
 */
interface ModelInterface
{
    /**
     * Constructor for all model
     * allows you to download data from database in the instance of the class
     * using the primary key when object is created
     * ModelInterface constructor.
     * @param int $id
     *
     */
    public function __construct($id);

    /**
     * @param array $fields
     * @return ModelBase
     */
    public function fields(array $fields);

    /**
     * @param array $where
     * @return ModelBase
     */
    public function where(array $where);

    /**
     * @param $table
     * @param array $join
     * @return ModelBase
     */
    public function leftJoin($table, array $join);

    /**
     * @param $table
     * @param array $join
     * @return ModelBase
     */
    public function rightJoin($table, array $join);

    /**
     * @param $table
     * @param array $join
     * @return ModelBase
     */
    public function innerJoin($table, array $join);

    /**
     * @param array $order
     * @return ModelBase
     */
    public function order(array $order);

    /**
     * @param array $limit
     * @return ModelBase
     */
    public function limit(array $limit);

    /**
     * This method execute query created user query builder using method Query
     * @return array
     */
    public function execute();

    /**
     * @return mixed
     * DATABASE ENGINE ADAPTER
     * @return \PDO
     */
    public function getAdapter();
}
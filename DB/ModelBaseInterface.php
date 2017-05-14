<?php
/**
 * Created by PhpStorm.
 * User: vladimir
 * Date: 23.05.16
 * Time: 18:28
 */

namespace isv\DB;

/**
 * Interface ModelBaseInterface
 * @package isv\DB
 * @version 1.1
 */
interface ModelBaseInterface
{
    /**
     * Get info about primary key field name for get instances
     * of record in database using just this method
     * @return int
     */
     public function getPrimary();

    /**
     * Find record in database table using primary KEY
     * @param $primary int
     * @return mixed
     */
    public function findByPk($primary);

    /**
     * finds all the records matching the specified parameters
     * @param string $fields
     * @param null|array $params
     * @return mixed
     */
    public function findAll($fields = '*', $params = NULL);

    /**
     * finds one record matching the specified parameters
     * @param string $fields
     * @param null|array $params
     * @return mixed
     */
    public function findOne($fields = '*', $params = NULL);

    /**
     * This implementation of SQL COUNT(*)
     * @param string $fields
     * @param null $params
     * @return mixed
     */
    public function count($fields = '*', $params = NULL);

    /**
     * delete record using record primary key
     * @param int $primary
     * @return mixed
     */
    public function delete($primary);

    /**
     * Save Object properties to database row
     * or create new database row if primary key = 0
     * @return mixed
     */
    public function save();

    /**
     * This method will return config  data for each record instance
     * @return array
     */
    public function forms();

    /**
     * This method net to contain every model who
     * implemented this interface, and return array data with validation rules
     * @return array
     */
    public function validateData();
}
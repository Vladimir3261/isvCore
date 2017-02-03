<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: vladimir
 * Date: 1/15/17
 * Time: 3:38 PM
 */

namespace isv\Http;

/**
 * Extends standard php-curl library
 * Class Curl
 * @package isv\Http
 */
class Curl
{
    /**
     * Http request
     */
    const HTTP = 1;
    /**
     * JSON request
     */
    const JSON = 2;

    /**
     * Curl connection resource
     * @var resource
     */
    protected $connection;
    /**
     * @var array
     */
    protected $postFields = [];
    /**
     * @var array|null
     */
    protected $result = NULL;

    /**
     * Crerate connection to URL
     * Curl constructor.
     * @param string $url
     */
    public function __construct(string $url)
    {
        $this->connection = curl_init();
        curl_setopt($this->connection, CURLOPT_URL, $url);
        curl_setopt($this->connection, CURLOPT_RETURNTRANSFER, true);
        return $this;
    }

    /**
     * POST or GET request
     * @param bool $isPost
     * @return $this
     */
    public function isPost(bool $isPost)
    {
        if($isPost)
            curl_setopt($this->connection, CURLOPT_POST, false);
        return $this;
    }

    /**
     * Set one POST fiels like key=>value
     * @param $key
     * @param $value
     * @return $this
     */
    public function addField($key, $value)
    {
        $this->postFields[$key] = $value;
        return $this;
    }

    /**
     * Set all post fields from prepared array
     * @param array $fields
     * @return $this
     */
    public function setPostFields(array $fields)
    {
        $this->postFields = $fields;
        return $this;
    }

    /**
     * Send simple cURL request
     */
    public function send()
    {
        $this->generateFields(static::HTTP)->execute();
    }

    /**
     * Send JSON request
     */
    public function sendJson()
    {
        $this->generateFields(static::JSON)->execute();
    }

    /**
     * Send curl request and close connection
     */
    private function execute()
    {
        $this->result['body'] = curl_exec ($this->connection);
        $this->result['headers'] = curl_getinfo($this->connection);
        $this->close();
    }


    /**
     * Generate post field for send like native http post or JSON
     * @param int $type
     */
    private function generateFields(int $type)
    {
        if($type === static::JSON)
        {
            curl_setopt($this->connection, CURLOPT_HTTPHEADER, ["Content-type: application/json"]);
            $str = json_encode($this->postFields);
        }
        else
        {
            $postvarsArray  = [];
            foreach ($this->postFields as $k=>$v)
            {
                array_push($postvarsArray, $k.'='.$v);
            }
            $str = implode('&', $postvarsArray);
        }
        curl_setopt($this->connection, CURLOPT_POSTFIELDS, $str);
        return $this;
    }

    /**
     * get Curl request result body
     * @return mixed
     */
    public function getResult()
    {
        return $this->result['body'];
    }

    /**
     * get Curl request result headers
     * @return mixed
     */
    public function getHeaders()
    {
        return $this->result['headers'];
    }

    /**
     * get FULL Curl execution result
     * @return array|null
     */
    public function getFullInfo()
    {
        return $this->result;
    }


    /**
     * Cloase cUrl connection
     */
    private function close()
    {
        curl_close ($this->connection);
    }

}
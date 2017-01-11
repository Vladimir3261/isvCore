<?php
/**
 * Created by PhpStorm.
 * User: vladimir
 * Date: 12.02.16
 * Time: 17:33
 */

namespace isv\Exception;
use Exception;
use isv\Exception\ExceptionInterface as ISInterface;

/**
 * Abstract class ISVException the main exception class for extend
 * every single exception class
 * @package isv\Exception
 * @version 1.1
 */
abstract class ISVException extends Exception implements ISInterface
{
    /**
     * @var string Exception message
     */
    protected $message = 'Unknown exception';

    /**
     * @var string $string Unknown interface implementation
     */
    private   $string;

    /**
     * @var int $code isv-framework exception code
     */
    protected $code    = 0;

    /**
     * @var string $file Source filename of exception
     */
    protected $file;

    /**
     * @var string Source line of exception
     */
    protected $line;

    /**
     * @var string Unknown interface implementation
     */
    private   $trace;

    public function __construct($message = null, $code = 0)
    {
        if (!$message) {
            throw new $this('Unknown '. get_class($this));
        }
        parent::__construct($message, $code);
    }

    public function display()
    {
        $array = $this->getTrace();
        $message = $this->getMessage();
        $code = $this->getCode();
        include_once __DIR__.'/html/exception_dump.php';
    }

}
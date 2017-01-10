<?php
/**
 * Created by PhpStorm.
 * User: vladimir
 * Date: 04.02.16
 * Time: 23:36
 */

namespace isv\Exception;
/**
 * Interface ExceptionInterface
 * @package isv\Exception
 */
interface ExceptionInterface
{
    /**
     * @return string exception message
     */
    public function getMessage();

    /**
     * @return int
     * isv-framework Exception code
     */
    public function getCode();

    /**
     * @return string Source filename
     */
    public function getFile();

    /**
     * @return string Source line
     */
    public function getLine();

    /**
     * @return array An array of the backtrace()
     */
    public function getTrace();

    /**
     * @return string Formated string of trace
     */
    public function getTraceAsString();

    /**
     * @return string formated string for display
     */
    public function __toString();

    /**
     * IException constructor.
     * @param null|string $message
     * @param int $code
     */
    public function __construct($message = null, $code = 0);
}
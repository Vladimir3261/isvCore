<?php
/**
 * Created by PhpStorm.
 * User: vladimir
 * Date: 03.05.16
 * Time: 11:41
 */

namespace isv\Access;

/**
 * Class AccessControl
 * @package isv\Access
 */
class AccessControl
{
    private $rules;

    public function __construct(array $rules)
    {
        $this->rules = $rules;
    }
}
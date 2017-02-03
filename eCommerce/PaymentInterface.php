<?php

/**
 * Created by PhpStorm.
 * User: vladimir
 * Date: 12/5/16
 * Time: 7:19 PM
 */
namespace isv\eCommerce;
use isv\DB\ModelBase;

/**
 * Interface PaymentInterface
 * @package isv\eCommerce
 */
interface PaymentInterface
{
    /**
     * @param ModelBase $order
     * @return mixed
     */
    public function startPayment(ModelBase $order);

    /**
     * Get payment link or form
     * @return mixed
     */
    public function getPayment();

    /**
     * Check payment verification
     * @param ModelBase $order
     * @param $postData array
     * @return mixed
     */
    public function verifyPayment(ModelBase $order, array $postData);
}
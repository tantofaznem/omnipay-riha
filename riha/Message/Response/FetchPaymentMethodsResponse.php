<?php

namespace Omnipay\Riha\Message\Response;

use Omnipay\Common\Message\FetchPaymentMethodsResponseInterface;
use Omnipay\Common\PaymentMethod;

/**
 * @see https://docs.riha.co.mz/reference/v2/methods-api/list-methods
 */
class FetchPaymentMethodsResponse extends AbstractRihaResponse implements FetchPaymentMethodsResponseInterface
{
    /**
     * Return available payment methods as an associative array.
     *
     * @return PaymentMethod[]
     */
    public function getPaymentMethods()
    {
        if (isset($this->data['_embedded']["methods"]) === false) {
            return [];
        }

        $paymentMethods = [];
        foreach ($this->data['_embedded']["methods"] as $method) {
            $paymentMethods[] = new PaymentMethod($method['id'], $method['description']);
        }

        return $paymentMethods;
    }
}

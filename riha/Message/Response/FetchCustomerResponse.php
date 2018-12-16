<?php

namespace Omnipay\Riha\Message\Response;

/**
 * @see https://docs.riha.co.mz/reference/v2/customers-api/get-customer
 */
class FetchCustomerResponse extends AbstractRihaResponse
{
    /**
     * @return string|null
     */
    public function getCustomerReference()
    {
        if (isset($this->data['id'])) {
            return $this->data['id'];
        }

        return null;
    }
}

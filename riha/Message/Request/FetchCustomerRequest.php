<?php

namespace Omnipay\Riha\Message\Request;

use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Common\Message\AbstractRequest;
use Omnipay\Riha\Message\Response\FetchCustomerResponse;

/**
 * Retrieve a single customer by its ID.
 *
 * @see https://docs.riha.co.mz/reference/v2/customers-api/get-customer
 */
class FetchCustomerRequest extends AbstractRihaRequest
{
    /**
     * @return string
     */
    public function getCustomerReference()
    {
        return $this->getParameter('customerReference');
    }

    /**
     * @param string $value
     * @return AbstractRequest
     */
    public function setCustomerReference($value)
    {
        return $this->setParameter('customerReference', $value);
    }

    /**
     * @return array
     * @throws InvalidRequestException
     */
    public function getData()
    {
        $this->validate('apiKey', 'customerReference');

        return [];
    }

    /**
     * @param array $data
     * @return FetchCustomerResponse
     */
    public function sendData($data)
    {
        $response = $this->sendRequest(self::GET, '/customers/' . $this->getCustomerReference(), $data);

        return $this->response = new FetchCustomerResponse($this, $response);
    }
}

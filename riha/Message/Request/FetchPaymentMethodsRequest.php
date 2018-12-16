<?php

namespace Omnipay\Riha\Message\Request;

use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Common\Message\ResponseInterface;
use Omnipay\Riha\Message\Response\FetchPaymentMethodsResponse;

/**
 * Retrieve all available payment methods.
 *
 * @see https://docs.riha.co.mz/reference/v2/methods-api/list-methods
 */
class FetchPaymentMethodsRequest extends AbstractRihaRequest
{
    /**
     * @return array
     * @throws InvalidRequestException
     */
    public function getData()
    {
        $this->validate('apiKey');

        return [];
    }

    /**
     * @param array $data
     * @return ResponseInterface|FetchPaymentMethodsResponse
     */
    public function sendData($data)
    {
        $response = $this->sendRequest(self::GET, '/methods');

        return $this->response = new FetchPaymentMethodsResponse($this, $response);
    }
}

<?php

namespace Omnipay\Riha\Message\Request;

use Omnipay\Common\Message\AbstractRequest;

/**
 * This class holds all the common things for all of Riha requests.
 *
 * @see https://docs.riha.co.mz/index
 */
abstract class AbstractRihaRequest extends AbstractRequest
{
    const POST = 'POST';
    const GET = 'GET';

    /**
     * @var string
     */
    protected $apiVersion = "v2";

    /**
     * @var string
     */
    protected $baseUrl = 'https://api.riha.co.mz/';

    /**
     * @return string
     */
    public function getApiKey()
    {
        return $this->getParameter('apiKey');
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setApiKey($value)
    {
        return $this->setParameter('apiKey', $value);
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setTransactionId($value)
    {
        return $this->setParameter('transactionId', $value);
    }

    /**
     * @return string
     */
    public function getTransactionId()
    {
        return $this->getParameter('transactionId');
    }

    /**
     * @param string $method
     * @param string $endpoint
     * @param array $data
     * @return array
     */
    protected function sendRequest($method, $endpoint, array $data = null)
    {
        $response = $this->httpClient->request(
            $method,
            $this->baseUrl . $this->apiVersion . $endpoint,
            [
                'Authorization' => 'Bearer ' . $this->getApiKey()
            ],
            ($data === null) ? null : json_encode($data)
        );

        return json_decode($response->getBody(), true);
    }
}

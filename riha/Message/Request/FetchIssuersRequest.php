<?php

namespace Omnipay\Riha\Message\Request;

use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Riha\Message\Response\FetchIssuersResponse;
use Psr\Http\Message\ResponseInterface;

/**
 * Returns issuers available for the ideal payment method.
 *
 * @see https://docs.riha.co.mz/reference/v2/methods-api/get-method
 */
class FetchIssuersRequest extends AbstractRihaRequest
{
    /**
     * Since the Issuer endpoint got removed in the Riha v2 api.
     * We now use the include parameter on the get-method endpoint.
     *
     * @var string
     */
    protected $endpoint = '/methods/ideal?include=issuers';

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
     * @return ResponseInterface|FetchIssuersResponse
     */
    public function sendData($data)
    {
        $response = $this->sendRequest(self::GET, $this->endpoint);

        return $this->response = new FetchIssuersResponse($this, $response);
    }
}

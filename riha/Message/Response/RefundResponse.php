<?php

namespace Omnipay\Riha\Message\Response;

/**
 * @see https://docs.riha.co.mz/reference/v2/refunds-api/create-refund
 */
class RefundResponse extends AbstractRihaResponse
{
    /**
     * @return null|string
     */
    public function getTransactionReference()
    {
        return $this->data['paymentId'];
    }

    /**
     * @return string
     */
    public function getTransactionId()
    {
        return $this->data['id'];
    }

    /**
     * @return bool
     */
    public function isSuccessful()
    {
        return isset($this->data['id']);
    }
}

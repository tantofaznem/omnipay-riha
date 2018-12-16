<?php

namespace Omnipay\Riha\Message\Response;

/**
 * @see https://docs.riha.co.mz/reference/v2/payments-api/get-payment
 */
class CompletePurchaseResponse extends FetchTransactionResponse
{
    /**
     * {@inheritdoc}
     */
    public function isSuccessful()
    {
        return parent::isSuccessful() && $this->isPaid();
    }
}

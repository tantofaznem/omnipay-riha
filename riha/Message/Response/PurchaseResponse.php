<?php

namespace Omnipay\Riha\Message\Response;

/**
 * @see https://docs.riha.co.mz/reference/v2/payments-api/create-payment
 */
class PurchaseResponse extends FetchTransactionResponse
{
    /**
     * When you do a `purchase` the request is never successful because
     * you need to redirect off-site to complete the purchase.
     *
     * {@inheritdoc}
     */
    public function isSuccessful()
    {
        return false;
    }
}

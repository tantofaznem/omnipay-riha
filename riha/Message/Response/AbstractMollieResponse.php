<?php

namespace Omnipay\Riha\Message\Response;

use Omnipay\Common\Message\AbstractResponse;

class AbstractRihaResponse extends AbstractResponse
{
    /**
     * @return bool
     */
    public function isSuccessful()
    {
        if (isset($this->data['status']) && isset($this->data['detail'])) {
            return false;
        }

        return true;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return json_encode($this->data);
    }
}

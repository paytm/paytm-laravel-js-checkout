<?php

namespace Paytm\JsCheckout\Contracts;

interface Provider
{
    /**
     * Return raw response.
     *
     * @return object
     */
    public function response();

}

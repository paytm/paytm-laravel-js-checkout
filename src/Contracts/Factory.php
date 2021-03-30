<?php

namespace Paytm\JsCheckout\Contracts;

interface Factory
{
    /**
     * Get Paytm Wallet Provider
     *
     * @param  string  $driver
     * @return \Paytm\JsCheckout\Contracts\Provider
     */
    
    public function driver($do = null);
}

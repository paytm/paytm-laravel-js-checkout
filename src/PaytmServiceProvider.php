<?php

namespace Paytm\JsCheckout;


use Illuminate\Support\ServiceProvider;


class PaytmServiceProvider extends ServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('Paytm\JsCheckout\Contracts\Factory', function ($app) {
            return new PaytmManager($app);
        });
    }

    public function provides(){
        return ['Paytm\JsCheckout\Contracts\Factory'];
    }
}

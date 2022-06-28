<?php

namespace Paytm\JsCheckout;

use Illuminate\Support\Manager;
use Illuminate\Http\Request;
class PaytmManager extends Manager implements Contracts\Factory{
	

	protected $config;



	public function with($driver){
		return $this->driver($driver);
	}

	protected function createReceiveDriver(){
		$this->config = $this->container['config']['services.paytm'];

		return $this->buildProvider(
			'Paytm\JsCheckout\Providers\ReceivePaymentProvider',
			$this->config
			);
	}
	
	public function getDefaultDriver(){
		throw new \Exception('No driver was specified. - Laravel Paytm');
	}

	public function buildProvider($provider, $config){
		return new $provider(
			$this->container['request'],
			$config
			);
	}
}

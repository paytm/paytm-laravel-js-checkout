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
		$this->config = $this->app['config']['services.paytm'];

		return $this->buildProvider(
			'Paytm\JsCheckout\Providers\ReceivePaymentProvider',
			$this->config
			);
	}

	protected function createStatusDriver(){
		$this->config = $this->app['config']['services.paytm'];
		return $this->buildProvider(
			'Paytm\JsCheckout\Providers\StatusCheckProvider',
			$this->config
			);
	}

	protected function createBalanceDriver(){
		$this->config = $this->app['config']['services.paytm'];
		return $this->buildProvider(
			'Paytm\JsCheckout\Providers\BalanceCheckProvider',
			$this->config
			);
	}

	protected function createAppDriver(){
		$this->config = $this->app['config']['services.paytm'];
		return $this->buildProvider(
			'Paytm\JsCheckout\Providers\PaytmAppProvider',
			$this->config
			);
	}

	protected function createRefundDriver() {
		$this->config = $this->app['config']['services.paytm'];
		return $this->buildProvider(
			'Paytm\JsCheckout\Providers\RefundPaymentProvider',
			$this->config
			);
	}
	
	protected function createRefundStatusDriver(){
		$this->config = $this->app['config']['services.paytm'];
		return $this->buildProvider(
			'Paytm\JsCheckout\Providers\RefundStatusCheckProvider',
			$this->config
			);
	}
	
	public function getDefaultDriver(){
		throw new \Exception('No driver was specified. - Laravel Paytm');
	}

	public function buildProvider($provider, $config){
		return new $provider(
			$this->app['request'],
			$config
			);
	}
}

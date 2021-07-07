<?php

namespace Paytm\JsCheckout\Providers;
use Paytm\JsCheckout\Facades\Paytm;
use Paytm\JsCheckout\Traits\HasTransactionStatus;
use Illuminate\Http\Request;

class ReceivePaymentProvider extends PaytmProvider{
	use HasTransactionStatus;
	
	private $parameters = null;
	private $view = 'paytm::transact';

    public function prepare($params = array()){
		$defaults = [
			'order' => NULL,
			'user' => NULL,
			'amount' => NULL,
            'callback_url' => NULL,
            'email' => NULL,
            'mobile_number' => NULL,
		];

		$_p = array_merge($defaults, $params);
		foreach ($_p as $key => $value) {

			if ($value == NULL) {
				
				throw new \Exception(' \''.$key.'\' parameter not specified in array passed in prepare() method');
				
				return false;
			}
		}
		$this->parameters = $_p;
		return $this;
	}

	public function receive(){
		if ($this->parameters == null) {
			throw new \Exception("prepare() method not called");
		}
		return $this->beginTransaction();
	}

	public function view($view) {
		if($view) {
			$this->view = $view;
		}
		return $this;
	}

	private function beginTransaction(){

		$apiURL = getPaytmURL($this->inititate_transaction_url, $this->environment) . '?mid='.$this->merchant_id.'&orderId='.$this->parameters['order'];
		$paytmParams = array();

		$paytmParams["body"] = array(
			"requestType"   => "Payment",
			"mid"           => $this->merchant_id,
			"websiteName"   => $this->merchant_website,
			"orderId"       => $this->parameters['order'],
			"callbackUrl"   => $this->parameters['callback_url'],
			"txnAmount"     => array(
				"value"     => $this->parameters['amount'],
				"currency"  => "INR",
			),
			"userInfo"      => array(
				"custId"    => $this->parameters['email'],
			),
		);
		/*
		* Generate checksum by parameters we have in body
		* Find your Merchant Key in your Paytm Dashboard at https://dashboard.paytm.com/next/apikeys 
		*/
		$checksum = generateSignature(json_encode($paytmParams["body"], JSON_UNESCAPED_SLASHES), $this->merchant_key);
		
		$paytmParams["head"] = array(
			"signature"	=> $checksum
		);

		$postData = json_encode($paytmParams, JSON_UNESCAPED_SLASHES);

		$response = executecUrl($apiURL, $postData);
		$data = array('orderId' => $this->parameters['order'], 'amount' => $this->parameters['amount']);
		if(!empty($response['body']['txnToken'])){
			$data['txnToken'] = $response['body']['txnToken'];
		}else{
			$data['txnToken'] = '';
			$data['message'] = "Something went wrong";
			$data['resultMsg'] = $response['body']['resultInfo']['resultMsg'];
		}
		$data['apiurl'] = $apiURL;
		$checkout_url = str_replace('MID',$this->merchant_id, getPaytmURL($this->checkout_js_url,$this->environment));
		$data['checkoutUrl'] = $checkout_url;
		return 	$data;	
	}

    public function getOrderId(){
        return $this->response()['ORDERID'];
    }
    public function getTransactionId(){
        return $this->response()['TXNID'];
    }

}

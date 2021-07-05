<?php

namespace Paytm\JsCheckout\Providers;
use Paytm\JsCheckout\Contracts\Provider as ProviderContract;
use Illuminate\Http\Request;
require __DIR__.'/../../lib/PaytmChecksum.php';
require __DIR__.'/../../lib/PaytmConstants.php';
require __DIR__.'/../../lib/PaytmHelper.php';

class PaytmProvider implements ProviderContract {

	protected $request;
	protected $response;
	protected $paytm_txn_url;
	protected $paytm_txn_status_url;
	protected $paytm_refund_url;
	protected $paytm_refund_status_url;
	protected $paytm_balance_check_url;

	protected $merchant_key;
	protected $merchant_id;
	protected $merchant_website;
	protected $industry_type;
	protected $channel;

	protected $inititate_transaction_url;
	protected $environment;
	protected $checkout_js_url;

	public function __construct(Request $request, $config){
		$this->request = $request;
		
		if ($config['env'] == 'production') {
			$domain = 'securegw.paytm.in';
		}else{
			$domain = 'securegw-stage.paytm.in';
		}
		$this->paytm_txn_url = 'https://'.$domain.'/theia/processTransaction';
		$this->paytm_txn_status_url = 'https://'.$domain.'/merchant-status/getTxnStatus';
		$this->paytm_refund_url = 'https://'.$domain.'/refund/HANDLER_INTERNAL/REFUND';
		$this->paytm_refund_status_url = 'https://'.$domain.'/refund/HANDLER_INTERNAL/getRefundStatus';
		$this->paytm_balance_check_url = 'https://'.$domain.'/refund/HANDLER_INTERNAL/getRefundStatus';

		$this->merchant_key = $config['merchant_key'];
		$this->merchant_id = $config['merchant_id'];
		$this->merchant_website  = $config['merchant_website'];
		$this->industry_type = $config['industry_type'];
		$this->channel = $config['channel'];

		$this->inititate_transaction_url = "theia/api/v1/initiateTransaction/";
		$this->environment = ($config['env']=="production")?1:0;
		$this->checkout_js_url	= "merchantpgpui/checkoutjs/merchants/MID.js";
	}

	public function response(){
		$checksum = $this->request->get('CHECKSUMHASH');
		unset($_POST['CHECKSUMHASH']);
		$result =  verifySignature($_POST, $this->merchant_key, $checksum);
		
		if($result==1){
		    return $this->response = $this->request->post();
		}
        	throw new \Exception('Invalid checksum');
	}

	public function getResponseMessage() {
    		return $this->response()['RESPMSG'];
   	}

	public function api_call($url, $params){
		return callAPI($url, $params);
	}

	public function api_call_new($url, $params){
		return callAPI($url, $params);
	}
}

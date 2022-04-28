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
			$env= 1;
			$domain = 'securegw.paytm.in';
		}else{
			$domain = 'securegw-stage.paytm.in';
			$env= 0;
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
		$this->env = $env;
	}

	public function response(){ 
		$checksum = $this->request->get('CHECKSUMHASH');
		unset($_POST['CHECKSUMHASH']);
		$result =  verifySignature($_POST, $this->merchant_key, $checksum);
		
		if($result==1){
			$reqParams = array(
						"MID" 		=> $this->merchant_id,
						"ORDERID" 	=> $_POST['ORDERID']
					);

			$reqParams['CHECKSUMHASH'] = generateSignature($reqParams, $this->merchant_key);

			/* number of retries untill cURL gets success */
			$retry = 1;
			do{
				$postData = 'JsonData='.urlencode(json_encode($reqParams));
				$resParams = executecUrl(getPaytmURL('order/status', $this->env), $postData);
				$retry++;
			} while(!$resParams['STATUS'] && $retry < 3);
			/* number of retries untill cURL gets success */

			if(!isset($resParams['STATUS'])){
				$resParams = $_POST;
			}
			if($resParams['STATUS'] == 'TXN_SUCCESS') {
				return $this->response = $_POST;
			}
		    return $this->response = $_POST;
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

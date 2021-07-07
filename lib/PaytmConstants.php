<?php

class PaytmConstants{
	CONST TRANSACTION_STATUS_URL_PRODUCTION		                = "https://securegw.paytm.in/order/status";
	CONST TRANSACTION_STATUS_URL_STAGING		                = "https://securegw-stage.paytm.in/order/status";

	CONST PRODUCTION_HOST						= "https://securegw.paytm.in/";
	CONST STAGING_HOST					        = "https://securegw-stage.paytm.in/";

	CONST ORDER_PROCESS_URL						= "order/process";
	CONST ORDER_STATUS_URL						= "order/status";
	CONST INITIATE_TRANSACTION_URL				        = "theia/api/v1/initiateTransaction";
	CONST CHECKOUT_JS_URL						= "merchantpgpui/checkoutjs/merchants/MID.js";


	CONST SAVE_PAYTM_RESPONSE 					= true;
	CONST CHANNEL_ID						= "WEB";
	CONST APPEND_TIMESTAMP						= true;
	CONST X_REQUEST_ID						= "PLUGIN_LARAVEL_";
	CONST PLUGIN_DOC_URL						= "";

	CONST MAX_RETRY_COUNT						= 3;
	CONST CONNECT_TIMEOUT						= 10;
	CONST TIMEOUT							= 10;

	CONST LAST_UPDATED						= "20210616";
	CONST PLUGIN_VERSION						= "1.2";

	CONST CUSTOM_CALLBACK_URL					= "";


	CONST ID						        = "paytm";
	CONST METHOD_TITLE						= "Paytm Payments";
	CONST METHOD_DESCRIPTION					= "The best payment gateway provider in India for e-payment through credit card, debit card & netbanking.";

	CONST TITLE							= "Paytm";
	CONST DESCRIPTION						= "The best payment gateway provider in India for e-payment through credit card, debit card & netbanking.";

	 
	
	CONST FRONT_MESSAGE					    = "Thank you for your order, please click the button below to pay with paytm.";
	CONST NOT_FOUND_TXN_URL					= "Something went wrong. Kindly contact with us.";
	CONST PAYTM_PAY_BUTTON					= "Pay via Paytm";
	CONST CANCEL_ORDER_BUTTON				= "Cancel order & Restore cart";
	CONST POPUP_LOADER_TEXT					= "Thank you for your order. We are now redirecting you to paytm to make payment.";

	CONST TRANSACTION_ID					= "<b>Transaction ID:</b> %s";
	CONST PAYTM_ORDER_ID					= "<b>Paytm Order ID:</b> %s";

	CONST REASON							= " Reason: %s";
	CONST FETCH_BUTTON						= "Fetch Status";

}

?>

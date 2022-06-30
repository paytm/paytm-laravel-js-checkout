For Laravel 5.0 and above

## Getting Started
To get started add the following package to your `composer.json` file using this command.

    composer require paytm/js-checkout

## Configuring
**Note: For Laravel 5.5 and above auto-discovery takes care of below configuration.**

When composer installs Laravel Paytm Wallet library successfully, register the `Paytm\JsCheckout\PaytmServiceProvider` in your `config/app.php` configuration file.

```php
'providers' => [
    // Other service providers...
    Paytm\JsCheckout\PaytmServiceProvider::class,
],
```
Also, add the `Paytm` facade to the `aliases` array in your `app` configuration file:

```php
'aliases' => [
    // Other aliases
    'Paytm' => Paytm\JsCheckout\Facades\Paytm::class,
],
```
#### Add the paytm credentials to the `.env` file
```bash
PAYTM_ENVIRONMENT=staging
PAYTM_MERCHANT_ID=YOUR_MERCHANT_ID_HERE
PAYTM_MERCHANT_KEY=YOUR_SECRET_KEY_HERE
PAYTM_MERCHANT_WEBSITE=YOUR_MERCHANT_WEBSITE
PAYTM_CHANNEL=YOUR_CHANNEL_HERE
PAYTM_INDUSTRY_TYPE=YOUR_INDUSTRY_TYPE_HERE
```


#### One more step to go....
On your `config/services.php` add the following configuration

```php
'paytm' => [
        'env' => env('PAYTM_ENVIRONMENT'), // values : (staging | production)
        'merchant_id' => env('PAYTM_MERCHANT_ID'),
        'merchant_key' => env('PAYTM_MERCHANT_KEY'),
        'merchant_website' => env('PAYTM_MERCHANT_WEBSITE'),
        'channel' => env('PAYTM_CHANNEL'),
        'industry_type' => env('PAYTM_INDUSTRY_TYPE'),
],
```
Note : All the credentials mentioned are provided by Paytm after signing up as merchant.

#### Laravel 7 Changes
Our package is compatible with Laravel 7 but same_site setting is changed in default Laravel installation, make sure you change `same_site` to `null` in `config/session.php` or callback won't include cookies and you will be logged out when a payment is completed

```php
<?php

use Illuminate\Support\Str;

return [
  /...
  'same_site' => null,
];
```

## Usage


### Making a transaction
```
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Paytm;

class PaytmController extends Controller
{
    // display a form for payment
    public function initiate()
    {
          return view('paytm');
    }

    public function pay(Request $request)
    {
        $amount = 1; //Amount to be paid

        $userData = [
            'name' => $request->name, // Name of user
            'mobile' => $request->mobile, //Mobile number of user
            'email' => $request->email, //Email of user
            'fee' => $amount,
            'order_id' => rand(1,1000) //Order id
        ];

        $payment = Paytm::with('receive');

        $payment->prepare([
            'order' => $userData['order_id'], 
            'user' => 1,
            'mobile_number' => $userData['mobile'],
            'email' => $userData['email'], // your user email address
            'amount' => $amount, // amount will be paid in INR.
            'callback_url' => route('status') // callback URL
        ]);
        $response =  $payment->receive();  // initiate a new payment
        return $response;
    }

    public function paymentCallback()
    {
        $transaction = Paytm::with('receive');

        $response = $transaction->response();
        
        $order_id = $transaction->getOrderId(); // return a order id
      
        $transaction->getTransactionId(); // return a transaction id
        // update the db data as per result from api call
        if ($transaction->isSuccessful()) {
            return redirect(route('initiate.payment'))->with('message', "Your payment is successfull.");

        } else if ($transaction->isFailed()) {
            return redirect(route('initiate.payment'))->with('message', "Your payment is failed.");
            
        } else if ($transaction->isOpen()) {
             return redirect(route('initiate.payment'))->with('message', "Your payment is processing.");
        }
        $transaction->getResponseMessage(); //Get Response Message If Available
    }
}

```
### Making a view page
```
<!DOCTYPE html>
<html>
<head>
    <title>Payment gateway using Paytm</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <style type="text/css">
    #paytm-pg-spinner {margin: 20% auto 0;width: 70px;text-align: center;z-index: 999999;position: relative;}

    #paytm-pg-spinner > div {width: 10px;height: 10px;background-color: #012b71;border-radius: 100%;display: inline-block;-webkit-animation: sk-bouncedelay 1.4s infinite ease-in-out both;animation: sk-bouncedelay 1.4s infinite ease-in-out both;}

    #paytm-pg-spinner .bounce1 {-webkit-animation-delay: -0.64s;animation-delay: -0.64s;}

    #paytm-pg-spinner .bounce2 {-webkit-animation-delay: -0.48s;animation-delay: -0.48s;}
    #paytm-pg-spinner .bounce3 {-webkit-animation-delay: -0.32s;animation-delay: -0.32s;}

    #paytm-pg-spinner .bounce4 {-webkit-animation-delay: -0.16s;animation-delay: -0.16s;}
    #paytm-pg-spinner .bounce4, #paytm-pg-spinner .bounce5{background-color: #48baf5;} 
    @-webkit-keyframes sk-bouncedelay {0%, 80%, 100% { -webkit-transform: scale(0) }40% { -webkit-transform: scale(1.0) }}

    @keyframes sk-bouncedelay { 0%, 80%, 100% { -webkit-transform: scale(0);transform: scale(0); } 40% { 
        -webkit-transform: scale(1.0); transform: scale(1.0);}}
    .paytm-overlay{position: fixed;top: 0px;opacity: .4;height: 100%;background: #000;}

    </style>
<div id="paytm-pg-spinner" class="paytm-pg-loader" style="display: none;">
  <div class="bounce1"></div>
  <div class="bounce2"></div>
  <div class="bounce3"></div>
  <div class="bounce4"></div>
  <div class="bounce5"></div>
</div>
<div class="paytm-overlay" style="display:none;"></div>
<div class="container" width="500px">
    <div class="panel panel-primary" style="margin-top:110px;">
        <div class="panel-heading"><h3 class="text-center">Payment gateway using Paytm Laravel JS Checkout</h3></div>
        <div class="panel-body">
            <form action="{{ route('make.payment') }}" method="POST" enctype="multipart/form-data">
                {!! csrf_field() !!}

                @if($message = Session::get('message'))
                    <p>{!! $message !!}</p>
                    <?php Session::forget('success'); ?>
                @endif

                <div class="row">
                    <div class="col-md-12">
                        <strong>Name:</strong>
                        <input type="text" name="name" class="form-control name" placeholder="Name" required>
                    </div>
                    <div class="col-md-12">
                        <strong>Mobile No:</strong>
                        <input type="text" name="mobile" class="form-control mobile" maxlength="10" placeholder="Mobile No." required>
                    </div>
                    <div class="col-md-12">
                        <strong>Email:</strong>
                        <input type="email" class="form-control email" placeholder="Email" name="email" required>
                    </div>
                    <div class="col-md-12" >
                        <br/>
                        <div class="btn btn-info">
                            Term Fee : 1 Rs/-
                        </div>
                    </div>
                    <div class="col-md-12">
                        <br/>
                        <button type="submit" class="btn btn-success pay">Paytm</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>   

<script src="https://code.jquery.com/jquery-2.2.4.min.js" integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44=" crossorigin="anonymous"></script>
    @if(env('PAYTM_ENVIRONMENT')=='production')
        <script type="application/javascript" crossorigin="anonymous" src="https:\\securegw.paytm.in\merchantpgpui\checkoutjs\merchants\<?php echo env('PAYTM_MERCHANT_ID')?>.js" ></script>
    @else
       <script type="application/javascript" crossorigin="anonymous" src="https:\\securegw-stage.paytm.in\merchantpgpui\checkoutjs\merchants\<?php echo env('PAYTM_MERCHANT_ID')?>.js" ></script>
    @endif    

    <script type="text/javascript">
    //function openJsCheckout(){ 
    $(".pay").click(function(e){    
        var name = $('.name').val();
        var mobile = $('.mobile').val();
        var email = $('.email').val();
        if(name == "" || mobile == "" || email== "" ){
            alert("Please fill all the fields");
            return false;
        }
        e.preventDefault();
        $.ajax({
           type:'POST',
           url:'payment',
           data: {
            '_token':'{{ csrf_token() }}',
            'name': $('.name').val(),
            'mobile': $('.mobile').val(),
            'email': $('.email').val(),
            },
           success:function(data) {
            $('.paytm-pg-loader').show();
            $('.paytm-overlay').show();
	    if(data.txnToken == ""){
                alert(data.message);
                $('.paytm-pg-loader').hide();
                $('.paytm-overlay').hide();
                return false;
            }
            invokeBlinkCheckoutPopup(data.orderId,data.txnToken,data.amount)
           }
        });
    
  });

    function invokeBlinkCheckoutPopup(orderId,txnToken,amount){
        window.Paytm.CheckoutJS.init({
            "root": "",
            "flow": "DEFAULT",
            "data": {
                "orderId": orderId,
                "token": txnToken,
                "tokenType": "TXN_TOKEN",
                "amount": amount,
            },
            handler:{
                    transactionStatus:function(data){
                } , 
                notifyMerchant:function notifyMerchant(eventName,data){
                    if(eventName=="APP_CLOSED")
                    {
                      $('.paytm-pg-loader').hide();
                      $('.paytm-overlay').hide();
                      //location.reload();
                    }
                    console.log("notify merchant about the payment state");
                } 
                }
        }).then(function(){
            window.Paytm.CheckoutJS.invoke();
        });
    }

</script>                
</body>
</html>

```
### Define routes
```
Route::get('/initiate','PaytmController@initiate')->name('initiate.payment');
Route::post('/payment','PaytmController@pay')->name('make.payment');
Route::post('/payment/status', 'PaytmController@paymentCallback')->name('status');

```
Important: The `callback_url` must not be csrf protected [Check out here to how to do that](https://laracasts.com/discuss/channels/general-discussion/l5-disable-csrf-middleware-on-certain-routes)




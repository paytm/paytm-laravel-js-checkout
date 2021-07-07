<?php

namespace Paytm\JsCheckout\Traits;
use Paytm\JsCheckout\Facades\Paytm;

trait HasTransactionStatus {

    public function isOpen(){
        if ($this->response['STATUS'] == Paytm::STATUS_OPEN){
            return true;
        }
        return false;
    }

    public function isFailed(){
        if ($this->response['STATUS'] == Paytm::STATUS_FAILURE) {
            return true;
        }
        return false;
    }

    public function isSuccessful(){
        if($this->response['STATUS'] == Paytm::STATUS_SUCCESSFUL){
            return true;
        }
        return false;
    }

    public function isPending(){
        if($this->response['STATUS'] == Paytm::STATUS_PENDING){
            return true;
        }
        return false;
    }
}

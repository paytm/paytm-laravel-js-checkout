<?php
	

	 function encrypt_new($input, $key) {
	 	$iv = "@@@@&&&&####$$$$";
		$key = html_entity_decode($key);

		if(function_exists('openssl_encrypt')){
			$data = openssl_encrypt ( $input , "AES-128-CBC" , $key, 0, $iv );
		} else {
			$size = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, 'cbc');
			$input = pkcs5Pad($input, $size);
			$td = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', 'cbc', '');
			mcrypt_generic_init($td, $key, $iv);
			$data = mcrypt_generic($td, $input);
			mcrypt_generic_deinit($td);
			mcrypt_module_close($td);
			$data = base64_encode($data);
		}
		return $data;
	}

	 function decrypt_new($encrypted, $key) {
	 	$iv = "@@@@&&&&####$$$$";
		$key = html_entity_decode($key);
		
		if(function_exists('openssl_decrypt')){
			$data = openssl_decrypt ( $encrypted , "AES-128-CBC" , $key, 0, $iv );
		} else {
			$encrypted = base64_decode($encrypted);
			$td = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', 'cbc', '');
			mcrypt_generic_init($td, $key, $iv);
			$data = mdecrypt_generic($td, $encrypted);
			mcrypt_generic_deinit($td);
			mcrypt_module_close($td);
			$data = pkcs5Unpad($data);
			$data = rtrim($data);
		}
		return $data;
	}

	 function generateSignature($params, $key) {
		if(!is_array($params) && !is_string($params)){
			throw new Exception("string or array expected, ".gettype($params)." given");			
		}
		if(is_array($params)){
			$params = getStringByParams($params);			
		}
		return generateSignatureByString($params, $key);
	}

	 function verifySignature($params, $key, $checksum){
		if(!is_array($params) && !is_string($params)){
			throw new Exception("string or array expected, ".gettype($params)." given");
		}
		if(is_array($params)){
			$params = getStringByParams($params);
		}		
		return verifySignatureByString($params, $key, $checksum);
	}

	 function generateSignatureByString($params, $key){
		$salt = generateRandomString(4);
		return calculateChecksum($params, $key, $salt);
	}

	 function verifySignatureByString($params, $key, $checksum){
		$paytm_hash = decrypt_new($checksum, $key);
		$salt = substr($paytm_hash, -4);
		return $paytm_hash == calculateHash($params, $salt) ? true : false;
	}

	 function generateRandomString($length) {
		$random = "";
		srand((double) microtime() * 1000000);

		$data = "9876543210ZYXWVUTSRQPONMLKJIHGFEDCBAabcdefghijklmnopqrstuvwxyz!@#$&_";	

		for ($i = 0; $i < $length; $i++) {
			$random .= substr($data, (rand() % (strlen($data))), 1);
		}

		return $random;
	}

	 function getStringByParams($params) {
		ksort($params);		
		$params = array_map(function ($value){
			return ($value == null) ? "" : $value;
	  	}, $params);
		return implode("|", $params);
	}

	 function calculateHash($params, $salt){
		$finalString = $params . "|" . $salt;
		$hash = hash("sha256", $finalString);
		return $hash . $salt;
	}

	 function calculateChecksum($params, $key, $salt){
		$hashString = calculateHash($params, $salt);
		return encrypt_new($hashString, $key);
	}

	 function pkcs5Pad($text, $blocksize) {
		$pad = $blocksize - (strlen($text) % $blocksize);
		return $text . str_repeat(chr($pad), $pad);
	}

	 function pkcs5Unpad($text) {
		$pad = ord($text[strlen($text) - 1]);
		if ($pad > strlen($text))
			return false;
		return substr($text, 0, -1 * $pad);
	}
?>
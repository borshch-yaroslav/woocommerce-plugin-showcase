<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class OmnisendHelper {

	public static function runCronSynchronization(){

        OmnisendManager::pushAllContactsToOmnisend();
        OmnisendManager::pushAllProductsToOmnisend();
        OmnisendManager::pushAllOrdersToOmnisend(); 
	}


	/*Test Omnisend API key*/
	public static function omnisendAuth($apiKey){

		$authLink = "https://api.omnisend.com/v3/products?limit=1&offset=0";
		$authResult = OmnisendHelper::curlOmnisend( $authLink, "GET", ['apiKey' => $apiKey] );

	    if( $authResult['code'] == 200){
	        return true;
	    } else {
	        return false;
	    }
	}


	/*Omnisend cUrl request wrapper*/ 
	public static function curlOmnisend($link, $method = "POST", $postfields = [] ){

		$apiKey = get_option('omnisend_api_key', null);

		if( is_array($postfields) && isset($postfields['apiKey']) ){
			$apiKey = $postfields['apiKey'];
		}

		$curlResult = [];

	    $curl = curl_init();

	      curl_setopt_array($curl, array(
	      CURLOPT_URL => $link,
	      CURLOPT_RETURNTRANSFER => true,
	      CURLOPT_ENCODING => "",
	      CURLOPT_MAXREDIRS => 10,
	      CURLOPT_TIMEOUT => 30,
	      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	      CURLOPT_CUSTOMREQUEST => $method,
	      CURLOPT_POSTFIELDS => json_encode( $postfields ),
	      CURLOPT_HTTPHEADER => array(
	        "x-api-key: " . $apiKey
	      ),
	    ));

	    $response = curl_exec($curl);
		$response = json_decode($response);	    

	    $code=curl_getinfo($curl,CURLINFO_HTTP_CODE);
	    $code=(int)$code;

	    curl_close($curl);


	    $curlResult['code'] = $code;
	    $curlResult['response'] = $response;

	    return $curlResult;

	}
}
?>
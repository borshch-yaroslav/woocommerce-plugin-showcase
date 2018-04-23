<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

	class EmptyRequiredFieldsException extends Exception {}

	class OmnisendManager {

		/**
		*	Manager's METHODS
		*
		*		pushCartToOmnisend()
		*		pushContactToOmnisend($user)
		*		pushProductToOmnisend($productId)
		*		pushOrderToOmnisend($orderId)
		*
		*		updateOrderStatus($orderId, $statusType, $orderStatus)
		*
		*		deleteProductFromOmnisend($id)
		*		deleteCartFromOmnisend($id)
		*		deleteOrderFromOmnisend($id)
		*
		*		pushAllContactsToOmnisend()
		*		pushAllProductsToOmnisend()
		*		pushAllOrdersToOmnisend()		
		*/


		/*Push or update Woocommerce cart in Omnisend account*/
		public static function pushCartToOmnisend(){

			$preparedCart = OmnisendCart::create();		

			/*If cart created successfully, push cart to Omnisend*/
			if( $preparedCart ){

				/*Try to add new cart*/
				$link = "https://api.omnisend.com/v3/carts";
			    $curlResult = OmnisendHelper::curlOmnisend( $link, "POST", $preparedCart );

			    if( $curlResult['code'] != 200 ){
					/*Update Existing cart*/
					$link = "https://api.omnisend.com/v3/carts/" . get_user_meta( get_current_user_id(), 'omnisend_woocommerce_cart_id', true );			    	
			    	$curlResult = OmnisendHelper::curlOmnisend( $link, "PUT", $preparedCart );
				}
				$returnResult = $curlResult['response'];				
			} else {
				$returnResult = 'One or more required fields are empty or invalid';	
			}

			return $returnResult;
		}


		/*Push or update WP contact in Omnisend account*/
		public static function pushContactToOmnisend($user){

			if( !empty($user) ){

				$preparedContact = OmnisendContact::create($user);

				/*If all required fields are set, push contact to Omnisend*/
				if( $preparedContact ){

					/*Add or Update contact*/
					$link = "https://api.omnisend.com/v3/contacts";
			    	$curlResult = OmnisendHelper::curlOmnisend( $link, "POST", $preparedContact );

					$returnResult = $curlResult['response'];
				} else {
					$returnResult = 'One or more required fields are empty or invalid';	
				}
			} else {
				$returnResult = 'One or more required fields are empty or invalid';	
			}

			return $returnResult;
		}


		/*Push or update product in omnisend account*/
		public static function pushProductToOmnisend( $productId = ''){

			/*Delete product from Omnisend, if it has't Published status*/
			if( get_post_status($productId) !== "publish" ){
				OmnisendManager::deleteProductFromOmnisend($productId);
				return 'Product has not Published status';
			}

			$preparedProduct = OmnisendProduct::create($productId);

			/*If all required fields are set, push product to Omnisend*/
			if( $preparedProduct ){

				$link = "https://api.omnisend.com/v3/products";
			    $curlResult = OmnisendHelper::curlOmnisend( $link, "POST", $preparedProduct );

			    if( $curlResult['code'] != 200 ){
			    	/*If product can't be created, maybe it already exists - try to update*/
					$link = "https://api.omnisend.com/v3/products/" . $productId;			    	
			    	$curlResult = OmnisendHelper::curlOmnisend( $link, "PUT", $preparedProduct );
				}
				$returnResult = $curlResult['response'];	

			} else {
				$returnResult = 'One or more required fields are empty or invalid';	
			}

			return $returnResult;
		}


		/*Push or Woocommerce order to Omnisend account*/
		public static function pushOrderToOmnisend($orderId){

			$preparedOrder = OmnisendOrder::create($orderId);

			/*If all required fields are set, push order to Omnisend*/
			if( $preparedOrder ){

				$link = "https://api.omnisend.com/v3/orders";
			    $curlResult = OmnisendHelper::curlOmnisend( $link, "POST", $preparedOrder );

			    if( $curlResult['code'] != 200 ){
					$link = "https://api.omnisend.com/v3/orders/" . $orderId;			    	
			    	$curlResult = OmnisendHelper::curlOmnisend( $link, "PUT", $preparedOrder );
			    }
				$returnResult = $curlResult['response'];

			} else {
				$returnResult = 'One or more required fields are empty or invalid';
			}

			return $returnResult;
		}



		public static function updateOrderStatus($orderId, $statusType, $orderStatus){
			
			$postData = [];
			if( $statusType == "fulfillment" ){
				$postData["fulfillmentStatus"] = $orderStatus;

				$date = date_create( date('Y-m-d H:i:s') );
				date_sub($date,date_interval_create_from_date_string("1 hours"));
				$postData['fulfillmentStatusDate'] = date_format($date,"Y-m-d\TH:i:s\Z");
			} else {
				$postData["paymentStatus"] = $orderStatus;

				$date = date_create( date('Y-m-d H:i:s') );
				date_sub($date,date_interval_create_from_date_string("1 hours"));
				$postData['paymentStatusDate'] = date_format($date,"Y-m-d\TH:i:s\Z");
			}

			$link = "https://api.omnisend.com/v3/orders/" . $orderId;			    	
			$curlResult = OmnisendHelper::curlOmnisend( $link, "PATCH", $postData );

			return $curlResult['response'];
		}

		//Delete Product
		public static function deleteProductFromOmnisend($id){

			$link = "https://api.omnisend.com/v3/products/" . $id;
			$curlResult = OmnisendHelper::curlOmnisend( $link, "DELETE", [] );

			return $curlResult['response'];
		}

		//Delete Cart
		public static function deleteCartFromOmnisend($id){

			$link = "https://api.omnisend.com/v3/carts/" . $id;
			$curlResult = OmnisendHelper::curlOmnisend( $link, "DELETE", [] );

			return $curlResult['response'];
		}

		//Delete Order
		public static function deleteOrderFromOmnisend($id){

			$link = "https://api.omnisend.com/v3/orders/" . $id;
			$curlResult = OmnisendHelper::curlOmnisend( $link, "DELETE", [] );

			return $curlResult['response'];
		}



		/*Push or update All WP contacts in Omnisend account. Based on pushContactToOmnisend function*/
		public static function pushAllContactsToOmnisend(){

			$args = array('orderby' => 'display_name');
			$wp_user_query = new WP_User_Query($args);
			$users = $wp_user_query->get_results();

			if (!empty($users)) {
				foreach ($users as $user) {
					OmnisendManager::pushContactToOmnisend($user);
				}
			}
		}

		/*Push or update All Woocommerce Products in Omnisend account. Based on pushProductToOmnisend function*/
		public static function pushAllProductsToOmnisend(){

	        $args = array(
	            'post_type'      => 'product',
	            'posts_per_page' => -1
	        );

	        $products = new WP_Query( $args );

	        while ( $products->have_posts() ) : $products->the_post();
	          OmnisendManager::pushProductToOmnisend( get_the_ID() );
	        endwhile;

	        wp_reset_query();
		}

		/*Push All Woocommerce Orders in Omnisend account. Based on pushOrderToOmnisend function*/
		public static function pushAllOrdersToOmnisend(){

			$query = new WC_Order_Query( 
				array(
			    'return' => 'ids',
				) 
			);

			$orderIds = $query->get_orders();

	        for( $i = 0; $i < count($orderIds); $i++ ){
	          OmnisendManager::pushOrderToOmnisend( $orderIds[$i] );
	        }

		}	

	}
?>
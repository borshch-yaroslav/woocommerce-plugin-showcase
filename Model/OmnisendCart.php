<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

	class OmnisendCart {

		/*Required*/
		public $cartID;
		public $email;
		/*Required*/
		public $currency;
		/*Required*/
		public $cartSum;
		/*Required*/		
		public $products = [];


	    public static function create() {
	        try {
	            return new OmnisendCart();
	        } catch (EmptyRequiredFieldsException $exception) {
	            return null;
	        }
	    }


		public function __construct(){

			global $woocommerce;

			$wcCart = $woocommerce->cart->get_cart();

			if( empty($wcCart) ){
				throw new EmptyRequiredFieldsException();
			}

			$this->cartID = get_user_meta( get_current_user_id(), 'omnisend_woocommerce_cart_id', true );
			if ( empty( $this->cartID ) ){
				$random = "" . rand();
				update_user_meta( get_current_user_id(), 'omnisend_woocommerce_cart_id', $random);
				$this->cartID = $random;				
			}

			$email = wp_get_current_user()->user_email;
			if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
			  $this->email = $email;
			}

			$this->currency = get_woocommerce_currency();
	
			$this->cartSum = intval( preg_replace( '#[^\d.]#', '', $woocommerce->cart->get_cart_total() ) * 100 );

			foreach( $wcCart as $wcProduct ){

				$product = [];

				/*Required field*/
				$product['cartProductID'] = $wcProduct['key'];
				/*Required field*/				
				$product['productID'] = "" . $wcProduct['product_id'];
				/*Required field*/				
				$product['variantID'] = "" . $wcProduct['variation_id'];
				if( empty($product['variantID']) ){
					$product['variantID'] = $product['productID'];
				}			
				/*Required field*/
				$product['title'] = wc_get_product( $wcProduct['product_id'] )->name;
				$product['description'] = wc_get_product( $wcProduct['product_id'] )->description;	
				/*Required field*/
				$product['quantity'] = $wcProduct['quantity'];
				/*Required field*/					
				$product['price'] = intval( wc_get_product( $wcProduct['product_id'] )->get_price()  * 100 );	
				$product['productUrl'] = get_permalink( $wcProduct['product_id'] );

				$urlTmp = parse_url( wp_get_attachment_url( wc_get_product( $wcProduct['product_id'] )->image_id ) );
		        if( $urlTmp['path'] !== '' ){
					$product['imageUrl'] = $urlTmp['scheme'] . '://' . $urlTmp['host'] . $urlTmp['path'];
		        }

		        if( !empty($product['cartProductID']) && !empty($product['productID']) && isset($product['variantID']) && !empty($product['title'])
		        	&& !empty($product['quantity']) && isset($product['price']) ){
						array_push( $this->products, $product );
				}
			}
			if( empty($this->cartID) || empty($this->currency) || !isset($this->cartSum) || empty($this->products) ){
				throw new EmptyRequiredFieldsException();
			}
		}
	}
?>
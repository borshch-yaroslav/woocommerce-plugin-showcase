<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
	class OmnisendOrder {

		/*Required*/
		public $orderID;
		public $email;
		public $cartID;
		public $shippingMethod;
		public $discountValue;
		/*Required*/
		public $currency;
		/*Required*/
		public $orderSum;
		public $subTotalSum;
		public $discountSum;
		public $taxSum;
		/*Required*/
		public $createdAt;
		public $paymentMethod;
		public $billingAddress = [];
		public $shippingAddress = [];
		public $products = [];				


	    public static function create($orderId) {
	        try {
	            return new OmnisendOrder($orderId);
	        } catch (EmptyRequiredFieldsException $exception) {
	            return null;
	        }
	    }


		public function __construct($orderId){

			$wcOrder = wc_get_order( $orderId );
			if( empty($wcOrder) ){
				throw new EmptyRequiredFieldsException();
			}

			$wcOrderData = $wcOrder->get_data();		

			$this->orderID = "" . $orderId;
			
			$email = $wcOrderData['billing']['email'];
			if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
			  $this->email = $email;
			}

			$this->cartID = get_user_meta( get_current_user_id(), 'omnisend_woocommerce_cart_id', true);

			foreach( $wcOrder->get_items( 'shipping' ) as $item_id => $shipping_item_obj ){
			    $this->shippingMethod  = $shipping_item_obj->get_name();
			}

			$this->discountValue = intval( $wcOrderData['discount_total'] );
			$this->currency = $wcOrder->get_currency();
			$this->orderSum = intval( $wcOrder->get_total() * 100 );
			$this->subTotalSum = intval( $wcOrder->get_subtotal() * 100 );
			$this->discountSum = intval( $wcOrderData['discountSum'] * 100 );
			$this->taxSum = intval( $wcOrderData['total_tax'] * 100 );

			$date = date_create( date('Y-m-d H:i:s') );
			date_sub($date,date_interval_create_from_date_string("1 hours"));
			$this->createdAt = date_format($date,"Y-m-d\TH:i:s\Z");

			$this->paymentMethod = $wcOrderData['payment_method'];

			$this->billingAddress['firstName'] = $wcOrderData['billing']['first_name'];
			$this->billingAddress['lastName'] = $wcOrderData['billing']['last_name'];
			$this->billingAddress['company'] = $wcOrderData['billing']['company'];
			$this->billingAddress['country'] = $wcOrderData['billing']['country'];
			$this->billingAddress['state'] = $wcOrderData['billing']['state'];
			$this->billingAddress['city'] = $wcOrderData['billing']['city'];
			$this->billingAddress['address'] = $wcOrderData['billing']['address_1'];
			$this->billingAddress['address2'] = $wcOrderData['billing']['address_2'];
			$this->billingAddress['postalCode'] = $wcOrderData['billing']['postcode'];

			$phoneNumber = $wcOrderData['billing']['phone'];
			$phoneNumber = '+' . preg_replace('/[^0-9]/', '', $phoneNumber);
			$regexp = '/^\++?[0-9]\d{6,14}$/';
			preg_match_all($regexp, $phoneNumber, $matches, PREG_SET_ORDER, 0);
			if( count($matches) > 0 ){
				$this->billingAddress['phoneNumber'] = $phoneNumber;
				$this->shippingAddress['phoneNumber'] = $phoneNumber;
			}	

			if( !empty($wcOrderData['shipping']['first_name']) ){
				$this->shippingAddress['firstName'] =  $wcOrderData['shipping']['first_name'];
			} else {
				$this->shippingAddress['firstName'] =  $wcOrderData['billing']['first_name'];
			}

			if( !empty($wcOrderData['shipping']['last_name']) ){
				$this->shippingAddress['lastName'] =  $wcOrderData['shipping']['last_name'];
			} else {
				$this->shippingAddress['lastName'] =  $wcOrderData['billing']['last_name'];
			}

			if( !empty($wcOrderData['shipping']['company']) ){
				$this->shippingAddress['company'] =  $wcOrderData['shipping']['company'];
			} else {
				$this->shippingAddress['company'] =  $wcOrderData['billing']['company'];
			}

			if( !empty($wcOrderData['shipping']['country']) ){
				$this->shippingAddress['country'] =  $wcOrderData['shipping']['country'];
			} else {
				$this->shippingAddress['country'] =  $wcOrderData['billing']['country'];
			}

			if( !empty($wcOrderData['shipping']['state']) ){
				$this->shippingAddress['state'] =  $wcOrderData['shipping']['state'];
			} else {
				$this->shippingAddress['state'] =  $wcOrderData['billing']['state'];
			}

			if( !empty($wcOrderData['shipping']['city']) ){
				$this->shippingAddress['city'] =  $wcOrderData['shipping']['city'];
			} else {
				$this->shippingAddress['city'] =  $wcOrderData['billing']['city'];
			}

			if( !empty($wcOrderData['shipping']['address_1']) ){
				$this->shippingAddress['address'] =  $wcOrderData['shipping']['address_1'];
			} else {
				$this->shippingAddress['address'] =  $wcOrderData['billing']['address_1'];
			}															

			if( !empty($wcOrderData['shipping']['address_2']) ){
				$this->shippingAddress['address2'] =  $wcOrderData['shipping']['address_2'];
			} else {
				$this->shippingAddress['address2'] =  $wcOrderData['billing']['address_2'];
			}	

			if( !empty($wcOrderData['shipping']['postcode']) ){
				$this->shippingAddress['postalCode'] =  $wcOrderData['shipping']['postcode'];
			} else {
				$this->shippingAddress['postalCode'] =  $wcOrderData['billing']['postcode'];
			}	

			foreach ( $wcOrder->get_items() as $wc_product_id => $wc_product_data) {

				$wcProduct = $wc_product_data->get_product();
				$product = [];
				$productValid = true;

				/*Required field*/
				$product['productID'] = "" . $wc_product_data['product_id'];
				if( empty($product['productID']) ){
					$productValid = false;
				}

				$product['sku'] = $wcProduct->get_sku;

				/*Required field*/
				if( $wcProduct->is_type( 'variable' ) ) {				
					$product['variantID'] = "" . $wc_product_data->get_variation_id();
				} else {
					$product['variantID'] = $product['productID'];
				}
				if( empty($product['variantID']) ){
					$productValid = false;
				}

				$product['variantTitle'] = $wc_product_data->get_name();

				/*Required field*/		
				$product['title'] = $wcProduct->get_name();
				if( empty($product['title']) ){
					$productValid = false;
				}

				/*Required field*/
				$product['quantity'] = intval( $wc_product_data->get_quantity() );
				if( !isset($product['quantity']) ){
					$productValid = false;
				}

				/*Required field*/				
				$product['price'] = intval( $wcProduct->get_price() * 100 );
				if( !isset($product['price']) ){
					$productValid = false;
				}

				$product['weight'] = intval( $wcProduct->get_weight() );

				$urlTmp = parse_url( wp_get_attachment_url( $wcProduct->image_id ) );
		        if( $urlTmp['path'] !== '' ){
					$product['imageUrl'] = $urlTmp['scheme'] . '://' . $urlTmp['host'] . $urlTmp['path'];
		        }

				$product['productUrl'] = get_permalink($wc_product_data['product_id']);

				if( $productValid ){
					array_push( $this->products, $product );
				}	
			}
			if( empty($this->orderID) || empty($this->currency) || !isset($this->orderSum) || empty($this->createdAt) ){
				throw new EmptyRequiredFieldsException();
			}

		}

	}
?>
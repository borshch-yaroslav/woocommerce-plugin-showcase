<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
	class OmnisendContact{

		/*Required*/
		public $email;
		public $firstName;
		public $lastName;
		public $country;
		public $state;
		public $city;
		public $address;
		public $postalCode;
		public $phoneNumber;
		/*Required*/
		public $status;
		/*Required*/
		public $statusDate;		

	    public static function create($user) {
	        try {
	            return new OmnisendContact($user);
	        } catch (EmptyRequiredFieldsException $exception) {
	            return null;
	        }
	    }

		public function __construct($user){

			$email = $user->user_email;
			if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
			  $this->email = $email;
			}

			if( get_user_meta( $user->ID, 'first_name', true ) !== '' ){
				$this->firstName = get_user_meta( $user->ID, 'first_name', true );
			} else if( get_user_meta( $user->ID, 'shipping_first_name', true ) !== '' ){
				$this->firstName = get_user_meta( $user->ID, 'shipping_first_name', true );
			} else if( get_user_meta( $user->ID, 'billing_first_name', true ) !== '' ){
				$this->firstName = get_user_meta( $user->ID, 'billing_first_name', true );
			} else {
				$this->firstName = $user->display_name;
			}

			if( get_user_meta( $user->ID, 'last_name', true ) !== '' ){
				$this->lastName = get_user_meta( $user->ID, 'last_name', true );
			} else if(  get_user_meta( $user->ID, 'shipping_last_name', true ) !== '' ) {
				$this->lastName = get_user_meta( $user->ID, 'shipping_last_name', true );
			} else {
				$this->lastName = get_user_meta( $user->ID, 'billing_last_name', true );
			}

			if( get_user_meta( $user->ID, 'billing_country', true ) !== '' ){
				$this->country = get_user_meta( $user->ID, 'billing_country', true );
			} else {
				$this->country = get_user_meta( $user->ID, 'shipping_country', true );
			}

			if( get_user_meta( $user->ID, 'billing_state', true ) !== '' ){
				$this->state = get_user_meta( $user->ID, 'billing_state', true );
			} else {
				$this->state = get_user_meta( $user->ID, 'shipping_state', true );
			}

			if( get_user_meta( $user->ID, 'billing_city', true ) !== '' ){
				$this->city = get_user_meta( $user->ID, 'billing_city', true );
			} else {
				$this->city = get_user_meta( $user->ID, 'shipping_city', true );
			}					

			$address1 = '';
			$address2 = '';
			if( get_user_meta( $user->ID, 'billing_address_1', true ) !== '' ){
				$address1 = get_user_meta( $user->ID, 'billing_address_1', true );
			} else {
				$address1 = get_user_meta( $user->ID, 'shipping_address_1', true );
			}
			if( get_user_meta( $user->ID, 'billing_address_2', true ) !== '' ){
				$address2 = get_user_meta( $user->ID, 'billing_address_2', true );
			} else {
				$address2 = get_user_meta( $user->ID, 'shipping_address_2', true );
			}
			$this->address = $address1 . ' ' . $address2;

			if( get_user_meta( $user->ID, 'billing_postcode', true ) !== '' ){
				$this->postalCode = get_user_meta( $user->ID, 'billing_postcode', true );
			} else {
				$this->postalCode = get_user_meta( $user->ID, 'shipping_postcode', true );
			}					

			$phoneNumber = get_user_meta( $user->ID, 'billing_phone', true );
			$phoneNumber = '+' . preg_replace('/[^0-9]/', '', $phoneNumber);
			$regexp = '/^\++?[0-9]\d{6,14}$/';
			preg_match_all($regexp, $phoneNumber, $matches, PREG_SET_ORDER, 0);
			if( count($matches) > 0 ){
				$this->phoneNumber = $phoneNumber;
			}				

			$this->status = 'nonSubscribed';

			$date = date_create( date('Y-m-d H:i:s') );
			date_sub($date,date_interval_create_from_date_string("1 hours"));
			$this->statusDate = date_format($date,"Y-m-d\TH:i:s\Z");

			if( empty($this->email) || empty($this->status) || empty($this->statusDate) ){
				throw new EmptyRequiredFieldsException();
			}

		}
	}
?>
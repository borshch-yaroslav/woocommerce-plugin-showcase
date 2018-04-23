<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/*Update Omnisend accountID for code snippet in footer*/
add_action('wp_ajax_save_omnisend_account_id_main', 'save_omnisend_account_id_main');
add_action('wp_ajax_nopriv_save_omnisend_account_id_main', 'save_omnisend_account_id_main');
function save_omnisend_account_id_main(){

  $response = [];

  if( isset($_POST['omnisend_account_id']) ){
      
      $response['success'] = true;

      /*Valdiate accountID string*/
      if ( !ctype_xdigit($_POST['omnisend_account_id']) || strlen($_POST['omnisend_account_id']) != 24 ) {
        $response['success'] = false;
      }

      if( $response['success']  ){
        /*Save Account ID into database*/
        update_option('omnisend_account_id', $_POST['omnisend_account_id']);

        $response['omnisend_account_id'] = $_POST['omnisend_account_id'];
        $response['body'] = 'Omnisend Account ID updated successfully!';
      } else {
        $response['body'] = 'Invalid Omnisend Account ID Format';
      }
  }

  echo json_encode($response);

  exit;
}

/*Push cart to Omnisend on Add to cart or Cart Update button click*/
add_action('wp_ajax_push_cart_to_omnisend', 'push_cart_to_omnisend');
add_action('wp_ajax_nopriv_push_cart_to_omnisend', 'push_cart_to_omnisend');
function push_cart_to_omnisend(){

  $response = [];

  $response['success'] = true;

  $response['cart'] = OmnisendManager::pushCartToOmnisend();

  echo json_encode($response);

  exit;
}

/*Update Omnisend API key*/
add_action('wp_ajax_save_omnisend_api_key', 'save_omnisend_api_key');
add_action('wp_ajax_nopriv_save_omnisend_api_key', 'save_omnisend_api_key');
function save_omnisend_api_key(){

  $response = [];

  if( isset($_POST['omnisend_api_key']) ){
      /*Check if API key is valid*/
      $response['success'] = OmnisendHelper::omnisendAuth($_POST['omnisend_api_key']);

      if( $response['success']  ){

        /*Save API key into database*/
        update_option('omnisend_api_key', $_POST['omnisend_api_key']);

        /*Cron synchronization*/
        wp_schedule_single_event( time(), 'omnisend_init' ); 

        $response['api_key'] = $_POST['omnisend_api_key'];
        $response['body'] = 'API key setted successfully! All Contacts, Products and Orders will be synchronize with Omnisend in Background Process.';
      } else {
        $response['body'] = 'Invalid API Key';
      }
  }

  echo json_encode($response);

  exit;
}
?>
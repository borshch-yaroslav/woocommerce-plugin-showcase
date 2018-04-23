<?php
/*Hook for triggering action on product  create, update or import*/
add_action( 'save_post', 'omnisend_on_product_change');
function omnisend_on_product_change( $post_id ) {
    if ( get_post_type( $post_id ) == 'product'
        && in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
            OmnisendManager::pushProductToOmnisend($post_id);
    }
}
/*Hook for triggering action on product moved to trash*/
add_action( 'trashed_post', 'omnisend_on_product_trashed' );
function omnisend_on_product_trashed( $post_id ){
    if ( get_post_type( $post_id ) == 'product'
        && in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
            OmnisendManager::deleteProductFromOmnisend($post_id);
    }
}


/*Hook for triggering action on user create or update*/
add_action( 'user_register', 'omnisend_on_user_update', 10, 1 );
add_action( 'profile_update', 'omnisend_on_user_update', 10, 2 );
function omnisend_on_user_update( $user_id, $old_user_data) {
    if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {	
		OmnisendManager::pushContactToOmnisend( get_userdata( $user_id ) );
	}
}


/**ORDERS*/
/*Hook for triggering action when order created*/
add_action( 'woocommerce_thankyou', 'order_created_omnisend',  10, 1  );
function order_created_omnisend($order_id){
	OmnisendManager::pushOrderToOmnisend( $order_id );

    /*Delete Cart from Omnisend, and clean cart session when order is done*/
    OmnisendManager::deleteCartFromOmnisend( get_user_meta( get_current_user_id(), 'omnisend_woocommerce_cart_id', true ) );
	update_user_meta( get_current_user_id(), 'omnisend_woocommerce_cart_id', '');
}

/**Fulfillment statuses*/
/*Hook for triggering action when order staus is changed to Processing*/
add_action( 'woocommerce_order_status_processing', 'order_processing_omnisend' );
function order_processing_omnisend($order_id){
    OmnisendManager::updateOrderStatus( $order_id, "fulfillment", "inProgress" ); 
}
/*Hook for triggering action when order staus is changed to Processing*/
add_action( 'woocommerce_order_status_completed', 'order_completed_omnisend' );
function order_completed_omnisend($order_id){
    OmnisendManager::updateOrderStatus( $order_id, "fulfillment", "fulfilled" ); 
}

/**Payment statuses*/
/*Hook for triggering action when order staus is changed to Pending*/
add_action( 'woocommerce_order_status_pending', 'order_pending_omnisend' );
function order_pending_omnisend($order_id){
    OmnisendManager::updateOrderStatus( $order_id, "payment", "awaitingPayment" ); 
}
/*Hook for triggering action when order staus is changed to Cancelled*/
add_action( 'woocommerce_order_status_cancelled', 'order_cancelled_omnisend' );
function order_cancelled_omnisend($order_id){
    OmnisendManager::updateOrderStatus( $order_id, "payment", "voided" ); 
}
/*Hook for triggering action when order staus is changed to Refunded*/
add_action( 'woocommerce_order_status_refunded', 'order_refunded_omnisend' );
function order_refunded_omnisend($order_id){
    OmnisendManager::updateOrderStatus( $order_id, "payment", "refunded" ); 
}

/*Add hook for background Synchroniztion*/
function omnisendInitialSynchronization() {
    /*Synchronize existing Products, Orders and Contacts with Omnisend*/
    OmnisendHelper::runCronSynchronization(); 
}
add_action( 'omnisend_init', 'omnisendInitialSynchronization' );


/*Add code snippet to the footer, if account ID is setted*/
add_action( 'wp_footer', function () {

    if( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ){

        $omnisend_account_id = get_option('omnisend_account_id', null);
        if ( $omnisend_account_id !==  null ) {
        ?>

            <script type=" text/javascript">
                //OMNISEND-SNIPPET-SOURCE-CODE-V1
                window.soundest = window.soundest || []; 
                soundest. push([ "accountID", "<?php echo get_option('omnisend_account_id', null); ?>"]); 
                ! function(){ var e=document. createElement( "script");e.type= "text/javascript",e. async=! 0,e.src= "https://omnisrc.com/inshop/launcher.js"; var t=document. getElementsByTagName( "script")[ 0];t.parentNode. insertBefore(e,t)}();
            </script>

<?php   }
    }
});
?>
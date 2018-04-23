jQuery(document).ready(function($){


	$('body').on('click', '.ajax_add_to_cart', function(e){
		if( !$(this).hasClass('disabled') ){
			pushCartToOmnisend();
		}
	});

	$('body').on('click', 'button[name="update_cart"]', function(e){
		var attr = $(this).attr('disabled');
		if (typeof attr === typeof undefined || attr !== false) {
			pushCartToOmnisend();
		}

	});	


	/*Send cart data to Omnisend*/
	function pushCartToOmnisend(){
		setTimeout(function(){

			$.ajax({
	            type: 'POST',
	            url: '/wp-admin/admin-ajax.php',
	            data: {
		            action: 'push_cart_to_omnisend',
	            },
	            beforeSend: function(){
	            },
	            success: function(response)
	            {
	            	response = $.parseJSON(response);
	            	if( response['success'] ){} else {}
	            },
	            error: function(errorThrown)
	            {
	            },
	            complete: function(textStatus)
	            {
	            }
	        });
		}, 250);
	}

	if( $('body').hasClass('woocommerce') ){
		pushCartToOmnisend();
	}

});
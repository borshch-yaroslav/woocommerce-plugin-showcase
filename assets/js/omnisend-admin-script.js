jQuery(document).ready(function($){

	$('body').append('<div class="omnisend-ajax-modal"></div>');

	$(document).on({
	    ajaxStart: function() { $("body").addClass("omnisend-ajax-loading");    },
	     ajaxStop: function() { $("body").removeClass("omnisend-ajax-loading"); }    
	});

	/*Process Account id update form*/
	$('#account-id-form').submit(function(e){
		e.preventDefault();

		$('.response-message-accountid').html('');
		$('.response-message-accountid').removeClass('omnisend-warning');
		$('.response-message-accountid').removeClass('omnisend-success');

		if( $('#account-id').val() !== '' ) {

			$.ajax({
	            type: 'POST',
	            url: '/wp-admin/admin-ajax.php',
	            data: {
		            action: 'save_omnisend_account_id_main',
		            omnisend_account_id: $('#account-id').val()
	            },
	            beforeSend: function(){
	            },
	            success: function(response)
	            {

	            	response = $.parseJSON(response);

	            	if( response['success'] ){

	            		$('.response-message-accountid').addClass('omnisend-success');		
	            		$('.account-id-status').html('<h3>Current Omnisend Account ID: <span class="current-account-id">' + response['omnisend_account_id'] + '</span></h3>');
	            		$('#account-id').val('');
	            	} else {
	            		$('.response-message-accountid').addClass('omnisend-warning');
	            	}

	            	$('.response-message-accountid').html(response['body']);

	            	setTimeout(function(){
						$('.response-message-accountid').html('');
	            	}, 10000);

	            },
	            error: function(errorThrown)
	            {
	            },
	            complete: function(textStatus)
	            {
	            }
	        });

		} else {
			$('.response-message-accountid').html('Empty accountId field');
			$('.response-message-accountid').addClass('omnisend-warning');
	        setTimeout(function(){
				$('.response-message-accountid').html('');
	        }, 7000);
		}
	});


	/*Process API key update form*/
	$('#api-key-form').submit(function(e){
		e.preventDefault();

		$('.response-message-key').html('');
		$('.response-message-key').removeClass('omnisend-warning');
		$('.response-message-key').removeClass('omnisend-success');	

		if( $('#api-key').val() !== '' ) {

			$.ajax({
	            type: 'POST',
	            url: '/wp-admin/admin-ajax.php',
	            data: {
		            action: 'save_omnisend_api_key',
		            omnisend_api_key: $('#api-key').val()
	            },
	            beforeSend: function(){
	            },
	            success: function(response)
	            {

	            	response = $.parseJSON(response);

	            	if( response['success'] ){
	            		$('#api-key').val('');
						$('.response-message-key').addClass('omnisend-success');
	            		$('.api-key-status').html('<h3>Current Omnisend API Key: <span class="omnisend-key">' + response['api_key'] + '</span></h3>');

						$('.synchronize-block').html('<h5>To synchronize existing products click here: <a id="synchronize-existing">Synchronize</a></h5><p class="response-message-sync"></p>');
	            	} else {
	            		$('.response-message-key').addClass('omnisend-warning');
	            	}

	            	$('.response-message-key').html(response['body']);

	            	setTimeout(function(){
						$('.response-message-key').html('');
	            	}, 10000);

	            },
	            error: function(errorThrown)
	            {
	            },
	            complete: function(textStatus)
	            {			
	            }
	        });

		} else {
			$('.response-message-key').addClass('omnisend-warning');
			$('.response-message-key').html('Empty key field');
	        setTimeout(function(){
				$('.response-message-key').html('');
	        }, 10000);
		}
	});

});
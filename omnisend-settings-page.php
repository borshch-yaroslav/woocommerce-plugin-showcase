<?php
/*Plugin settings View page*/
function omnisend_settings_page(){

	/*Check if WooCommerce is active*/
	if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {		

		$omnisend_api_key = get_option('omnisend_api_key', null);
		$omnisend_account_id = get_option('omnisend_account_id', null);

	?>

		<div class="omnisend-page">
			<h1>Welcome to Omnisend Woocommerce Plugin!</h1>

			<div class="account-id-section">
				<div class="account-id-status">
					<?php
						if ($omnisend_account_id !==  null) {
							echo '<h3>Current Omnisend Account ID: <span class="current-account-id">' . $omnisend_account_id . '</span></h3>';
						} else {
							echo '<h4><a target="_blank" href="https://app.omnisend.com/registration/#/step-1">Sign up on Omnisend</a></h4>';
							echo '<h4>Or set Omnisend Account ID, if you already have account</h4>';
						}
						?>
				</div>	

				<form id="account-id-form">
					<input type="text" name="account-id" id="account-id" placeholder="Omnisend Account ID">
					<input type="submit" name="account-id-submit" id="account-id-submit" value="Save">
				</form>
				<h4 class="response-message-accountid"></h4>
			</div>


			<div class="api-key-section">
				<div class="api-key-status">
					<?php
					if ($omnisend_api_key !==  null) {
						echo '<h3>Current Omnisend API Key: <span class="omnisend-key">' . $omnisend_api_key . '</span></h3>';
					} else {
						echo '<h3>Please, Configure Omnisend API Key to Start Push Product Info To Omnisend</h3>';
					}
					?>
				</div>

				<form id="api-key-form">
					<input type="text" name="api-key" id="api-key" placeholder="New API key">
					<input type="submit" name="api-key-submit" id="api-key-submit" value="Save">
				</form>
				<h4 class="response-message-key"></h4>

			</div>

		</div>


	<?php } else {
		/*If Woocommerce is not Installed - message with Woocommerce installation link*/
		$install_link = esc_url( network_admin_url('plugin-install.php?s=woocommerce&tab=search&type=term') ) ;
	?>

		<div class="omnisend-page">
			<h2 class="omnisend-warning">Please, Install <a href="<?php echo $install_link; ?>">Woocommerce</a>!</h2>
		</div>
<?php }
}
?>
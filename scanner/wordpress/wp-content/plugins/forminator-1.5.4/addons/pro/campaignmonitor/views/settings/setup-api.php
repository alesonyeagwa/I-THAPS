<?php
// defaults
$vars = array(
	'error_message'   => '',
	'api_key'         => '',
	'api_key_error'   => '',
	'client_id'       => '',
	'client_id_error' => '',
	'client_name'     => '',
);
/** @var array $template_vars */
foreach ( $template_vars as $key => $val ) {
	$vars[ $key ] = $val;
}

?>
<div class="integration-header">
	<h3 class="sui-box-title" id="dialogTitle2"><?php echo esc_html( sprintf( __( 'Configure %1$s API', Forminator::DOMAIN ), 'Campaign Monitor' ) ); ?></h3>
	<p><?php esc_html_e( 'Setup Campaign Monitor API Access.', Forminator::DOMAIN ); ?></p>
	<?php if ( ! empty( $vars['client_name'] ) ) : ?>
		<span class="sui-notice sui-notice-success"><p><?php esc_html_e( 'Campaign Monitor Integrations currently connected to API Client : ', Forminator::DOMAIN ); ?>
				<strong><?php echo esc_html( $vars['client_name'] ); ?></strong></p></span>
	<?php endif; ?>
	<?php if ( ! empty( $vars['error_message'] ) ) : ?>
		<span class="sui-notice sui-notice-error"><p><?php echo esc_html( $vars['error_message'] ); ?></p></span>
	<?php endif; ?>
</div>
<form>
	<div class="sui-form-field <?php echo esc_attr( ! empty( $vars['api_key_error'] ) ? 'sui-form-field' : '' ); ?>">
		<label class="sui-label"><?php esc_html_e( 'API Key', Forminator::DOMAIN ); ?></label>
		<div class="sui-field-with-icon">
			<input
					class="sui-form-control"
					name="api_key" placeholder="<?php echo esc_attr( sprintf( __( 'Enter %1$s API Key', Forminator::DOMAIN ), 'Campaign Monitor' ) ); ?>"
					value="<?php echo esc_attr( $vars['api_key'] ); ?>">
			<i class="sui-icon-key" aria-hidden="true"></i>
		</div>
		<?php if ( ! empty( $vars['api_key_error'] ) ) : ?>
			<span class="sui-error-message"><?php echo esc_html( $vars['api_key_error'] ); ?></span>
		<?php endif; ?>
		<span class="sui-description">
			<?php esc_html_e( 'To obtain Campaign Monitor API Credentials, follow these steps :', Forminator::DOMAIN ); ?>
			<ol class="instructions" id="apikey-instructions">
				<li><?php echo __( 'Login to your Campaign Monitor account <a href="https://login.createsend.com/l" target="_blank">here</a>.', Forminator::DOMAIN ); //wpcs: xss ok. ?></li>
				<li><?php echo __( 'Go to Account Settings, then navigate to <strong>API Keys</strong> section.', Forminator::DOMAIN ); //wpcs: xss ok. ?></li>
				<li><?php echo __( 'Click on <strong>Show API Key</strong>, select and copy on the shown up value.', Forminator::DOMAIN ); //wpcs: xss ok. ?></li>
			</ol>
		</span>
	</div>

	<div class="sui-form-field <?php echo esc_attr( ! empty( $vars['client_id_error'] ) ? 'sui-form-field' : '' ); ?>">
		<label class="sui-label"><?php esc_html_e( 'Client ID', Forminator::DOMAIN ); ?></label>
		<input
				class="sui-form-control"
				name="client_id" placeholder="<?php echo esc_attr( sprintf( __( 'Enter %1$s Client ID', Forminator::DOMAIN ), 'Campaign Monitor' ) ); ?>"
				value="<?php echo esc_attr( $vars['client_id'] ); ?>">
		<?php if ( ! empty( $vars['client_id_error'] ) ) : ?>
			<span class="sui-error-message"><?php echo esc_html( $vars['client_id_error'] ); ?></span>
		<?php endif; ?>
		<span class="sui-description">
			<?php echo __( 'Client ID is optional, unless you are on <strong>Agency-Mode</strong>, then you can find your desired Client ID on the <strong>Account Settings</strong> > <strong>API Keys</strong>',
			               Forminator::DOMAIN ); //wpcs: xss ok. ?>
		</span>
	</div>

</form>

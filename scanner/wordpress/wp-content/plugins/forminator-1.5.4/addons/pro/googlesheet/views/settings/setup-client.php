<?php
// defaults
$vars = array(
	'token'               => '',
	'error_message'       => '',
	'client_id'           => '',
	'client_secret'       => '',
	'client_secret_error' => '',
	'client_id_error'     => '',
	'redirect_url'        => '',
);
/** @var array $template_vars */
foreach ( $template_vars as $key => $val ) {
	$vars[ $key ] = $val;
}
?>
<div class="integration-header">
	<h3 class="sui-box-title" id="dialogTitle2"><?php echo esc_html( sprintf( __( 'Setup %1$s Client', Forminator::DOMAIN ), 'Google Sheets' ) ); ?></h3>
	<?php if ( ! empty( $vars['token'] ) ) : ?>
		<p><?php esc_html_e( 'Your Google Sheets account is already authorized. Edit info bellow to re-authorize.', Forminator::DOMAIN ); ?> </p>
	<?php else : ?>
		<p><?php esc_html_e( 'Setup Google Client to be used by Forminator to communicating with Google Sheets server.', Forminator::DOMAIN ); ?></p>
		<?php if ( ! empty( $vars['error_message'] ) ) : ?>
			<span class="sui-notice sui-notice-error"><p><?php echo esc_html( $vars['error_message'] ); ?></p></span>
		<?php endif; ?>
	<?php endif ?>
</div>
<form>
	<div class="sui-form-field <?php echo esc_attr( ! empty( $vars['client_id_error'] ) ? 'sui-form-field-error' : '' ); ?>">
		<label class="sui-label"><?php esc_html_e( 'Client ID', Forminator::DOMAIN ); ?></label>
		<input
				class="sui-form-control"
				name="client_id" placeholder="<?php echo esc_attr( __( 'Client ID', Forminator::DOMAIN ) ); ?>"
				value="<?php echo esc_attr( $vars['client_id'] ); ?>">
		<?php if ( ! empty( $vars['client_id_error'] ) ) : ?>
			<span class="sui-error-message"><?php echo esc_html( $vars['client_id_error'] ); ?></span>
		<?php endif; ?>
	</div>
	<div class="sui-form-field <?php echo esc_attr( ! empty( $vars['client_secret_error'] ) ? 'sui-form-field-error' : '' ); ?>">
		<label class="sui-label"><?php esc_html_e( 'Client Secret', Forminator::DOMAIN ); ?></label>
		<input
				class="sui-form-control"
				name="client_secret" placeholder="<?php echo esc_attr( __( 'Client Secret', Forminator::DOMAIN ) ); ?>"
				value="<?php echo esc_attr( $vars['client_secret'] ); ?>">
		<?php if ( ! empty( $vars['client_secret_error'] ) ) : ?>
			<span class="sui-error-message"><?php echo esc_html( $vars['client_secret_error'] ); ?></span>
		<?php endif; ?>
		<span class="sui-description">
				<?php esc_html_e( 'Follow these instructions to retrieve your Client ID and Secret.', Forminator::DOMAIN ); ?>
			<ol class="instructions" id="clientid-instructions">
					<li>
						<?php echo sprintf(
							__( 'Go to the %1$s', Forminator::DOMAIN ),
							'<a href="https://console.developers.google.com/cloud-resource-manager" target="_blank">' . __( 'Google API Console', Forminator::DOMAIN ) . '</a>'
						); //wpcs: xss ok?>
					</li>
					<li>
						<?php esc_html_e(
							'Select an existing project or create a new one. If creating a new project, you will need to enter a name, but the ID is not important and can be ignored.',
							Forminator::DOMAIN
						); ?>
					</li>
					<li>
						<?php echo sprintf(
							__( 'Once the Project creation is completed go to the %1$s. Here you need to enable the %2$s and %3$s.', Forminator::DOMAIN ),
							'<strong>' . __( 'API Manager', Forminator::DOMAIN ) . '</strong>',
							'<strong>' . __( 'Google Drive API', Forminator::DOMAIN ) . '</strong>',
							'<strong>' . __( 'Google Sheets API', Forminator::DOMAIN ) . '</strong>'
						); //wpcs: xss ok?>
					</li>
					<li>
						<?php echo sprintf(
							__( 'Next, go to the %1$s section.', Forminator::DOMAIN ),
							'<strong>' . __( 'API Manager &gt; Credentials', Forminator::DOMAIN ) . '</strong>'
						); //wpcs: xss ok?>
						<ol>
							<li>
								<?php echo sprintf(
									__( 'Click %1$s.', Forminator::DOMAIN ),
									'<strong>' . __( 'Create Credentials &gt; OAuth 2.0 client ID', Forminator::DOMAIN ) . '</strong>'
								); //wpcs: xss ok?>
							</li>
							<li>
								<?php echo sprintf(
									__( 'In the popup select the %1$s as %2$s.', Forminator::DOMAIN ),
									'<strong>' . __( 'Application Type', Forminator::DOMAIN ) . '</strong>',
									'<strong>' . __( 'Web application', Forminator::DOMAIN ) . '</strong>'
								); //wpcs: xss ok?>
							</li>
							<li>
								<?php echo sprintf(
									__( 'In the field %1$s, put this value below', Forminator::DOMAIN ),
									'<strong>' . __( 'Authorized redirect URI', Forminator::DOMAIN ) . '</strong>'
								); //wpcs: xss ok?>
								<pre class="sui-code-snippet"><?php echo esc_html( ! empty( $vars['redirect_url'] ) ? $vars['redirect_url'] : '' ); ?></pre>.</li>
							<li>
								<?php echo sprintf(
									__( 'Then click the %1$s button.', Forminator::DOMAIN ),
									'<strong>' . __( 'Create Client ID', Forminator::DOMAIN ) . '</strong>'
								); //wpcs: xss ok?>
							</li>
						</ol>
					<li>
					<?php esc_html_e(
						'After the popup closes copy the Client ID and Client Secret from the Google page and paste into the form fields.',
						Forminator::DOMAIN
					); ?>
					</li>
				</ol>
			</span>
	</div>
</form>

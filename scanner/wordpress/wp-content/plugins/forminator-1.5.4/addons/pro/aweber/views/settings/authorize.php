<?php
// defaults
$vars = array(
	'account_id'   => 0,
	'auth_url'     => '',
	'is_connected' => false,
);
/** @var array $template_vars */
foreach ( $template_vars as $key => $val ) {
	$vars[ $key ] = $val;
}
?>
<div class="integration-header">
	<h3 class="sui-box-title" id="dialogTitle2"><?php echo esc_html( sprintf( __( 'Connect %1$s', Forminator::DOMAIN ), 'AWeber' ) ); ?></h3>
	<?php if ( ! empty( $vars['account_id'] ) ) : ?>
		<span class="sui-notice sui-notice-success">
		<p><?php esc_html_e( 'Your AWeber account is already authorized.', Forminator::DOMAIN ); ?> </p>
		</span>
	<?php else : ?>
		<p><?php esc_html_e( 'Authorize Forminator to connect with your AWeber account in order to send data from your forms.', Forminator::DOMAIN ); ?></p>
	<?php endif ?>
</div>
<?php if ( empty( $vars['account_id'] ) ) : ?>
	<a href="<?php echo esc_attr( $vars['auth_url'] ); ?>" target="_blank" class="sui-button sui-button-primary forminator-addon-connect"><?php esc_html_e( 'AUTHORIZE', Forminator::DOMAIN ); ?></a>
<?php endif ?>
<?php if ( $vars['is_connected'] ) : ?>
	<button class="sui-button sui-button-ghost forminator-addon-disconnect"><?php esc_html_e( 'DISCONNECT', Forminator::DOMAIN ); ?></button>
<?php endif ?>


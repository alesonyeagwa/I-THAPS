<?php
// defaults
$vars = array(
	'error_message' => '',
	'is_connected'  => false,
);
/** @var array $template_vars */
foreach ( $template_vars as $key => $val ) {
	$vars[ $key ] = $val;
}

?>
<div class="integration-header">
	<h3 class="sui-box-title" id="dialogTitle2"><?php echo esc_html( sprintf( __( 'Activate %1$s', Forminator::DOMAIN ), 'Zapier' ) ); ?></h3>
	<p><?php esc_html_e( 'Activate Zapier to start using it on your forms.', Forminator::DOMAIN ); ?></p>
	<?php if ( ! empty( $vars['is_connected'] ) ) : ?>
		<span class="sui-notice sui-notice-success"><p><?php esc_html_e( 'Zapier is already active.', Forminator::DOMAIN ); ?></p></span>
	<?php endif; ?>
	<?php if ( ! empty( $vars['error_message'] ) ) : ?>
		<span class="sui-notice sui-notice-error"><p><?php echo esc_html( $vars['error_message'] ); ?></p></span>
	<?php endif; ?>
</div>
<form>
	<input type="hidden" value="1" name="connect">
</form>

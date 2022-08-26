<?php
$form_id = $_POST['id'];// WPCS: CSRF ok. varified on admin ajax
$nonce   = wp_create_nonce( 'forminatorCustomFormRequest' );

$exportable = array();
$model      = Forminator_Custom_Form_Model::model()->load( $form_id );
if ( $model instanceof Forminator_Custom_Form_Model ) {
	$exportable = $model->to_exportable_data();
} ?>

<div class="sui-box-body wpmudev-popup-form">

	<div class="sui-form-field">
		<textarea class="sui-form-control" readonly="readonly" rows="10"><?php echo esc_attr( wp_json_encode( $exportable ) ); ?></textarea>
		<span class="sui-description"><?php esc_html_e( 'Copy ALL text above, and paste to import dialog.', Forminator::DOMAIN ); ?></span>
	</div>

</div>

<div class="sui-box-footer">

	<button class="sui-button forminator-popup-cancel" data-a11y-dialog-hide="forminator-popup"><?php esc_html_e( 'Close', Forminator::DOMAIN ); ?></button>

	<div class="sui-actions-right">

		<form action="<?php echo esc_attr( admin_url( 'admin.php?page=forminator-cform' ) ); ?>" method="post">
			<input type="hidden" name="formninator_action" value="export">
			<input type="hidden" name="forminatorNonce" value="<?php echo esc_attr( $nonce ); ?>">
			<input type="hidden" name="id" value="<?php echo esc_attr( $form_id ); ?>">
			<button class="sui-button sui-button-primary"><i class="sui-icon-download" aria-hidden="true"></i> <?php esc_html_e( 'Download', Forminator::DOMAIN ); ?></button>
		</form>

	</div>

</div>

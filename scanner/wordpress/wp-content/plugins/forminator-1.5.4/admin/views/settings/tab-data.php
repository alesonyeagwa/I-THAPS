<?php
$nonce = wp_create_nonce( 'forminator_save_privacy_settings' );
?>

<div class="sui-box" data-nav="data" style="display: none;">

	<form class="forminator-settings-save" action="">

		<div class="sui-box-body">

			<?php $this->template( 'settings/data/forms-privacy' ); ?>

			<?php $this->template( 'settings/data/polls-privacy' ); ?>

			<?php $this->template( 'settings/data/uninstall' ); ?>

		</div>

		<div class="sui-box-footer">

			<div class="sui-actions-right">

				<button class="sui-button sui-button-blue wpmudev-action-done" data-title="<?php esc_attr_e( "Data settings", Forminator::DOMAIN ); ?>" data-action="privacy_settings" data-nonce="<?php echo esc_attr( $nonce ); ?>">
					<span class="sui-loading-text"><?php esc_html_e( 'Save Settings', Forminator::DOMAIN ); ?></span>
					<i class="sui-icon-loader sui-loading" aria-hidden="true"></i>
				</button>

			</div>

		</div>

	</form>

</div>

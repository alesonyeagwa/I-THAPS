<?php $count = $this->countModules(); ?>

<h1 class="sui-header-title"><?php esc_html_e( "Polls", Forminator::DOMAIN ); ?></h1>

<div class="sui-actions-left">

	<button class="sui-button sui-button-blue wpmudev-button-open-modal" data-modal="polls"><i class="sui-icon-plus" aria-hidden="true"></i> <?php esc_html_e( 'Create', Forminator::DOMAIN ); ?></button>

	<?php if ( Forminator::is_import_export_feature_enabled() ) : ?>

		<a href="#"
			class="sui-button wpmudev-open-modal"
			data-modal="import_poll"
			data-modal-title=""
			data-nonce="<?php echo esc_attr( wp_create_nonce( 'forminator_popup_import_poll' ) ); ?>">
			<i class="sui-icon-upload-cloud" aria-hidden="true"></i> <?php esc_html_e( 'Import', Forminator::DOMAIN ); ?>
		</a>

	<?php endif; ?>

</div>

<div class="sui-actions-right">

	<a href="https://premium.wpmudev.org/docs/wpmu-dev-plugins/forminator/#chapter-3" target="_blank" class="sui-button sui-button-ghost"><i class="sui-icon-academy"></i> <?php esc_html_e( "View Documentation", Forminator::DOMAIN ); ?></a>

</div>

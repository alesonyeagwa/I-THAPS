<?php
$uninstall = get_option( "forminator_uninstall_clear_data", false );
?>

<div class="sui-box-settings-row">

	<div class="sui-box-settings-col-1">
		<span class="sui-settings-label"><?php esc_html_e( 'Uninstall Plugin', Forminator::DOMAIN ); ?></span>
		<span class="sui-description"><?php esc_html_e( 'Choose whether to keep or delete all the data when the plugin is uninstalled.', Forminator::DOMAIN ); ?></span>
	</div>

	<div class="sui-box-settings-col-2">

		<div class="sui-side-tabs">

			<div class="sui-tabs-menu">

				<label for="delete_uninstall-false" class="sui-tab-item<?php echo $uninstall ? '' : ' active'; ?>">
					<input type="radio"
						name="delete_uninstall"
						value="false"
						id="delete_uninstall-false"
						<?php echo esc_attr( checked( $uninstall, false ) ); ?> />
					<?php esc_html_e( 'Keep', Forminator::DOMAIN ); ?>
				</label>

				<label for="delete_uninstall-true" class="sui-tab-item<?php echo $uninstall ? ' active' : ''; ?>">
					<input type="radio"
						name="delete_uninstall"
						value="true"
						id="delete_uninstall-true"
						<?php echo esc_attr( checked( $uninstall, true ) ); ?> />
					<?php esc_html_e( 'Delete', Forminator::DOMAIN ); ?>
				</label>

			</div>

		</div>

	</div>

</div>

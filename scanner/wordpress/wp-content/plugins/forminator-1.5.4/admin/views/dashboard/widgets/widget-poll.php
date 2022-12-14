<div class="sui-box">

	<div class="sui-box-header">

		<h3 class="sui-box-title"><i class="sui-icon-graph-bar" aria-hidden="true"></i><?php esc_html_e( "Polls", Forminator::DOMAIN ); ?></h3>

	</div>

	<div class="sui-box-body">

		<p><?php esc_html_e( 'Create interactive polls to collect users opinions, with lots of dynamic options and settings.', Forminator::DOMAIN ); ?></p>

		<?php if ( 0 === forminator_polls_total() ) { ?>

			<p><button href="/" class="sui-button sui-button-blue wpmudev-open-modal" data-modal="polls"><i class="sui-icon-plus" aria-hidden="true"></i> <?php esc_html_e( 'Create', Forminator::DOMAIN ); ?></button></p>

		<?php } ?>

	</div>

	<?php if ( forminator_polls_total() > 0 ) { ?>

		<table class="sui-table sui-table-flushed">

			<thead>

				<tr>

					<th><?php esc_html_e( 'Name', Forminator::DOMAIN ); ?></th>
					<th class="fui-col-status"></th>

				</tr>

			</thead>

			<tbody>

				<?php foreach( forminator_polls_modules() as $module ) { ?>

					<tr>

						<td class="sui-table-item-title"><?php echo forminator_get_form_name( $module['id'], 'poll'); // WPCS: XSS ok. ?></td>

						<td class="fui-col-status">

							<a href="<?php echo admin_url( 'admin.php?page=forminator-poll&view-stats=' . esc_attr( $module['id'] ) ); // WPCS: XSS ok. ?>"
								class="sui-button-icon sui-tooltip"
								data-tooltip="<?php esc_html_e( 'View Status', Forminator::DOMAIN ); ?>">
								<i class="sui-icon-graph-line" aria-hidden="true"></i>
							</a>

							<div class="sui-dropdown">

								<button class="sui-button-icon sui-dropdown-anchor"
									aria-expanded="false"
									aria-label="<?php esc_html_e( 'More options', Forminator::DOMAIN ); ?>">
									<i class="sui-icon-widget-settings-config" aria-hidden="true"></i>
								</button>

								<ul>

									<li><button class="wpmudev-open-modal"
										data-modal="preview_polls"
										data-modal-title="<?php echo sprintf( "%s - %s", __( "Preview Poll", Forminator::DOMAIN ), forminator_get_form_name( $module['id'], 'poll' ) ); // WPCS: XSS ok. ?>"
										data-form-id="<?php echo esc_attr( $module['id'] ); ?>"
										data-nonce="<?php echo wp_create_nonce( 'forminator_popup_preview_polls' ); // WPCS: XSS ok. ?>">
										<i class="sui-icon-eye" aria-hidden="true"></i> <?php esc_html_e( "Preview", Forminator::DOMAIN ); ?>
									</button></li>

									<li>
										<button class="copy-clipboard" data-shortcode='[forminator_poll id="<?php echo esc_attr( $module['id'] ); ?>"]'><i class="sui-icon-code" aria-hidden="true"></i> <?php esc_html_e( "Copy Shortcode", Forminator::DOMAIN ); ?></button>
									</li>

									<li><a href="<?php echo admin_url( 'admin.php?page=forminator-entries&form_type=forminator_polls&form_id=' . $module['id'] ); // WPCS: XSS ok. ?>"><i class="sui-icon-community-people" aria-hidden="true"></i> <?php esc_html_e( 'View Submissions', Forminator::DOMAIN ); ?></a></li>

									<li><form method="post">
										<input type="hidden" name="formninator_action" value="clone">
										<input type="hidden" name="id" value="<?php echo esc_attr( $module['id'] ); ?>"/>
										<?php wp_nonce_field( 'forminatorPollFormRequest', 'forminatorNonce' ); ?>
										<button type="submit">
											<i class="sui-icon-page-multiple" aria-hidden="true"></i> <?php esc_html_e( 'Duplicate', Forminator::DOMAIN ); ?>
										</button>
									</form></li>

									<?php if ( Forminator::is_import_export_feature_enabled() ) : ?>

										<li><a href="#"
											class="wpmudev-open-modal"
											data-modal="export_poll"
											data-modal-title=""
											data-form-id="<?php echo esc_attr( $module['id'] ); ?>"
											data-nonce="<?php echo esc_attr( wp_create_nonce( 'forminator_popup_export_poll' ) ); ?>">
											<i class="sui-icon-cloud-migration" aria-hidden="true"></i> <?php esc_html_e( 'Export', Forminator::DOMAIN ); ?>
										</a></li>

									<?php endif; ?>

									<li><a href="#"
										class="wpmudev-open-modal"
										data-modal="delete-module"
										data-form-id="<?php echo esc_attr( $module['id'] ); ?>"
										data-nonce="<?php echo wp_create_nonce( 'forminatorPollFormRequest' ); // WPCS: XSS ok. ?>">
										<i class="sui-icon-trash" aria-hidden="true"></i> <?php esc_html_e( 'Delete', Forminator::DOMAIN ); ?>
									</a></li>

								</ul>

							</div>

						</td>

					</tr>

				<?php } ?>

			</tbody>

		</table>

		<div class="sui-box-footer">

			<button class="sui-button sui-button-blue wpmudev-open-modal"
				data-modal="polls">
				<i class="sui-icon-plus" aria-hidden="true"></i> <?php esc_html_e( 'Create', Forminator::DOMAIN ); ?>
			</button>

		</div>

	<?php } ?>

</div>

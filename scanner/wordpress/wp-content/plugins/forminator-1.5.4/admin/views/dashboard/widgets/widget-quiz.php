<div class="sui-box">

	<div class="sui-box-header">

		<h3 class="sui-box-title"><i class="sui-icon-academy" aria-hidden="true"></i><?php esc_html_e( "Quizzes", Forminator::DOMAIN ); ?></h3>

	</div>

	<div class="sui-box-body">

		<p><?php esc_html_e( 'Create fun or challenging quizzes for your visitors to take and share on social media.', Forminator::DOMAIN ); ?></p>

		<?php if ( 0 === forminator_quizzes_total() ) { ?>

			<button class="sui-button sui-button-blue wpmudev-open-modal"
				data-modal="quizzes">
				<i class="sui-icon-plus" aria-hidden="true"></i> <?php esc_html_e( 'Create', Forminator::DOMAIN ); ?>
			</button>

		<?php } ?>

	</div>

	<?php if ( forminator_quizzes_total() > 0 ) { ?>

		<table class="sui-table sui-table-flushed">

			<thead>

				<tr>

					<th><?php esc_html_e( 'Name', Forminator::DOMAIN ); ?></th>
					<th class="fui-col-status"></th>

				</tr>

			</thead>

			<tbody>

				<?php foreach ( forminator_quizzes_modules() as $module ) { ?>

					<tr>

						<td class="sui-table-item-title"><?php echo forminator_get_form_name( $module['id'], 'quiz'); // WPCS: XSS ok. ?></td>

						<td class="fui-col-status">

							<a href="<?php echo admin_url( 'admin.php?page=forminator-quiz&view-stats=' . esc_attr( $module['id'] ) ); // WPCS: XSS ok. ?>"
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
										data-modal="preview_quizzes"
										data-modal-title="<?php echo sprintf( "%s - %s", __( 'Preview Quiz', Forminator::DOMAIN ), forminator_get_form_name( $module['id'], 'quiz' ) ); // WPCS: XSS ok. ?>"
										data-form-id="<?php echo esc_attr( $module['id'] ); ?>"
										data-nonce="<?php echo wp_create_nonce( 'forminator_popup_preview_quizzes' ); // WPCS: XSS ok. ?>">
										<i class="sui-icon-eye" aria-hidden="true"></i> <?php esc_html_e( 'Preview', Forminator::DOMAIN ); ?>
									</button></li>

									<li>
										<button class="copy-clipboard" data-shortcode='[forminator_quiz id="<?php echo esc_attr( $module['id'] ); ?>"]'><i class="sui-icon-code" aria-hidden="true"></i> <?php esc_html_e( "Copy Shortcode", Forminator::DOMAIN ); ?></button>
									</li>

									<li><a href="<?php echo admin_url( 'admin.php?page=forminator-entries&form_type=forminator_quizzes&form_id=' . $module['id'] ); // WPCS: XSS ok. ?>"><i class="sui-icon-community-people" aria-hidden="true"></i> <?php esc_html_e( 'View Submissions', Forminator::DOMAIN ); ?></a></li>

									<li><form method="post">
										<input type="hidden" name="formninator_action" value="clone">
										<input type="hidden" name="id" value="<?php echo esc_attr( $module['id'] ); ?>"/>
										<?php wp_nonce_field( 'forminatorQuizFormRequest', 'forminatorNonce' ); ?>
										<button type="submit">
											<i class="sui-icon-page-multiple" aria-hidden="true"></i> <?php esc_html_e( 'Duplicate', Forminator::DOMAIN ); ?>
										</button>
									</form></li>

									<?php if ( Forminator::is_import_export_feature_enabled() ) : ?>

										<li><button class="wpmudev-open-modal"
											data-modal="export_quiz"
											data-modal-title=""
											data-form-id="<?php echo esc_attr( $module['id'] ); ?>"
											data-nonce="<?php echo esc_attr( wp_create_nonce( 'forminator_popup_export_quiz' ) ); ?>">
											<i class="sui-icon-cloud-migration" aria-hidden="true"></i> <?php esc_html_e( 'Export', Forminator::DOMAIN ); ?>
										</button></li>

									<?php endif; ?>

									<li><a href="#"
										class="wpmudev-open-modal"
										data-modal="delete-module"
										data-form-id="<?php echo esc_attr( $module['id'] ); ?>"
										data-nonce="<?php echo wp_create_nonce( 'forminatorQuizFormRequest' ); // WPCS: XSS ok. ?>">
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
				data-modal="quizzes">
				<i class="sui-icon-plus" aria-hidden="true"></i> <?php esc_html_e( 'Create', Forminator::DOMAIN ); ?>
			</button>

		</div>

	<?php } ?>

</div>

<?php
/**
 * JS reference : assets/js/admin/layout.js
 */

$path = forminator_plugin_url();

/** @var $this Forminator_CForm_View_Page */
$count             = $this->filtered_total_entries();
$is_filter_enabled = $this->is_filter_box_enabled();

if ( $this->error_message() ) : ?>

	<span class="sui-notice sui-notice-error"><p><?php echo esc_html( $this->error_message() ); ?></p></span>

<?php endif;

if ( $this->total_entries() > 0 ) : ?>

	<form method="GET" class="sui-box forminator-entries-actions">

		<input type="hidden" name="page" value="<?php echo esc_attr( $this->get_admin_page() ); ?>">
		<input type="hidden" name="form_type" value="<?php echo esc_attr( $this->get_form_type() ); ?>">
		<input type="hidden" name="form_id" value="<?php echo esc_attr( $this->get_form_id() ); ?>">

		<fieldset class="forminator-entries-nonce">
			<?php wp_nonce_field( 'forminatorCustomFormEntries', 'forminatorEntryNonce' ); ?>
		</fieldset>

		<div class="sui-box-body">

			<div class="sui-box-search">

				<div class="sui-search-left">

					<?php $this->bulk_actions(); ?>

				</div>

				<div class="sui-search-right">

					<div class="sui-pagination-wrap">

						<span class="sui-pagination-results"><?php if ( 1 === $count ) { printf( esc_html__( '%s result', Forminator::DOMAIN ), $count ); } else { printf( esc_html__( '%s results', Forminator::DOMAIN ), $count ); } // phpcs:ignore ?></span>

						<?php $this->paginate(); ?>

						<button class="sui-button-icon sui-button-outlined forminator-toggle-entries-filter <?php echo( $is_filter_enabled ? 'sui-active' : '' ); ?>">
							<i class="sui-icon-filter" aria-hidden="true"></i>
						</button>

					</div>

				</div>

			</div>

			<?php $this->template( 'custom-form/entries/filter' ); ?>

		</div>

		<table class="sui-table sui-table-flushed sui-accordion">

			<?php $this->entries_header(); ?>

			<tbody>

				<?php
				foreach ( $this->entries_iterator() as $entries ) {

					$entry_id    = $entries['id'];
					$db_entry_id = isset( $entries['entry_id'] ) ? $entries['entry_id'] : '';

					$summary       = $entries['summary'];
					$summary_items = $summary['items'];

					$detail       = $entries['detail'];
					$detail_items = $detail['items'];
					?>

					<tr class="sui-accordion-item" data-entry-id="<?php echo esc_attr( $db_entry_id ); ?>">

						<?php foreach ( $summary_items as $key => $summary_item ) { ?>

							<td colspan="<?php echo esc_attr( $summary_item['colspan'] ); ?>">

								<?php if ( 1 === $summary_item['colspan'] ) : ?>

									<label class="sui-checkbox">
										<input type="checkbox" name="entry[]" id="wpf-cform-module-<?php echo esc_attr( $db_entry_id ); ?>"
											value="<?php echo esc_attr( $db_entry_id ); ?>">
										<span></span>
										<div class="sui-description"><?php echo esc_html( $db_entry_id ); ?></div>
									</label>

								<?php else: ?>
									<?php echo esc_html( $summary_item['value'] ); ?>
								<?php endif; ?>

								<?php if ( ! $summary['num_fields_left'] && ( count( $summary_items ) - 1 ) === $key ) { ?>
									<span class="sui-accordion-open-indicator">
										<i class="sui-icon-chevron-down"></i>
									</span>
								<?php } ?>

							</td>

						<?php } ?>

						<?php if ( $summary['num_fields_left'] ) { ?>

							<td colspan="3">+ <?php echo esc_html( $summary['num_fields_left'] ); ?> other fields
								<span class="sui-accordion-open-indicator">
									<i class="sui-icon-chevron-down"></i>
								</span>
							</td>

						<?php } ?>

					</tr>

					<tr class="sui-accordion-item-content">

						<td colspan="<?php echo esc_attr( $detail['colspan'] ); ?>">

							<div class="sui-box">

								<div class="sui-box-body">

									<h2><?php printf( esc_html__( "Submission #%s", Forminator::DOMAIN ), $db_entry_id ); // WPCS: XSS ok. ?></h2>

									<?php foreach ( $detail_items as $detail_item ) { ?>

										<div class="fui-box-entries-resume">

											<div class="fui-box-entries-field-title"><?php echo esc_html( $detail_item['label'] ); ?></div>

											<div class="fui-box-entries-field-content">

												<?php $sub_entries = $detail_item['sub_entries']; ?>

												<?php
												if ( empty( $sub_entries ) ) {

													echo ( $detail_item['value'] ); // wpcs xss ok. html output intended

												} else {

													foreach ( $sub_entries as $sub_entry ) {
														?>

														<strong><?php echo esc_html( $sub_entry['label'] ); ?></strong>: <?php echo ( $sub_entry['value'] ); // wpcs xss ok. html output intended ?><br />

													<?php
													}

												}
												?>

											</div>

										</div>

									<?php } ?>

								</div>

								<div class="sui-box-footer">

									<button type="button" class="sui-button sui-button-ghost sui-button-red wpmudev-open-modal"
											data-modal="delete-module"
											data-form-id="<?php echo esc_attr( $db_entry_id ); ?>"
											data-nonce="<?php echo wp_create_nonce( 'forminatorCustomFormEntries' ); // WPCS: XSS ok. ?>"><i class="sui-icon-trash" aria-hidden="true"></i> <?php esc_html_e( "Delete", Forminator::DOMAIN ); ?></button>

								</div>

							</div>

						</td>

					</tr>

				<?php } ?>

			</tbody>

		</table>

		<div class="sui-box-body">

			<div class="sui-box-search">

				<div class="sui-search-left">

					<?php $this->bulk_actions( 'bottom' ); ?>

				</div>

				<div class="sui-search-right">

					<div class="sui-pagination-wrap">

						<span class="sui-pagination-results"><?php if ( 1 === $count ) { printf( esc_html__( '%s result', Forminator::DOMAIN ), $count ); } else { printf( esc_html__( '%s results', Forminator::DOMAIN ), $count ); } // phpcs:ignore ?></span>

						<?php $this->paginate(); ?>

<!--						<button class="sui-button-icon sui-button-outlined forminator-toggle-entries-filter --><?php //echo( $is_filter_enabled ? 'sui-active' : '' ); ?><!--">-->
<!--							<i class="sui-icon-filter" aria-hidden="true"></i>-->
<!--						</button>-->

					</div>

				</div>

			</div>

<!--	DISABLED because it will generate override $_GET data		-->
<!--			--><?php //$this->template( 'custom-form/entries/filter' ); ?>

		</div>

	</form>

<?php else : ?>

	<div class="sui-box sui-message">

		<img src="<?php echo $path . 'assets/img/forminator-submissions.png'; // WPCS: XSS ok. ?>"
			srcset="<?php echo $path . 'assets/img/forminator-submissions.png'; // WPCS: XSS ok. ?> 1x, <?php echo $path . 'assets/img/forminator-submissions@2x.png'; // WPCS: XSS ok. ?> 2x" alt="<?php esc_html_e( 'Forminator', Forminator::DOMAIN ); ?>"
			class="sui-image"
			aria-hidden="true" />

		<div class="sui-message-content">

			<h2><?php echo forminator_get_form_name( $this->form_id, 'custom_form' ); // WPCS: XSS ok. ?></h2>

			<p><?php esc_html_e( "You haven’t received any submissions for this form yet. When you do, you’ll be able to view all the data here.", Forminator::DOMAIN ); ?></p>

		</div>

	</div>

<?php endif; ?>

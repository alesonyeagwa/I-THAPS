<?php
$path  = forminator_plugin_url();
$count = Forminator_Form_Entry_Model::count_all_entries();
?>

<?php if ( $count > 0 ) { ?>

	<?php $markup = $this->render_entries(); ?>

	<form method="get"
		name="bulk-action-form"
		class="sui-box">

		<div class="sui-box-search">

			<div class="sui-search-left">

				<input type="hidden" name="page" value="forminator-entries">

				<select name="form_type"
					onchange="submit()"
					class="sui-select-sm sui-select-inline">

					<?php foreach ( $this->get_form_types() as $post_type => $name ) { ?>
						<option value="<?php echo esc_attr( $post_type ); ?>" <?php echo selected( $post_type, $this->get_current_form_type() ); ?>><?php echo esc_html( $name ); ?></option>
					<?php } ?>

				</select>
				
				<?php echo $this->render_form_switcher(); // phpcs:ignore ?>

				<button class="sui-button sui-button-blue" onclick="submit()"><?php esc_html_e( 'Show Submissions', Forminator::DOMAIN ); ?></button>

			</div>

			<?php if ( $markup ) : ?>
				<div class="sui-search-right">
					<a href="/" class="sui-button sui-button-ghost wpmudev-open-modal" data-modal="exports-schedule"><i class="sui-icon-paperclip" aria-hidden="true"></i> <?php esc_html_e( 'Export', Forminator::DOMAIN ); ?></a>
				</div>
			<?php endif; ?>

		</div>

	</form>

	<?php if( $markup ) : ?>

		<?php echo $markup; // phpcs:ignore ?>

	<?php else: ?>

		<div class="sui-box sui-message">

			<img src="<?php echo $path . 'assets/img/forminator-disabled.png'; // WPCS: XSS ok. ?>"
				 srcset="<?php echo $path . 'assets/img/forminator-disabled.png'; // WPCS: XSS ok. ?> 1x, <?php echo $path . 'assets/img/forminator-disabled@2x.png'; // WPCS: XSS ok. ?> 2x"
				 alt="<?php esc_html_e( 'Forminator', Forminator::DOMAIN ); ?>"
				 class="sui-image" />

			<div class="sui-message-content">

				<h2><?php esc_html_e( 'Almost there!', Forminator::DOMAIN ); ?></h2>

				<p><?php esc_html_e( 'Select the form, poll or quiz module to view the corresponding submissions.', Forminator::DOMAIN ); ?></p>

			</div>

		</div>

	<?php endif; ?>
	
<?php } else { ?>
	
	<div class="sui-box sui-message">

		<img src="<?php echo $path . 'assets/img/forminator-submissions.png'; // WPCS: XSS ok. ?>"
			srcset="<?php echo $path . 'assets/img/forminator-submissions.png'; // WPCS: XSS ok. ?> 1x, <?php echo $path . 'assets/img/forminator-submissions@2x.png'; // WPCS: XSS ok. ?> 2x"
			alt="<?php esc_html_e( 'Forminator', Forminator::DOMAIN ); ?>"
			class="sui-image" />

		<div class="sui-message-content">

			<h2><?php esc_html_e( 'Submissions', Forminator::DOMAIN ); ?></h2>

			<p><?php esc_html_e( 'You haven’t received any form, poll or quiz submissions yet. When you do, you’ll be able to view all the data here.', Forminator::DOMAIN ); ?></p>

		</div>

	</div>
	
<?php } ?>

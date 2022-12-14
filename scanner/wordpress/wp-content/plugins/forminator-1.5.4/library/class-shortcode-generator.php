<?php
if ( ! defined( 'ABSPATH' ) ) {
	die();
}

/**
 * Class Forminator_Shortcode_Generator
 */
class Forminator_Shortcode_Generator {

	/**
	 * Forminator_Shortcode_Generator constructor.
	 *
	 * @since 1.0
	 */
	public function __construct() {
		add_filter( 'media_buttons_context', array( $this, 'attach_button' ) );
		add_action( 'admin_footer', array( $this, 'enqueue_js_scripts' ) );
		if ( function_exists( 'hustle_activated' ) ) {
			add_action( 'admin_footer', array( $this, 'enqueue_preview_scripts_for_hustle' ) );
		}
	}

	/**
	 * Check if current page is Hustle wizard page
	 *
	 * @since 1.0.5
	 *
	 * @return bool
	 */
	public function is_hustle_wizard() {
		$screen = get_current_screen();

		// If no screen id, abort
		if( !isset( $screen->id ) ) return false;

		// Hustle wizard pages
		$pages = array(
			'hustle_page_hustle_popup',
			'hustle_page_hustle_slidein',
			'hustle_page_hustle_embedded',
			'hustle_page_hustle_sshare'
		);

		// Check if current page is hustle wizard page
		if( in_array( $screen->id, $pages, true ) ) return true;

		return false;
	}

	/**
	 * Attach button
	 *
	 * @since 1.0
	 * @param $content
	 *
	 * @return string
	 */
	public function attach_button( $content ) {
		global $pagenow;
		$html = '';

		// If page different than Post or Page, abort
		if ( 'post.php' !== $pagenow && 'post-new.php' !== $pagenow && ! $this->is_hustle_wizard() ) {
			return $content;
		}

		// Button markup
		$html .= '<button id="forminator-generate-shortcode" class="button"><span class="dashicons dashicons-welcome-add-page"></span>' . __( 'Add Form', Forminator::DOMAIN ) . '</button>';

		$content .= $html;
		return $content;
	}

	/**
	 * @since 1.0
	 * @param $content
	 *
	 * @return mixed
	 */
	public function enqueue_js_scripts( $content ) {
		global $pagenow;

		// If page different than Post or Page, abort
		if ( 'post.php' !== $pagenow && 'post-new.php' !== $pagenow && ! $this->is_hustle_wizard() ) {
			return $content;
		}

		wp_enqueue_script( 'jquery-ui-core' );
		wp_enqueue_script( 'jquery-ui-widget' );
		wp_enqueue_script( 'jquery-ui-mouse' );
		wp_enqueue_script( 'jquery-ui-tabs' );
		wp_enqueue_script( 'select2-forminator', forminator_plugin_url() . 'assets/js/library/select2.full.min.js', array( 'jquery' ), FORMINATOR_VERSION, false );
		wp_enqueue_style( 'select2-forminator-css', forminator_plugin_url() . 'assets/css/select2.min.css', array(), "4.0.3" ); // Select2
		wp_enqueue_style( 'forminator-shortcode-generator-styles', forminator_plugin_url() . 'assets/css/scgen.min.css', array(), FORMINATOR_VERSION );
		wp_enqueue_script( 'forminator-shortcode-generator', forminator_plugin_url() . 'build/admin/scgen.min.js', array( 'jquery' ), FORMINATOR_VERSION, false );

		$this->print_markup();
		?>
		<script type="text/javascript">
			jQuery(document).ready(function () {
				jQuery("#forminator-generate-shortcode").on( 'click', function(e) {
					e.preventDefault();
				});
			});
		</script>
		<?php
	}

	/**
	 * @since 1.0
	 * @param $content
	 *
	 * @return mixed
	 */
	public function enqueue_preview_scripts_for_hustle( $content ) {
		// If page is not Hustle module settings page, abort
		if ( ! $this->is_hustle_wizard() ) {
			return $content;
		}

		wp_enqueue_style( 'forminator-shortcode-generator-front-styles', forminator_plugin_url() . 'assets/css/front.min.css', array(), FORMINATOR_VERSION );
	}

	/**
	 * Print modal markup
	 *
	 * @since 1.0
	 */
	public function print_markup() {
		?>
		<div id="forminator-popup" class="wpmudev-modal">

			<div class="wpmudev-modal-mask" aria-hidden="true"></div>

			<div class="wpmudev-box wpmudev-show">

				<div class="wpmudev-box-header">

					<div class="wpmudev-header--text">
						<h2 class="wpmudev-subtitle"><?php echo esc_html__( "Generate form shortcode", Forminator::DOMAIN ); ?></h2>
					</div>

					<div class="wpmudev-header--action">
						<button id="forminator-popup-close" class="wpmudev-box--action">
							<span class="wpmudev-icon--close"></span>
						</button>

					</div>

				</div>

				<div class="wpmudev-box-section">

					<div class="wpmudev-section--text">

						<div class="wpmudev-tabs">

							<ul class="wpmudev-tabs--nav">

								<li class="wpmudev-tab"><a class="wpmudev-tab--link" href="#forminator-custom-forms"><?php esc_html_e( 'Custom Forms', Forminator::DOMAIN ); ?></a></li>

								<li class="wpmudev-tab"><a class="wpmudev-tab--link" href="#forminator-polls"><?php esc_html_e( 'Polls', Forminator::DOMAIN ); ?></a></li>

								<li class="wpmudev-tab"><a class="wpmudev-tab--link" href="#forminator-quizzes"><?php esc_html_e( 'Quizzes', Forminator::DOMAIN ); ?></a></li>

							</ul>

							<div id="forminator-custom-forms" class="wpmudev-tabs--content">

								<?php echo $this->get_forms(); // WPCS: XSS ok. ?>

								<div class="wpmudev-tabs--action">

									<button class="wpmudev-button wpmudev-button-blue wpmudev-insert-cform"><?php esc_html_e( 'Insert Form', Forminator::DOMAIN ); ?></button>

								</div>

							</div>

							<div id="forminator-polls" class="wpmudev-tabs--content">

								<?php echo $this->get_polls(); // WPCS: XSS ok. ?>

								<div class="wpmudev-tabs--action">

									<button class="wpmudev-button wpmudev-button-blue wpmudev-insert-poll"><?php esc_html_e( 'Insert Form', Forminator::DOMAIN ); ?></button>

								</div>

							</div>

							<div id="forminator-quizzes" class="wpmudev-tabs--content">

								<?php echo $this->get_quizzes(); // WPCS: XSS ok. ?>

								<div class="wpmudev-tabs--action">

									<button class="wpmudev-button wpmudev-button-blue wpmudev-insert-quiz"><?php esc_html_e( 'Insert Form', Forminator::DOMAIN ); ?></button>

								</div>

							</div>

						</div>

					</div>

				</div>

			</div>

		</div>
		<?php
	}

	/**
	 * Print forms select
	 *
	 * @since 1.0
	 * @return string
	 */
	public function get_forms() {
		$html = '<select name="forms" class="wpmudev-select forminator-custom-form-list">';
		$html .= '<option value="">' . __( "Select a form", Forminator::DOMAIN ) . '</option>';
		$modules = forminator_cform_modules( 999 );
		foreach( $modules as $module ) {
			$title = forminator_get_form_name( $module['id'], 'custom_form');
			if ( strlen( $title ) > 25 ) {
				$title = substr( $title, 0, 25 ) . '...';
			}
			$html .= '<option value="' . $module['id'] . '">' . $title . ' - ID: ' . $module['id'] . '</option>';
		}
		$html .= '</select>';

		return $html;
	}

	/**
	 * Print polls select
	 *
	 * @since 1.0
	 * @return string
	 */
	public function get_polls() {
		$html = '<select name="forms" class="wpmudev-select forminator-insert-poll">';
		$html .= '<option value="">' . __( "Select Poll", Forminator::DOMAIN ) . '</option>';
		$modules = forminator_polls_modules( 999 );
		foreach( $modules as $module ) {
			$title = forminator_get_form_name( $module['id'], 'poll');
			if ( strlen( $title ) > 25 ) {
				$title = substr( $title, 0, 25 ) . '...';
			}
			$html .= '<option value="' . $module['id'] . '">' . $title . ' - ID: ' . $module['id'] . '</option>';
		}
		$html .= '</select>';

		return $html;
	}

	/**
	 * Print quizzes select
	 *
	 * @since 1.0
	 * @return string
	 */
	public function get_quizzes() {
		$html = '<select name="forms" class="wpmudev-select forminator-quiz-list">';
		$html .= '<option value="">' . __( "Select Quiz", Forminator::DOMAIN ) . '</option>';
		$modules = forminator_quizzes_modules( 999 );
		foreach( $modules as $module ) {
			$title = forminator_get_form_name( $module['id'], 'quiz');
			if ( strlen( $title ) > 25 ) {
				$title = substr( $title, 0, 25 ) . '...';
			}
			$html .= '<option value="' . $module['id'] . '">' . $title . ' - ID: ' . $module['id'] . '</option>';
		}
		$html .= '</select>';

		return $html;
	}
}

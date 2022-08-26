<?php
/**
 * Return needed cap for admin pages
 *
 * @since 1.0
 * @return string
 */
function forminator_get_admin_cap() {
	$cap = 'manage_options';

	if ( is_multisite() && is_network_admin() ) {
		$cap = 'manage_network';
	}

	return apply_filters( 'forminator_admin_cap', $cap );
}

/**
 * Checks if user is allowed to perform the ajax actions
 *
 * @since 1.0
 * @return bool
 */
function forminator_is_user_allowed() {
	return current_user_can( 'manage_options' );
}

/**
 * Check if array value exists
 *
 * @since 1.0
 *
 * @param array  $array
 * @param string $key - the string key
 *
 * @return bool
 */
function forminator_array_value_exists( $array, $key ) {
	return ( isset( $array[ $key ] ) && ! empty( $array[ $key ] ) );
}

/**
 * Convert object to array
 *
 * @since 1.0
 *
 * @param $object
 *
 * @return array
 */
function forminator_object_to_array( $object ) {
	$array = array();

	if ( empty( $object ) ) {
		return $array;
	}

	foreach ( $object as $key => $value ) {
		$array[ $key ] = $value;
	}

	return $array;
}

/**
 * Return AJAX url
 *
 * @since 1.0
 * @return mixed
 */
function forminator_ajax_url() {
	return admin_url( "admin-ajax.php", is_ssl() ? 'https' : 'http' );
}

/**
 * Checks if the AJAX call is valid
 *
 * @since 1.0
 *
 * @param $action
 */
function forminator_validate_ajax( $action ) {
	if ( ! forminator_is_user_allowed() || ! check_ajax_referer( $action ) ) {
		wp_send_json_error( __( "Invalid request, you are not allowed to do that action.", Forminator::DOMAIN ) );
	}
}

/**
 * Enqueue admin fonts
 *
 * @since 1.0
 * @since 1.5.1 implement $version
 *
 * @param $version
 */
function forminator_admin_enqueue_fonts( $version ) {
	wp_enqueue_style( 'forminator-roboto',
	                  'https://fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,700,700i|Roboto:300,300i,400,400i,500,500i,700,700i',
	                  array(),
	                  '1.0' ); // cache as long as you can
	wp_enqueue_style( 'forminator-opensans',
	                  'https://fonts.googleapis.com/css?family=Open+Sans:400,400i,700,700i',
	                  array(),
	                  '1.0' ); // cache as long as you can
	wp_enqueue_style( 'forminator-source',
	                  'https://fonts.googleapis.com/css?family=Source+Code+Pro',
	                  array(),
	                  '1.0' ); // cache as long as you can

	// if plugin internal font need to enqueued, please use $version as its subject to cache
}

/**
 * Enqueue admin styles
 *
 * @since 1.0
 * @since 1.1 Remove forminator-admin css after migrate to shared-ui
 *
 * @param $version
 */
function forminator_admin_enqueue_styles( $version ) {
	wp_enqueue_style( 'select2-forminator-css', forminator_plugin_url() . 'assets/css/select2.min.css', array(), "4.0.3", false ); // Select2
	wp_enqueue_style( 'shared-ui', forminator_plugin_url() . 'assets/css/shared-ui.min.css', array(), $version, false );
	wp_enqueue_style( 'forminator-form-styles', forminator_plugin_url() . 'assets/css/front.min.css', array(), $version, false );
}

/**
 * Enqueue jQuery UI scripts on admin
 *
 * @since 1.0
 */
function forminator_admin_jquery_ui() {
	wp_enqueue_script( 'jquery-ui', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js', array(), '1.12.1', false );
}

/**
 * Load admin scripts
 *
 * @since 1.0
 */
function forminator_admin_jquery_ui_init() {
	wp_enqueue_script( 'jquery-ui-core' );
	wp_enqueue_script( 'jquery-ui-widget' );
	wp_enqueue_script( 'jquery-ui-mouse' );
	wp_enqueue_script( 'jquery-ui-tabs' );
	wp_enqueue_script( 'jquery-ui-sortable' );
	wp_enqueue_script( 'jquery-ui-draggable' );
	wp_enqueue_script( 'jquery-ui-droppable' );
	wp_enqueue_script( 'jquery-ui-datepicker' );
	wp_enqueue_script( 'jquery-ui-resize' );
	wp_enqueue_style( 'wp-color-picker' );
}

/**
 * Enqueue SUI scripts on admin
 *
 * @since 1.1
 */
function forminator_sui_scripts() {

	$chartjs_version = "2.7.2";

	$sanitize_version = str_replace( '.', '-', FORMINATOR_SUI_VERSION );
	$sui_body_class   = "sui-$sanitize_version";

	wp_enqueue_script( 'shared-ui', forminator_plugin_url() . 'assets/js/shared-ui.min.js', array( 'jquery' ), $sui_body_class, true );

}

/**
 * Enqueue admin scripts
 *
 * @since 1.0
 *
 * @param       $version
 * @param array $data
 * @param array $l10n
 */
function forminator_admin_enqueue_scripts( $version, $data = array(), $l10n = array() ) {
	$language = get_option( "forminator_captcha_language", "en" );

	wp_enqueue_script( 'select2-forminator', forminator_plugin_url() . 'assets/js/library/select2.full.min.js', array( 'jquery' ), $version, false );
	wp_enqueue_script( 'ace-editor', forminator_plugin_url() . 'assets/js/library/ace/ace.js', array( 'jquery' ), $version, false );
	wp_enqueue_script( 'google-charts', 'https://www.gstatic.com/charts/loader.js', array( 'jquery' ), $version, false );

	if ( function_exists( 'wp_enqueue_editor' ) ) {
		wp_enqueue_editor();
	}
	if ( function_exists( 'wp_enqueue_media' ) ) {
		wp_enqueue_media();
	}

	wp_enqueue_script( 'forminator-admin-layout', forminator_plugin_url() . 'build/admin/layout.js', array( 'jquery' ), $version, false );
	wp_register_script(
		'forminator-admin',
		forminator_plugin_url() . 'build/main.js',
		array(
			'backbone',
			'underscore',
			'jquery',
			'wp-color-picker',
		),
		$version,
		false
	);
	wp_localize_script( 'forminator-admin', 'forminator_data', $data );
	wp_localize_script( 'forminator-admin', 'forminator_l10n', $l10n );
	wp_enqueue_script( 'forminator-admin' );
}

/**
 * Enqueue front-end styles
 *
 * @since 1.0
 *
 * @param $version
 */
function forminator_print_front_styles( $version = '1.0' ) {
	wp_enqueue_style( 'forminator-form-styles', forminator_plugin_url() . 'assets/css/front.min.css', array(), $version );
	wp_enqueue_style( 'select2-forminator-css', forminator_plugin_url() . 'assets/css/select2.min.css', array(), "4.0.3" ); // Select2
	wp_enqueue_style( 'intlTelInput-forminator-css', forminator_plugin_url() . 'assets/css/intlTelInput.min.css', array(), "4.0.3" ); // intlTelInput
}

/**
 * Enqueue front-end styles
 *
 * @since 1.0
 *
 * @param $version
 */
function forminator_print_front_scripts( $version = '1.0' ) {
	if ( ! is_admin() ) {
		wp_enqueue_script( 'select2-forminator', forminator_plugin_url() . 'assets/js/library/select2.full.min.js', array( 'jquery' ), $version, false );
		wp_enqueue_script( 'google-charts', 'https://www.gstatic.com/charts/loader.js', array( 'jquery' ), $version, false );
		wp_enqueue_script( 'forminator-jquery-validate', forminator_plugin_url() . 'assets/js/library/jquery.validate.min.js', array( 'jquery' ), FORMINATOR_VERSION, false );
		wp_enqueue_script( 'forminator-intlTelInput', forminator_plugin_url() . 'assets/js/library/intlTelInput.min.js', array( 'jquery' ), FORMINATOR_VERSION, false ); // intlTelInput
		wp_enqueue_script( 'forminator-front-scripts',
		                   forminator_plugin_url() . 'build/front/front.multi.min.js',
		                   array( 'jquery', 'select2-forminator', 'forminator-jquery-validate', 'forminator-intlTelInput' ),
		                   $version,
		                   false );

		wp_localize_script( 'forminator-front-scripts', 'ForminatorFront', forminator_localize_data() );
	}

	if ( is_admin() ) {
		wp_localize_script( 'forminator-front-scripts', 'ForminatorConditions', array() );
	}
}

/**
 * Return front-end localization data
 *
 * @since 1.0
 */
function forminator_localize_data() {
	return array(
		'ajaxUrl' => forminator_ajax_url(),
		'cform'   => array(
			'processing'                => __( 'Submitting form, please wait', Forminator::DOMAIN ),
			'error'                     => __( 'An error occurred processing the form. Please try again', Forminator::DOMAIN ),
			'upload_error'              => __( 'An upload error occured processing the form. Please try again', Forminator::DOMAIN ),
			'pagination_prev'           => __( 'Back', Forminator::DOMAIN ),
			'pagination_next'           => __( 'Next', Forminator::DOMAIN ),
			'pagination_go'             => __( 'Submit', Forminator::DOMAIN ),
			'gateway'                   => array(
				'processing' => __( 'Processing payment, please wait', Forminator::DOMAIN ),
				'paid'       => __( 'Success! Payment confirmed. Submitting form, please wait', Forminator::DOMAIN ),
				'error'      => __( 'Error! Something went wrong when verifying the payment', Forminator::DOMAIN ),
			),
			'captcha_error'             => __( 'Invalid CAPTCHA', Forminator::DOMAIN ),
			'no_file_chosen'            => __( 'No file chosen', Forminator::DOMAIN ),
			// This is the file "/build/js/utils.js" found into intlTelInput plugin. Renamed so it makes sense within the "js/library" directory context.
			'intlTelInput_utils_script' => forminator_plugin_url() . 'assets/js/library/intlTelInputUtils.js',
		),
		'poll'    => array(
			'processing' => __( 'Submitting vote, please wait', Forminator::DOMAIN ),
			'error'      => __( 'An error occurred saving the vote. Please try again', Forminator::DOMAIN ),
		),
	);
}

/**
 * Return existing templates
 *
 * @since 1.0
 *
 * @param $path
 * @param $args
 *
 * @return mixed
 */

function forminator_template( $path, $args = array() ) {
	$file    = forminator_plugin_dir() . "admin/views/$path.php";
	$content = '';

	if ( is_file( $file ) ) {
		ob_start();

		if ( isset( $args['id'] ) ) {
			$args['template_class'] = $args['class'];
			$args['template_id']    = $args['id'];
			$title                  = $args['title'];
			$header_callback        = $args['header_callback'];
			$main_callback          = $args['main_callback'];
			$footer_callback        = $args['footer_callback'];
		}

		include $file;

		$content = ob_get_clean();
	}

	return $content;
}

/**
 * Return if template exist
 *
 * @since 1.0
 *
 * @param $path
 *
 * @return bool
 */
function forminator_template_exist( $path ) {
	$file = forminator_plugin_dir() . "admin/views/$path.php";

	return is_file( $file );
}

/**
 * Return if paypal settings are filled
 *
 * @since 1.0
 * @return bool
 */
function forminator_has_paypal_settings() {
	$client_id = get_option( "forminator_paypal_client_id", false );
	$secret    = get_option( "forminator_paypal_secret", false );

	if ( empty( $client_id ) || empty( $secret ) ) {
		return false;
	}

	return true;
}

/**
 * Return if captcha settings are filled
 *
 * @since 1.0
 * @return bool
 */
function forminator_has_captcha_settings() {
	$key    = get_option( "forminator_captcha_key", false );
	$secret = get_option( "forminator_captcha_secret", false );

	if ( empty( $key ) || empty( $secret ) ) {
		return false;
	}

	return true;
}

/**
 * Return form ID
 *
 * @since 1.0
 * @return int
 */
function forminator_get_form_id_helper() {
	$screen = get_current_screen();
	$ids    = forminator_get_page_ids_helper();

	if ( ! in_array( $screen->id, $ids, true ) ) {
		return 0;
	}

	return isset( $_GET['form_id'] ) ? intval( $_GET['form_id'] ) : 0; // WPCS: CSRF OK
}

/**
 * Get Page IDs
 *
 * @since 1.2
 * @return array
 */
function forminator_get_page_ids_helper() {
	if( FORMINATOR_PRO ) {
		return array(
			'forminator-pro_page_forminator-quiz-view',
			'forminator-pro_page_forminator-cform-view',
			'forminator-pro_page_forminator-poll-view',
			'forminator-pro_page_forminator-entries'
		);
	} else {
		// Free version
		return array(
			'forminator_page_forminator-quiz-view',
			'forminator_page_forminator-cform-view',
			'forminator_page_forminator-poll-view',
			'forminator_page_forminator-entries'
		);
	}
}

/**
 * Return form type
 *
 * @since 1.0
 * @return int|null|string
 */
function forminator_get_form_type_helper() {
	$screen = get_current_screen();
	$ids    = forminator_get_page_ids_helper();
	if ( ! in_array( $screen->id, $ids, true ) ) {
		return 0;
	}

	$form_type = "";
	$page      = isset( $_GET['page'] ) ? $_GET['page'] : null; // WPCS: CSRF OK
	if ( is_null( $page ) ) {
		return null;
	}

	switch ( $page ) {
		case 'forminator-quiz-view':
			$form_type = "quiz";
			break;
		case 'forminator-poll-view':
			$form_type = "poll";
			break;
		case 'forminator-cform-view':
			$form_type = "cform";
			break;
		case 'forminator-entries':
			if ( isset( $_GET['form_type'] ) && $_GET['form_type'] ) { // WPCS: CSRF OK
				switch ( $_GET['form_type'] ) { // WPCS: CSRF OK
					case 'forminator_forms':
						$form_type = "cform";
						break;
					case 'forminator_polls':
						$form_type = "poll";
						break;
					case 'forminator_quizzes':
						$form_type = "quiz";
						break;
				}
			}
			break;
	}

	return $form_type;
}

/**
 * @since 1.0
 *
 * @param $info
 * @param $key
 *
 * @return mixed
 */
function forminator_get_exporter_info( $info, $key ) {
	$data = get_option( 'forminator_entries_export_schedule', array() );

	return isset( $data[ $key ][ $info ] ) ? $data[ $key ][ $info ] : null;
}

/**
 * Return current logged in username
 *
 * @since 1.0
 * @return string
 */
function forminator_get_current_username() {
	$current_user = wp_get_current_user();
	if ( ! ( $current_user instanceof WP_User ) || empty( $current_user->user_login ) ) {
		return '';
	}

	return $current_user->user_login;
}

/**
 * @since 1.0
 *
 * @param $form_id
 *
 * @return bool
 */
function delete_export_logs( $form_id ) {
	if ( ! $form_id ) {
		return false;
	}

	$data   = get_option( 'forminator_exporter_log', array() );
	$delete = false;

	if ( isset( $data[ $form_id ] ) ) {
		unset( $data[ $form_id ] );
		$delete = update_option( 'forminator_exporter_log', $data );
	}

	return $delete;
}

/**
 * @since 1.0
 *
 * @param $form_id
 *
 * @return array
 */
function forminator_get_export_logs( $form_id ) {
	if ( ! $form_id ) {
		return array();
	}

	$data = get_option( 'forminator_exporter_log', array() );
	$row  = isset( $data[ $form_id ] ) ? $data[ $form_id ] : array();

	foreach ( $row as &$item ) {
		$item['time'] = date( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $item['time'] );
	}

	return $row;
}

/**
 * Return current page url
 *
 * @since 1.0.3
 *
 * @return mixed
 */
function forminator_get_current_url() {
	global $wp;

	return add_query_arg( $_SERVER['QUERY_STRING'], '', trailingslashit( home_url( $wp->request ) ) );
}

/**
 * Return week day from number
 *
 * @since 1.0
 *
 * @param $day
 *
 * @return string
 */
function forminator_get_day_translated( $day ) {
	$days = array(
		"mon" => __( "Monday", Forminator::DOMAIN ),
		"tue" => __( "Tuesday", Forminator::DOMAIN ),
		"wed" => __( "Wednesday", Forminator::DOMAIN ),
		"thu" => __( "Thursday", Forminator::DOMAIN ),
		"fri" => __( "Friday", Forminator::DOMAIN ),
		"sat" => __( "Saturday", Forminator::DOMAIN ),
		"sun" => __( "Sunday", Forminator::DOMAIN ),
	);

	return isset( $days[ $day ] ) ? $days[ $day ] : $day;
}

/**
 * Add log of forminator
 *
 * By default it will check `WP_DEBUG` and `FORMINATOR_DEBUG`
 * then will check `filters`
 *
 * @since 1.1
 * @since 1.3 add FORMINATOR_DEBUG as enabled flag
 */
function forminator_maybe_log() {
	$wp_debug_enabled = ( defined( 'WP_DEBUG' ) && WP_DEBUG );

	$enabled = ( defined( 'FORMINATOR_DEBUG' ) && FORMINATOR_DEBUG );

	$enabled = ( $wp_debug_enabled && $enabled );

	/**
	 * Filter log enable for forminator
	 *
	 * y default it will check `WP_DEBUG`, `FORMINATOR_DEBUG` must be true
	 *
	 * @since 1.1
	 *
	 * @param bool $enabled current enable status
	 */
	$enabled = apply_filters( 'forminator_enable_log', $enabled );

	if ( $enabled ) {
		$args    = func_get_args();
		$message = wp_json_encode( $args );
		if ( false !== $message ) {
			error_log( '[Forminator] ' . $message );// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
		}

	}
}

/**
 * Get chart colors combination for Polls
 *
 * @since 1.5.3
 *
 * @param int $poll_id
 *
 * @return array
 */
function forminator_get_poll_chart_colors( $poll_id = null ) {

	$chart_colors = array(
		'#17A8E3', // Blue
		'#FF6D6D', // Red
		'#FECF2F', // Yellow
		'#1ABC9C', // Green
		'#FF7E41', // Orange
		'#8D00B1', // Purple
		'#6689A1', // Blue Alt
		'#EA5676', // Red Alt
		'#D8DC6A', // Yellow Alt
		'#6BC192', // Green Alt
		'#EB8258', // Orange Alt
		'#995D81', // Purple Alt
		'#000000', // Black
		'#888888', // Black Alt
	);

	$chart_colors = apply_filters_deprecated( 'forminator_poll_chart_color', array( $chart_colors ), '1.5.3', 'forminator_poll_chart_colors' );

	/**
	 * Filter chart colors to be used for polls
	 *
	 * @since 1.5.3
	 *
	 * @param array $chart_colors
	 * @param int   $poll_id
	 */
	$chart_colors = apply_filters( 'forminator_poll_chart_colors', $chart_colors, $poll_id );

	return $chart_colors;
}

/**
 * Return reCAPTCHA languages
 *
 * @since 1.5.4
 * @return array
 */
function forminator_get_captcha_languages() {
	return apply_filters(
		'forminator_captcha_languages',
		array(
			'ar' => esc_html__( 'Arabic', Forminator::DOMAIN ),
			'af' => esc_html__( 'Afrikaans', Forminator::DOMAIN ),
			'am' => esc_html__( 'Amharic', Forminator::DOMAIN ),
			'hy' => esc_html__( 'Armenian', Forminator::DOMAIN ),
			'az' => esc_html__( 'Azerbaijani', Forminator::DOMAIN ),
			'eu' => esc_html__( 'Basque', Forminator::DOMAIN ),
			'bn' => esc_html__( 'Bengali', Forminator::DOMAIN ),
			'bg' => esc_html__( 'Bulgarian', Forminator::DOMAIN ),
			'ca' => esc_html__( 'Catalan', Forminator::DOMAIN ),
			'zh-HK' => esc_html__( 'Chinese (Hong Kong)', Forminator::DOMAIN ),
			'zh-CN' => esc_html__( 'Chinese (Simplified)', Forminator::DOMAIN ),
			'zh-TW' => esc_html__( 'Chinese (Traditional)', Forminator::DOMAIN ),
			'hr' => esc_html__( 'Croatian', Forminator::DOMAIN ),
			'cs' => esc_html__( 'Czech', Forminator::DOMAIN ),
			'da' => esc_html__( 'Danish', Forminator::DOMAIN ),
			'nl' => esc_html__( 'Dutch', Forminator::DOMAIN ),
			'en-GB' => esc_html__( 'English (UK)', Forminator::DOMAIN ),
			'en' => esc_html__( 'English (US)', Forminator::DOMAIN ),
			'et' => esc_html__( 'Estonian', Forminator::DOMAIN ),
			'fil' => esc_html__( 'Filipino', Forminator::DOMAIN ),
			'fi' => esc_html__( 'Finnish', Forminator::DOMAIN ),
			'fr' => esc_html__( 'French', Forminator::DOMAIN ),
			'fr-CA' => esc_html__( 'French (Canadian)', Forminator::DOMAIN ),
			'gl' => esc_html__( 'Galician', Forminator::DOMAIN ),
			'ka' => esc_html__( 'Georgian', Forminator::DOMAIN ),
			'de' => esc_html__( 'German', Forminator::DOMAIN ),
			'de-AT' => esc_html__( 'German (Austria)', Forminator::DOMAIN ),
			'de-CH' => esc_html__( 'German (Switzerland)', Forminator::DOMAIN ),
			'el' => esc_html__( 'Greek', Forminator::DOMAIN ),
			'gu' => esc_html__( 'Gujarati', Forminator::DOMAIN ),
			'iw' => esc_html__( 'Hebrew', Forminator::DOMAIN ),
			'hi' => esc_html__( 'Hindi', Forminator::DOMAIN ),
			'hu' => esc_html__( 'Hungarain', Forminator::DOMAIN ),
			'is' => esc_html__( 'Icelandic', Forminator::DOMAIN ),
			'id' => esc_html__( 'Indonesian', Forminator::DOMAIN ),
			'it' => esc_html__( 'Italian', Forminator::DOMAIN ),
			'ja' => esc_html__( 'Japanese', Forminator::DOMAIN ),
			'kn' => esc_html__( 'Kannada', Forminator::DOMAIN ),
			'ko' => esc_html__( 'Korean', Forminator::DOMAIN ),
			'lo' => esc_html__( 'Laothian', Forminator::DOMAIN ),
			'lv' => esc_html__( 'Latvian', Forminator::DOMAIN ),
			'lt' => esc_html__( 'Lithuanian', Forminator::DOMAIN ),
			'ms' => esc_html__( 'Malay', Forminator::DOMAIN ),
			'ml' => esc_html__( 'Malayalam', Forminator::DOMAIN ),
			'mr' => esc_html__( 'Marathi', Forminator::DOMAIN ),
			'mn' => esc_html__( 'Mongolian', Forminator::DOMAIN ),
			'no' => esc_html__( 'Norwegian', Forminator::DOMAIN ),
			'fa' => esc_html__( 'Persian', Forminator::DOMAIN ),
			'pl' => esc_html__( 'Polish', Forminator::DOMAIN ),
			'pt' => esc_html__( 'Portuguese', Forminator::DOMAIN ),
			'pt-BR' => esc_html__( 'Portuguese (Brazil)', Forminator::DOMAIN ),
			'pt-PT' => esc_html__( 'Portuguese (Portugal)', Forminator::DOMAIN ),
			'ro' => esc_html__( 'Romanian', Forminator::DOMAIN ),
			'ru' => esc_html__( 'Russian', Forminator::DOMAIN ),
			'sr' => esc_html__( 'Serbian', Forminator::DOMAIN ),
			'si' => esc_html__( 'Sinhalese', Forminator::DOMAIN ),
			'sk' => esc_html__( 'Slovak', Forminator::DOMAIN ),
			'sl' => esc_html__( 'Slovenian', Forminator::DOMAIN ),
			'es' => esc_html__( 'Spanish', Forminator::DOMAIN ),
			'es-419' => esc_html__( 'Spanish (Latin America)', Forminator::DOMAIN ),
			'sw' => esc_html__( 'Swahili', Forminator::DOMAIN ),
			'sv' => esc_html__( 'Swedish', Forminator::DOMAIN ),
			'ta' => esc_html__( 'Tamil', Forminator::DOMAIN ),
			'te' => esc_html__( 'Telugu', Forminator::DOMAIN ),
			'th' => esc_html__( 'Thai', Forminator::DOMAIN ),
			'tr' => esc_html__( 'Turkish', Forminator::DOMAIN ),
			'uk' => esc_html__( 'Ukrainian', Forminator::DOMAIN ),
			'ur' => esc_html__( 'Urdu', Forminator::DOMAIN ),
			'vi' => esc_html__( 'Vietnamese', Forminator::DOMAIN ),
			'zu' => esc_html__( 'Zulu', Forminator::DOMAIN ),
		)
	);
}

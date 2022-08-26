<?php

/**
 * Class Forminator_Admin_Data
 *
 * @since 1.0
 */
class Forminator_Admin_Data {

	public $core = null;

	/**
	 * Current Nonce
	 *
	 * @since 1.2
	 * @var string
	 */
	private $_nonce = '';

	/**
	 * Forminator_Admin_Data constructor.
	 *
	 * @since 1.0
	 */
	public function __construct() {
		$this->core = Forminator::get_instance();
	}

	/**
	 * Combine Data and pass to JS
	 *
	 * @since 1.0
	 * @return array
	 */
	public function get_options_data() {
		$data = $this->admin_js_defaults();
		$data = apply_filters( "forminator_data", $data );
		$data[ 'fields' ] = forminator_get_fields();
		return $data;
	}

	/**
	 * Generate nonce
	 *
	 * @since 1.2
	 */
	public function generate_nonce() {
		$this->_nonce = wp_create_nonce( 'forminator_load_google_fonts' );
	}

	/**
	 * Get current generated nonce
	 *
	 * @since 1.2
	 * @return string
	 */
	public function get_nonce() {
		return $this->_nonce;
	}

	/**
	 * Default Admin properties
	 *
	 * @since 1.0
	 * @return array
	 */
	public function admin_js_defaults() {
		// Generate addon nonce
		Forminator_Addon_Admin_Ajax::get_instance()->generate_nonce();

		return array(
			'ajaxUrl'        => forminator_ajax_url(),
			'application'    => '',
			'is_touch'       => wp_is_mobile(),
			'dashboardUrl'   => menu_page_url( 'forminator', false ),
			'defaultTabs'    => array(
				'standard',
				//'pricing',
				'posts',
				//'advanced'
			),
			'hasCaptcha'     => forminator_has_captcha_settings(),
			'formNonce'      => $this->get_nonce(),
			'searchNonce'    => wp_create_nonce( 'forminator_search_emails' ),
			'gFontNonce'     => wp_create_nonce( 'forminator_load_google_fonts' ),
			'addons_enabled' => Forminator::is_addons_feature_enabled(),
			'pluginUrl'      => forminator_plugin_url(),
			'addonNonce'     => Forminator_Addon_Admin_Ajax::get_instance()->get_nonce()
		);
	}
}

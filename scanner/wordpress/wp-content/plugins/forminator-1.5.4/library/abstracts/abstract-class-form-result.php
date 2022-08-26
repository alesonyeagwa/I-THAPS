<?php
if ( ! defined( 'ABSPATH' ) ) {
	die();
}

/**
 * Class Forminator_Result
 *
 * Abstract class for results
 *
 * @since 1.1
 */
abstract class Forminator_Result {

	/*
	 * Entry id
	 *
	 * @var integer
	 */
	protected $entry_id  = 0;
	protected $post_data = false;
	protected $post_type = '';

	public function __construct() {
		add_action( 'init', array( $this, 'add_rewrite_rules' ) );
		add_action( 'wp_head', array( $this, 'load_results_page' ), 99 );
	}

	public function set_entry( $id = false ) {
		if ( false === $id ) {
			$this->entry_id = get_query_var( 'entries', 0 );
		} else {
			$this->entry_id = intval( $id );
		}
	}

	public function set_postdata( $data = false ) {
		if ( false !== $data ) {
			$this->post_data = $data;
		}
	}

	public function load_results_page() {
		$this->set_entry();

		if ( empty( $this->entry_id ) ) {
			echo '';
		} else {
			$this->print_result_header();
		}
	}

	public function print_result_header() {
		echo '';
	}

	public function build_permalink( $id = false ) {
		if ( empty( $this->entry_id ) ) {
			return $this->post_data['_wp_http_referer'];
		}

		$permalink = get_option( 'permalink_structure' );

		if ( empty( $permalink ) ) {
			$query        = wp_parse_url( $this->post_data['_wp_http_referer'] );
			$query_params = array();

			if ( isset( $query['query'] ) ) {
				parse_str( $query['query'], $query_params );
			}

			$query_params['entries'] = $this->entry_id;

			return home_url( '?' . http_build_query( $query_params, '', '&' ) );
		}

		$http_referer = $this->post_data['_wp_http_referer'];
		$http_referer = preg_replace( '/entries((.*))?/', '', $http_referer );

		return $http_referer . "entries/" . $this->entry_id . "/";
	}

	public function add_rewrite_rules() {
		add_rewrite_tag( '%entries%', '([^&]+)' );
		add_rewrite_rule( '^entries/([^/]+)/?', 'index.php?entries=$matches[1]', 'top' );
		add_rewrite_rule( '(.?.+?)/entries(/(.*))?/?$', 'index.php?pagename=$matches[1]&entries=$matches[3]', 'top' );
	}
}

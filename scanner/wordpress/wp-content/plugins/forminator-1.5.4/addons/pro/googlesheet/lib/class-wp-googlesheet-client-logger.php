<?php

if ( ! class_exists( 'Google_Logger_Abstract' ) ) {
	require_once dirname( __FILE__ ) . '/external/Google/Logger/Abstract.php';
}


/**
 * Class Forminator_Addon_Wp_Googlesheet_Client_Logger
 */
class Forminator_Addon_Wp_Googlesheet_Client_Logger extends Google_Logger_Abstract {

	/**
	 * Writes a message to the current log implementation.
	 *
	 * @param string $message The message
	 */
	protected function write( $message ) {
		forminator_addon_maybe_log( $message );
	}
}

<?php

/**
 * Addon Name: Slack
 * Version: 1.0
 * Plugin URI:  https://premium.wpmudev.org/
 * Description: Integrate Forminator Custom Forms with Slack to get notified in real time.
 * Author: WPMU DEV
 * Author URI: http://premium.wpmudev.org
 */

define( 'FORMINATOR_ADDON_SLACK_VERSION', '1.0' );

function forminator_addon_slack_url() {
	return trailingslashit( forminator_plugin_url() . 'addons/pro/slack' );
}

function forminator_addon_slack_dir() {
	return trailingslashit( dirname( __FILE__ ) );
}

function forminator_addon_slack_assets_url() {
	return trailingslashit( forminator_addon_slack_url() . 'assets' );
}

require_once dirname( __FILE__ ) . '/forminator-addon-slack.php';
require_once dirname( __FILE__ ) . '/forminator-addon-slack-form-settings.php';
require_once dirname( __FILE__ ) . '/forminator-addon-slack-form-hooks.php';
//Direct Load
Forminator_Addon_Loader::get_instance()->register( 'Forminator_Addon_Slack' );

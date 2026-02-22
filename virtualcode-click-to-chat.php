<?php
/**
 * Plugin Name: Virtualcode Click to Chat
 * Plugin URI: https://github.com/webbyvivek/chatlink
 * Description: Add a floating WhatsApp chat widget with customizable button, business hours, page targeting, and expandable chat box.
 * Version: 1.1.0
 * Author: Virtualcode
 * Author URI: https://virtualcode.co
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: virtualcode-click-to-chat
 *
 * @package Virtualcode_Click_To_Chat
 */

defined( 'ABSPATH' ) || exit;

/**
 * ------------------------------------------------------------------------
 * Plugin constants
 * ------------------------------------------------------------------------
 */
define( 'VIRTUALCODE_CLICK_TO_CHAT_VERSION', '1.1.0' );
define( 'VIRTUALCODE_CLICK_TO_CHAT_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'VIRTUALCODE_CLICK_TO_CHAT_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'VIRTUALCODE_CLICK_TO_CHAT_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
define( 'VIRTUALCODE_CLICK_TO_CHAT_PLUGIN_FILE', __FILE__ );

/**
 * ------------------------------------------------------------------------
 * Default settings values
 * ------------------------------------------------------------------------
 */
defined( 'VIRTUALCODE_CLICK_TO_CHAT_DEFAULT_PHONE' )         || define( 'VIRTUALCODE_CLICK_TO_CHAT_DEFAULT_PHONE', '' );
defined( 'VIRTUALCODE_CLICK_TO_CHAT_DEFAULT_MESSAGE' )       || define( 'VIRTUALCODE_CLICK_TO_CHAT_DEFAULT_MESSAGE', 'Hello, I need help!' );
defined( 'VIRTUALCODE_CLICK_TO_CHAT_DEFAULT_POSITION' )      || define( 'VIRTUALCODE_CLICK_TO_CHAT_DEFAULT_POSITION', 'right' );
defined( 'VIRTUALCODE_CLICK_TO_CHAT_DEFAULT_GAP' )           || define( 'VIRTUALCODE_CLICK_TO_CHAT_DEFAULT_GAP', '20' );
defined( 'VIRTUALCODE_CLICK_TO_CHAT_DEFAULT_BUTTON_TEXT' )   || define( 'VIRTUALCODE_CLICK_TO_CHAT_DEFAULT_BUTTON_TEXT', 'Chat with us' );
defined( 'VIRTUALCODE_CLICK_TO_CHAT_DEFAULT_PRIMARY_COLOR' ) || define( 'VIRTUALCODE_CLICK_TO_CHAT_DEFAULT_PRIMARY_COLOR', '#25D366' );
defined( 'VIRTUALCODE_CLICK_TO_CHAT_DEFAULT_TEXT_COLOR' )    || define( 'VIRTUALCODE_CLICK_TO_CHAT_DEFAULT_TEXT_COLOR', '#ffffff' );
defined( 'VIRTUALCODE_CLICK_TO_CHAT_DEFAULT_TOOLTIP' )       || define( 'VIRTUALCODE_CLICK_TO_CHAT_DEFAULT_TOOLTIP', 'Need help? Chat with us' );
defined( 'VIRTUALCODE_CLICK_TO_CHAT_DEFAULT_DELAY' )         || define( 'VIRTUALCODE_CLICK_TO_CHAT_DEFAULT_DELAY', '0' );
defined( 'VIRTUALCODE_CLICK_TO_CHAT_DEFAULT_BEHAVIOR' )      || define( 'VIRTUALCODE_CLICK_TO_CHAT_DEFAULT_BEHAVIOR', 'direct' );
defined( 'VIRTUALCODE_CLICK_TO_CHAT_DEFAULT_AVATAR' )        || define( 'VIRTUALCODE_CLICK_TO_CHAT_DEFAULT_AVATAR', 'default' );
defined( 'VIRTUALCODE_CLICK_TO_CHAT_DEFAULT_STATUS' )        || define( 'VIRTUALCODE_CLICK_TO_CHAT_DEFAULT_STATUS', 'online' );

/**
 * ------------------------------------------------------------------------
 * Activation / Deactivation hooks
 * ------------------------------------------------------------------------
 */
function virtualcode_click_to_chat_activate() {
	if ( function_exists( 'virtualcode_click_to_chat_get_default_options' ) && ! get_option( 'virtualcode_click_to_chat_settings' ) ) {
		add_option( 'virtualcode_click_to_chat_settings', virtualcode_click_to_chat_get_default_options() );
	}
}

function virtualcode_click_to_chat_deactivate() {
	// Reserved for future cleanup.
}

register_activation_hook( __FILE__, 'virtualcode_click_to_chat_activate' );
register_deactivation_hook( __FILE__, 'virtualcode_click_to_chat_deactivate' );

/**
 * ------------------------------------------------------------------------
 * Load core plugin files (guarded) - ORDER MATTERS
 * ------------------------------------------------------------------------
 */
$virtualcode_click_to_chat_files = array(
	VIRTUALCODE_CLICK_TO_CHAT_PLUGIN_DIR . 'includes/settings.php',     // Settings & defaults (MUST LOAD FIRST)
	VIRTUALCODE_CLICK_TO_CHAT_PLUGIN_DIR . 'includes/class-mobile-detect.php', // Mobile detection class
	VIRTUALCODE_CLICK_TO_CHAT_PLUGIN_DIR . 'includes/others.php',       // Helper functions & frontend
	VIRTUALCODE_CLICK_TO_CHAT_PLUGIN_DIR . 'includes/loader.php',       // Loader & admin hooks
);

foreach ( $virtualcode_click_to_chat_files as $virtualcode_click_to_chat_file ) {
	if ( file_exists( $virtualcode_click_to_chat_file ) ) {
		require_once $virtualcode_click_to_chat_file;
	}
}

/**
 * ------------------------------------------------------------------------
 * Helper: should display widget (Wrapper)
 * ------------------------------------------------------------------------
 */
function virtualcode_click_to_chat_should_display() {
	return (bool) apply_filters( 'virtualcode_click_to_chat_should_display', true );
}

/**
 * ------------------------------------------------------------------------
 * Helper: Local time
 * ------------------------------------------------------------------------
 */
function virtualcode_click_to_chat_get_local_time( $format = 'H:i' ) {
	return current_time( $format );
}

/**
 * ------------------------------------------------------------------------
 * Legacy Helper: Avatar URL (kept for backward compatibility)
 * ------------------------------------------------------------------------
 */
function virtualcode_click_to_chat_get_avatar_url( $avatar = '', $status = '' ) {
	$avatar = $avatar ?: virtualcode_click_to_chat_get_option( 'avatar', VIRTUALCODE_CLICK_TO_CHAT_DEFAULT_AVATAR );
	$status = $status ?: virtualcode_click_to_chat_get_option( 'status', VIRTUALCODE_CLICK_TO_CHAT_DEFAULT_STATUS );

	if ( 'custom' === $avatar ) {
		$custom = virtualcode_click_to_chat_get_option( 'custom_avatar', '' );
		if ( ! empty( $custom ) ) {
			return esc_url( $custom );
		}
	}

	return VIRTUALCODE_CLICK_TO_CHAT_PLUGIN_URL . 'assets/icons/avatar-' . $status . '.png';
}
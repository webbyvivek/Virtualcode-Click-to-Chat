<?php
/**
 * Settings handler for Virtualcode Click to Chat plugin.
 *
 * Registers settings, provides defaults, and sanitizes options.
 *
 * @package Virtualcode_Click_To_Chat
 */

defined( 'ABSPATH' ) || exit;

// Prevent duplicate loading
if ( ! function_exists( 'virtualcode_click_to_chat_get_default_options' ) ) :

/**
 * Get default plugin options.
 *
 * @return array
 */
function virtualcode_click_to_chat_get_default_options() {
	return array(
		// General.
		'enabled'                => 1,
		'phone'                  => '',
		'message'                => '',
		'device_target'          => 'both', // both | desktop | mobile

		// Appearance.
		'position'               => 'right',
		'gap'                    => 20,
		'side_gap'               => 20,
		'bg_color'               => '#25D366',
		'text_color'             => '#ffffff',
		'button_text'            => __( 'Chat with us', 'virtualcode-click-to-chat' ),
		'icon_only'              => 0,
		'icon_size'              => 20,
		'text_size'              => 14,

		// Advanced - Page Targeting.
		'page_targeting_mode'    => 'all',
		'include_pages'          => array(),
		'exclude_pages'          => array(),

		// Advanced - Delay.
		'delay_seconds'          => 0,

		// Advanced - Business Hours.
		'business_hours_enabled' => 0,
		'business_days'          => array(),
		'business_start_time'    => '09:00',
		'business_end_time'      => '18:00',

		// Legacy options.
		'avatar'                 => 'default',
		'status'                 => 'online',
		'custom_avatar'          => '',
	);
}

endif;

/**
 * Register plugin settings.
 */
function virtualcode_click_to_chat_register_settings() {
	register_setting(
		'virtualcode_click_to_chat_settings_group',
		'virtualcode_click_to_chat_settings',
		array(
			'type'              => 'array',
			'sanitize_callback' => 'virtualcode_click_to_chat_sanitize_settings',
			'default'           => virtualcode_click_to_chat_get_default_options(),
		)
	);
}
add_action( 'admin_init', 'virtualcode_click_to_chat_register_settings' );

/**
 * Sanitize and merge settings before saving.
 *
 * @param array $input Raw input from submitted tab.
 * @return array
 */
function virtualcode_click_to_chat_sanitize_settings( $input ) {
	// Get current full settings
	$current_settings = (array) get_option( 'virtualcode_click_to_chat_settings', array() );
	$defaults = virtualcode_click_to_chat_get_default_options();
	
	// Start with current settings (preserves all tabs)
	$merged = wp_parse_args( $current_settings, $defaults );
	
	// Ensure input is array
	if ( ! is_array( $input ) ) {
		$input = array();
	}

	/* -------------------------
	 * General Section
	 * ------------------------- */

	// Enable Chat Link
	if ( array_key_exists( 'enabled', $input ) ) {
		$merged['enabled'] = ! empty( $input['enabled'] ) ? 1 : 0;
	}

	// Phone number
	if ( isset( $input['phone'] ) ) {
		$merged['phone'] = preg_replace( '/[^0-9]/', '', (string) $input['phone'] );
	}

	// Prefilled message
	if ( isset( $input['message'] ) ) {
		$merged['message'] = sanitize_textarea_field( $input['message'] );
	}

	// Device target
	if ( isset( $input['device_target'] ) && in_array( $input['device_target'], array( 'both', 'desktop', 'mobile' ), true ) ) {
		$merged['device_target'] = $input['device_target'];
	}

	/* -------------------------
	 * Appearance Section
	 * ------------------------- */

	// Button position
	if ( isset( $input['position'] ) && in_array( $input['position'], array( 'left', 'right' ), true ) ) {
		$merged['position'] = $input['position'];
	}

	// Gap settings
	if ( isset( $input['gap'] ) ) {
		$merged['gap'] = absint( $input['gap'] );
	}
	
	if ( isset( $input['side_gap'] ) ) {
		$merged['side_gap'] = absint( $input['side_gap'] );
	}

	// Background color
	if ( isset( $input['bg_color'] ) ) {
		$sanitized_color = sanitize_hex_color( $input['bg_color'] );
		if ( $sanitized_color ) {
			$merged['bg_color'] = $sanitized_color;
		}
	}

	// Text color
	if ( isset( $input['text_color'] ) ) {
		$sanitized_color = sanitize_hex_color( $input['text_color'] );
		if ( $sanitized_color ) {
			$merged['text_color'] = $sanitized_color;
		}
	}

	// Button text
	if ( isset( $input['button_text'] ) ) {
		$merged['button_text'] = sanitize_text_field( $input['button_text'] );
	}

	// Icon only mode
	if ( array_key_exists( 'icon_only', $input ) ) {
		$merged['icon_only'] = ! empty( $input['icon_only'] ) ? 1 : 0;
	}

	// Icon size
	if ( isset( $input['icon_size'] ) ) {
		$merged['icon_size'] = max( 12, absint( $input['icon_size'] ) );
	}

	// Text size
	if ( isset( $input['text_size'] ) ) {
		$merged['text_size'] = max( 10, absint( $input['text_size'] ) );
	}

	/* -------------------------
	 * Advanced - Page Targeting
	 * ------------------------- */

	// Page targeting mode
	if ( isset( $input['page_targeting_mode'] ) && in_array( $input['page_targeting_mode'], array( 'all', 'include', 'exclude' ), true ) ) {
		$merged['page_targeting_mode'] = $input['page_targeting_mode'];
	}

	// Include pages
	if ( isset( $input['include_pages'] ) ) {
		if ( is_array( $input['include_pages'] ) ) {
			$merged['include_pages'] = array_values( array_filter( array_map( 'absint', $input['include_pages'] ) ) );
		} else {
			$merged['include_pages'] = array();
		}
	}

	// Exclude pages
	if ( isset( $input['exclude_pages'] ) ) {
		if ( is_array( $input['exclude_pages'] ) ) {
			$merged['exclude_pages'] = array_values( array_filter( array_map( 'absint', $input['exclude_pages'] ) ) );
		} else {
			$merged['exclude_pages'] = array();
		}
	}

	/* -------------------------
	 * Advanced - Delay
	 * ------------------------- */

	if ( isset( $input['delay_seconds'] ) ) {
		$merged['delay_seconds'] = max( 0, absint( $input['delay_seconds'] ) );
	}

	/* -------------------------
	 * Advanced - Business Hours
	 * ------------------------- */

	// Business hours enabled
	if ( array_key_exists( 'business_hours_enabled', $input ) ) {
		$merged['business_hours_enabled'] = ! empty( $input['business_hours_enabled'] ) ? 1 : 0;
	}

	// Business days
	if ( isset( $input['business_days'] ) ) {
		if ( is_array( $input['business_days'] ) ) {
			$merged['business_days'] = array_values( array_filter( array_map( 'sanitize_key', $input['business_days'] ) ) );
		} else {
			$merged['business_days'] = array();
		}
	}

	// Business start time
	if ( isset( $input['business_start_time'] ) && preg_match( '/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', $input['business_start_time'] ) ) {
		$merged['business_start_time'] = $input['business_start_time'];
	}

	// Business end time
	if ( isset( $input['business_end_time'] ) && preg_match( '/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', $input['business_end_time'] ) ) {
		$merged['business_end_time'] = $input['business_end_time'];
	}

	/* -------------------------
	 * Legacy Settings
	 * ------------------------- */
	
	if ( isset( $input['avatar'] ) ) {
		$merged['avatar'] = sanitize_key( $input['avatar'] );
	}
	
	if ( isset( $input['status'] ) ) {
		$merged['status'] = sanitize_key( $input['status'] );
	}
	
	if ( isset( $input['custom_avatar'] ) ) {
		$merged['custom_avatar'] = esc_url_raw( $input['custom_avatar'] );
	}

	return $merged;
}

// Prevent duplicate function declaration
if ( ! function_exists( 'virtualcode_click_to_chat_get_option' ) ) :

/**
 * Get plugin option by key safely.
 *
 * @param string $key     Option key.
 * @param mixed  $default Default value if key not found.
 * @return mixed
 */
function virtualcode_click_to_chat_get_option( $key = '', $default = null ) {
	static $options = null;
	
	// Cache options for performance
	if ( null === $options ) {
		$options = (array) get_option( 'virtualcode_click_to_chat_settings', array() );
		$options = wp_parse_args( $options, virtualcode_click_to_chat_get_default_options() );
	}

	if ( '' === $key ) {
		return $options;
	}

	return array_key_exists( $key, $options ) ? $options[ $key ] : $default;
}

endif;

// Backward compatibility for old function
if ( ! function_exists( 'chatlink_get_option' ) ) :
function chatlink_get_option( $key = '', $default = null ) {
	return virtualcode_click_to_chat_get_option( $key, $default );
}
endif;

/**
 * Debug helper - Log settings (only when WP_DEBUG is enabled)
 * FIXED: Removed debug code for production
 * 
 * @param mixed $message Message to log (disabled in production)
 */
function virtualcode_click_to_chat_debug_log( $message ) {
	// Debug function disabled for production
	// Keep empty function for backward compatibility
	return;
}

// Backward compatibility for debug function
if ( ! function_exists( 'chatlink_debug_log' ) ) :
function chatlink_debug_log( $message ) {
	virtualcode_click_to_chat_debug_log( $message );
}
endif;
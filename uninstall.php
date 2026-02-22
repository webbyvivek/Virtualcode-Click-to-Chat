<?php
/**
 * Uninstall cleanup for Virtualcode Click to Chat plugin.
 *
 * This file runs when the plugin is deleted via the WordPress admin.
 * It removes all plugin-related data from the database.
 *
 * @package Virtualcode_Click_To_Chat
 */

// If uninstall not called from WordPress, exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

/**
 * Get all option names matching a pattern using WordPress cache functions.
 *
 * @param string $pattern SQL LIKE pattern.
 * @return array Array of option names.
 */
function virtualcode_click_to_chat_get_matching_options( $pattern ) {
	global $wpdb;
	
	// Check cache first
	$cache_key = 'virtualcode_click_to_chat_options_' . md5( $pattern );
	$options = wp_cache_get( $cache_key, 'virtualcode_click_to_chat' );
	
	if ( false === $options ) {
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Caching is implemented above
		$options = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT option_name FROM {$wpdb->options} WHERE option_name LIKE %s",
				$pattern
			)
		);
		wp_cache_set( $cache_key, $options, 'virtualcode_click_to_chat', HOUR_IN_SECONDS );
	}
	
	return $options;
}

/**
 * Get all user IDs with meta keys matching a pattern.
 *
 * @param string $pattern SQL LIKE pattern.
 * @return array Array of user IDs.
 */
function virtualcode_click_to_chat_get_users_with_meta_pattern( $pattern ) {
	global $wpdb;
	
	$cache_key = 'virtualcode_click_to_chat_users_' . md5( $pattern );
	$user_ids = wp_cache_get( $cache_key, 'virtualcode_click_to_chat' );
	
	if ( false === $user_ids ) {
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Caching is implemented above
		$user_ids = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT DISTINCT user_id FROM {$wpdb->usermeta} WHERE meta_key LIKE %s",
				$pattern
			)
		);
		wp_cache_set( $cache_key, $user_ids, 'virtualcode_click_to_chat', HOUR_IN_SECONDS );
	}
	
	return $user_ids;
}

/**
 * Get all post IDs with meta keys matching a pattern.
 *
 * @param string $pattern SQL LIKE pattern.
 * @return array Array of post IDs.
 */
function virtualcode_click_to_chat_get_posts_with_meta_pattern( $pattern ) {
	global $wpdb;
	
	$cache_key = 'virtualcode_click_to_chat_posts_' . md5( $pattern );
	$post_ids = wp_cache_get( $cache_key, 'virtualcode_click_to_chat' );
	
	if ( false === $post_ids ) {
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Caching is implemented above
		$post_ids = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT DISTINCT post_id FROM {$wpdb->postmeta} WHERE meta_key LIKE %s",
				$pattern
			)
		);
		wp_cache_set( $cache_key, $post_ids, 'virtualcode_click_to_chat', HOUR_IN_SECONDS );
	}
	
	return $post_ids;
}

/**
 * Get meta keys for a specific user matching a pattern.
 *
 * @param int    $user_id User ID.
 * @param string $pattern SQL LIKE pattern.
 * @return array Array of meta keys.
 */
function virtualcode_click_to_chat_get_user_meta_keys( $user_id, $pattern ) {
	global $wpdb;
	
	$cache_key = 'virtualcode_click_to_chat_user_meta_' . $user_id . '_' . md5( $pattern );
	$meta_keys = wp_cache_get( $cache_key, 'virtualcode_click_to_chat' );
	
	if ( false === $meta_keys ) {
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Caching is implemented above
		$meta_keys = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT meta_key FROM {$wpdb->usermeta} WHERE user_id = %d AND meta_key LIKE %s",
				$user_id,
				$pattern
			)
		);
		wp_cache_set( $cache_key, $meta_keys, 'virtualcode_click_to_chat', HOUR_IN_SECONDS );
	}
	
	return $meta_keys;
}

/**
 * Get meta keys for a specific post matching a pattern.
 *
 * @param int    $post_id Post ID.
 * @param string $pattern SQL LIKE pattern.
 * @return array Array of meta keys.
 */
function virtualcode_click_to_chat_get_post_meta_keys( $post_id, $pattern ) {
	global $wpdb;
	
	$cache_key = 'virtualcode_click_to_chat_post_meta_' . $post_id . '_' . md5( $pattern );
	$meta_keys = wp_cache_get( $cache_key, 'virtualcode_click_to_chat' );
	
	if ( false === $meta_keys ) {
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Caching is implemented above
		$meta_keys = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT meta_key FROM {$wpdb->postmeta} WHERE post_id = %d AND meta_key LIKE %s",
				$post_id,
				$pattern
			)
		);
		wp_cache_set( $cache_key, $meta_keys, 'virtualcode_click_to_chat', HOUR_IN_SECONDS );
	}
	
	return $meta_keys;
}

/**
 * Remove all plugin data from database using WordPress functions instead of direct DB calls.
 */
function virtualcode_click_to_chat_remove_all_data() {
	global $wpdb;
	
	// Delete main plugin options using WordPress functions
	delete_option( 'virtualcode_click_to_chat_settings' );
	delete_option( 'virtualcode_click_to_chat_version' );
	
	// Delete any transients using WordPress functions
	$transient_pattern = '_transient_virtualcode_click_to_chat_%';
	$transient_timeout_pattern = '_transient_timeout_virtualcode_click_to_chat_%';
	
	// Get and delete transients
	$transients = virtualcode_click_to_chat_get_matching_options( $transient_pattern );
	foreach ( $transients as $transient ) {
		$transient_name = str_replace( '_transient_', '', $transient );
		delete_transient( $transient_name );
	}
	
	// Get and delete transient timeouts
	$timeout_transients = virtualcode_click_to_chat_get_matching_options( $transient_timeout_pattern );
	foreach ( $timeout_transients as $transient ) {
		$transient_name = str_replace( '_transient_timeout_', '', $transient );
		delete_transient( $transient_name );
	}
	
	// Clear options cache
	wp_cache_delete( 'alloptions', 'options' );
	
	// Delete user meta using WordPress functions
	$user_meta_pattern = 'virtualcode_click_to_chat_%';
	$user_ids = virtualcode_click_to_chat_get_users_with_meta_pattern( $user_meta_pattern );
	
	foreach ( $user_ids as $user_id ) {
		$meta_keys = virtualcode_click_to_chat_get_user_meta_keys( $user_id, $user_meta_pattern );
		
		foreach ( $meta_keys as $meta_key ) {
			delete_user_meta( $user_id, $meta_key );
		}
		
		// Clear user meta cache
		wp_cache_delete( $user_id, 'user_meta' );
	}
	
	// Delete post meta using WordPress functions
	$post_meta_pattern = '_virtualcode_click_to_chat_%';
	$post_ids = virtualcode_click_to_chat_get_posts_with_meta_pattern( $post_meta_pattern );
	
	foreach ( $post_ids as $post_id ) {
		$meta_keys = virtualcode_click_to_chat_get_post_meta_keys( $post_id, $post_meta_pattern );
		
		foreach ( $meta_keys as $meta_key ) {
			delete_post_meta( $post_id, $meta_key );
		}
		
		// Clear post meta cache
		wp_cache_delete( $post_id, 'post_meta' );
	}
	
	// Clear all plugin caches
	wp_cache_delete( 'virtualcode_click_to_chat_options_%', 'virtualcode_click_to_chat' );
	wp_cache_flush();
}

/**
 * Handle multisite uninstall.
 */
function virtualcode_click_to_chat_handle_multisite() {
	global $wpdb;
	
	// Get all blog IDs - this is a one-time operation during uninstall, so direct query is acceptable
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
	$blog_ids = $wpdb->get_col( "SELECT blog_id FROM {$wpdb->blogs}" );
	
	foreach ( $blog_ids as $blog_id ) {
		switch_to_blog( $blog_id );
		virtualcode_click_to_chat_remove_all_data();
		restore_current_blog();
	}
	
	// Delete network options
	delete_site_option( 'virtualcode_click_to_chat_network_settings' );
	
	// Clear network cache
	wp_cache_flush();
}

// Run uninstall based on site type
if ( is_multisite() ) {
	virtualcode_click_to_chat_handle_multisite();
} else {
	virtualcode_click_to_chat_remove_all_data();
}

// Final cache flush
wp_cache_flush();
<?php
/**
 * Loader file for Virtualcode Click to Chat plugin.
 *
 * Registers admin menu, loads admin/front assets, and wires core hooks.
 *
 * @package Virtualcode_Click_To_Chat
 */

defined( 'ABSPATH' ) || exit;

/**
 * -----------------------------------------------------------------------------
 * Admin Menu Registration
 * -----------------------------------------------------------------------------
 */

/**
 * Register admin menu for Virtualcode Click to Chat.
 */
function virtualcode_click_to_chat_register_admin_menu() {
	add_menu_page(
		__( 'Virtualcode Click to Chat', 'virtualcode-click-to-chat' ),
		__( 'Click to Chat', 'virtualcode-click-to-chat' ),
		'manage_options',
		'virtualcode-click-to-chat',
		'virtualcode_click_to_chat_render_settings_page',
		'dashicons-whatsapp',
		58
	);
}
add_action( 'admin_menu', 'virtualcode_click_to_chat_register_admin_menu' );

/**
 * -----------------------------------------------------------------------------
 * Settings Page Renderer
 * -----------------------------------------------------------------------------
 */

/**
 * Render Virtualcode Click to Chat settings page.
 */
function virtualcode_click_to_chat_render_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	// Get all GET parameters sanitized at once - for UI display only
	$get_params = filter_input_array( INPUT_GET, FILTER_SANITIZE_SPECIAL_CHARS );
	
	// Get current tab - sanitize for UI display only
	$active_tab = 'general';
	if ( isset( $get_params['tab'] ) && is_string( $get_params['tab'] ) ) {
		// Sanitize the tab parameter - this is for UI display only, not form processing
		$raw_tab = sanitize_key( $get_params['tab'] );
		// Validate against allowed tabs
		$allowed_tabs = array( 'general', 'appearance', 'advanced' );
		if ( in_array( $raw_tab, $allowed_tabs, true ) ) {
			$active_tab = $raw_tab;
		}
	}
	
	// Only verify nonce if it's a tab switch request (when nonce is present)
	if ( isset( $get_params['_wpnonce'] ) && isset( $get_params['tab'] ) ) {
		$nonce = sanitize_text_field( $get_params['_wpnonce'] );
		if ( ! wp_verify_nonce( $nonce, 'virtualcode_click_to_chat_settings_tab' ) ) {
			wp_die( esc_html__( 'Security check failed', 'virtualcode-click-to-chat' ) );
		}
	}

	?>
	<div class="wrap">
		<h1><?php echo esc_html__( 'Click to Chat Settings', 'virtualcode-click-to-chat' ); ?></h1>
		
		<?php
		// Tab navigation
		$tabs = array(
			'general'    => __( 'General', 'virtualcode-click-to-chat' ),
			'appearance' => __( 'Appearance', 'virtualcode-click-to-chat' ),
			'advanced'   => __( 'Advanced', 'virtualcode-click-to-chat' ),
		);
		
		$nonce = wp_create_nonce( 'virtualcode_click_to_chat_settings_tab' );
		
		echo '<h2 class="nav-tab-wrapper">';
		foreach ( $tabs as $tab_key => $tab_label ) {
			$tab_url = add_query_arg(
				array(
					'page'     => 'virtualcode-click-to-chat',
					'tab'      => $tab_key,
					'_wpnonce' => $nonce,
				),
				admin_url( 'admin.php' )
			);

			$active_class = ( $active_tab === $tab_key ) ? ' nav-tab-active' : '';
			echo '<a href="' . esc_url( $tab_url ) . '" class="nav-tab' . esc_attr( $active_class ) . '">' . esc_html( $tab_label ) . '</a>';
		}
		echo '</h2>';
		
		// Add form ID based on active tab for JavaScript targeting
		$form_id = 'vc-' . $active_tab . '-form';
		echo '<form method="post" action="options.php" id="' . esc_attr( $form_id ) . '">';
		
		settings_fields( 'virtualcode_click_to_chat_settings_group' );
		wp_nonce_field( 'virtualcode_click_to_chat_settings_action', 'virtualcode_click_to_chat_settings_nonce' );
		
		// Load the active tab content
		$partial = VIRTUALCODE_CLICK_TO_CHAT_PLUGIN_DIR . 'partials/vc-' . $active_tab . '.php';
		if ( file_exists( $partial ) ) {
			require $partial;
		} else {
			echo '<div class="notice notice-error"><p>' . esc_html__( 'Settings section not found.', 'virtualcode-click-to-chat' ) . '</p></div>';
		}
		
		echo '</form>';
		?>
	</div>
	<?php
}

/**
 * -----------------------------------------------------------------------------
 * Plugin Action Links
 * -----------------------------------------------------------------------------
 */

/**
 * Add "Settings" link on Plugins page.
 */
function virtualcode_click_to_chat_add_plugin_settings_link( $links ) {
	$settings_url  = admin_url( 'admin.php?page=virtualcode-click-to-chat' );
	$settings_link = '<a href="' . esc_url( $settings_url ) . '">' . esc_html__( 'Settings', 'virtualcode-click-to-chat' ) . '</a>';
	array_unshift( $links, $settings_link );
	return $links;
}
add_filter( 'plugin_action_links_' . VIRTUALCODE_CLICK_TO_CHAT_PLUGIN_BASENAME, 'virtualcode_click_to_chat_add_plugin_settings_link' );

/**
 * -----------------------------------------------------------------------------
 * Admin Assets
 * -----------------------------------------------------------------------------
 */

/**
 * Enqueue admin assets for Virtualcode Click to Chat settings pages.
 */
function virtualcode_click_to_chat_enqueue_admin_assets( $hook ) {
	if ( 'toplevel_page_virtualcode-click-to-chat' !== $hook ) {
		return;
	}

	// Enqueue WordPress color picker
	wp_enqueue_style( 'wp-color-picker' );
	wp_enqueue_script( 'wp-color-picker' );
	
	// Enqueue main admin CSS
	wp_enqueue_style(
		'vc-admin',
		VIRTUALCODE_CLICK_TO_CHAT_PLUGIN_URL . 'assets/css/vc-admin.css',
		array(),
		VIRTUALCODE_CLICK_TO_CHAT_VERSION
	);
	
	// Enqueue toggle switch CSS
	wp_enqueue_style(
		'vc-toggle',
		VIRTUALCODE_CLICK_TO_CHAT_PLUGIN_URL . 'assets/css/vc-toggle.css',
		array(),
		VIRTUALCODE_CLICK_TO_CHAT_VERSION
	);
	
	// Enqueue fields-dependent CSS
	wp_enqueue_style(
		'vc-fields',
		VIRTUALCODE_CLICK_TO_CHAT_PLUGIN_URL . 'assets/css/vc-fields.css',
		array(),
		VIRTUALCODE_CLICK_TO_CHAT_VERSION
	);

	// Enqueue main admin script (consolidated)
	wp_enqueue_script(
		'vc-admin',
		VIRTUALCODE_CLICK_TO_CHAT_PLUGIN_URL . 'assets/js/vc-admin.js',
		array( 'jquery', 'wp-color-picker' ),
		VIRTUALCODE_CLICK_TO_CHAT_VERSION,
		true
	);
	
	// Get all GET parameters sanitized at once - for UI display only
	$get_params = filter_input_array( INPUT_GET, FILTER_SANITIZE_SPECIAL_CHARS );
	
	// Get current tab for UI display
	$current_tab = 'general';
	
	// Check if tab parameter exists - this is for UI display only, not data processing
	if ( isset( $get_params['tab'] ) && is_string( $get_params['tab'] ) ) {
		$sanitized_tab = sanitize_key( $get_params['tab'] );
		$allowed_tabs = array( 'general', 'appearance', 'advanced' );
		if ( in_array( $sanitized_tab, $allowed_tabs, true ) ) {
			$current_tab = $sanitized_tab;
		}
	}
	
	// Localize script for admin
	wp_localize_script(
		'vc-admin',
		'vcAdmin',
		array(
			'currentTab' => $current_tab,
			'strings'    => array(
				'enabled'  => __( 'Enabled', 'virtualcode-click-to-chat' ),
				'disabled' => __( 'Disabled', 'virtualcode-click-to-chat' ),
				'saved'    => __( 'Settings saved.', 'virtualcode-click-to-chat' ),
				'unsaved'  => __( 'You have unsaved changes. Are you sure you want to leave?', 'virtualcode-click-to-chat' ),
			),
		)
	);
}
add_action( 'admin_enqueue_scripts', 'virtualcode_click_to_chat_enqueue_admin_assets' );

/**
 * -----------------------------------------------------------------------------
 * Frontend Assets
 * -----------------------------------------------------------------------------
 */

/**
 * Enqueue frontend assets for Virtualcode Click to Chat widget.
 */
function virtualcode_click_to_chat_enqueue_frontend_assets() {
	if ( is_admin() ) {
		return;
	}

	// Check if widget should be displayed at all
	if ( ! virtualcode_click_to_chat_should_display() ) {
		return;
	}

	wp_enqueue_style(
		'vc-frontend',
		VIRTUALCODE_CLICK_TO_CHAT_PLUGIN_URL . 'assets/css/vc-frontend.css',
		array(),
		VIRTUALCODE_CLICK_TO_CHAT_VERSION
	);

	wp_enqueue_script(
		'vc-frontend',
		VIRTUALCODE_CLICK_TO_CHAT_PLUGIN_URL . 'assets/js/vc-frontend.js',
		array(),
		VIRTUALCODE_CLICK_TO_CHAT_VERSION,
		true
	);

	// Localize script for frontend
	wp_localize_script(
		'vc-frontend',
		'vcFrontend',
		array(
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
			'nonce'   => wp_create_nonce( 'vc_frontend_nonce' ),
			'strings' => array(
				'chatNow' => __( 'Chat with us on WhatsApp', 'virtualcode-click-to-chat' ),
			),
		)
	);
}
add_action( 'wp_enqueue_scripts', 'virtualcode_click_to_chat_enqueue_frontend_assets' );

/**
 * -----------------------------------------------------------------------------
 * Helper Functions
 * -----------------------------------------------------------------------------
 */

/**
 * Validate tab key to prevent loading invalid partials.
 *
 * @param string $tab Tab key.
 * @return string
 */
function virtualcode_click_to_chat_validate_tab_key( $tab ) {
	$allowed = array( 'general', 'appearance', 'advanced' );
	return in_array( $tab, $allowed, true ) ? $tab : 'general';
}

/**
 * Init hook for Virtualcode Click to Chat.
 * Note: For WordPress.org hosted plugins, translations are loaded automatically.
 * This function is kept for backward compatibility.
 */
function virtualcode_click_to_chat_init() {
	// For WordPress.org hosted plugins, translations are loaded automatically
	// This function is intentionally left empty but kept for compatibility
}
// Note: The 'init' action is intentionally not added as translations are handled automatically by WordPress.org
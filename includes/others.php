<?php
/**
 * Misc helpers & frontend output for Virtualcode Click to Chat.
 *
 * @package Virtualcode_Click_To_Chat
 */

defined( 'ABSPATH' ) || exit;

// Prevent duplicate loading
if ( ! function_exists( 'virtualcode_click_to_chat_get_animation_class' ) ) :

/**
 * Get animation class for frontend buttons.
 */
function virtualcode_click_to_chat_get_animation_class() {
	$base_class   = 'vc-animate';
	$hover_class  = 'vc-anim-hover';
	$active_class = 'vc-anim-active';

	return trim( $base_class . ' ' . $hover_class . ' ' . $active_class );
}

endif;

if ( ! function_exists( 'virtualcode_click_to_chat_get_whatsapp_link' ) ) :

/**
 * Build WhatsApp deep link.
 */
function virtualcode_click_to_chat_get_whatsapp_link() {
	$phone   = virtualcode_click_to_chat_get_option( 'phone', '' );
	$message = virtualcode_click_to_chat_get_option( 'message', '' );

	if ( empty( $phone ) ) {
		return '';
	}

	$phone = preg_replace( '/[^0-9]/', '', $phone );

	$url = 'https://wa.me/' . rawurlencode( $phone );

	if ( ! empty( $message ) ) {
		$url .= '?text=' . rawurlencode( $message );
	}

	return esc_url( $url );
}

endif;

if ( ! function_exists( 'virtualcode_click_to_chat_check_device' ) ) :

/**
 * Check device visibility.
 *
 * @param string $device_target Device target setting.
 * @return bool
 */
function virtualcode_click_to_chat_check_device( $device_target = '' ) {
	$device_target = $device_target ?: virtualcode_click_to_chat_get_option( 'device_target', 'both' );

	if ( 'both' === $device_target ) {
		return true;
	}

	// Make sure class exists
	if ( ! class_exists( 'Virtualcode_Click_To_Chat_MobileDetect' ) ) {
		require_once VIRTUALCODE_CLICK_TO_CHAT_PLUGIN_DIR . 'includes/class-mobile-detect.php';
	}

	$detect = new Virtualcode_Click_To_Chat_MobileDetect();
	
	switch ( $device_target ) {
		case 'desktop':
			return $detect->isDesktop();
		case 'mobile':
			return $detect->isMobile() || $detect->isTablet();
		default:
			return true;
	}
}

endif;

if ( ! function_exists( 'virtualcode_click_to_chat_should_render_on_page' ) ) :

/**
 * Determine if widget should render on current page.
 */
function virtualcode_click_to_chat_should_render_on_page() {
	$mode        = virtualcode_click_to_chat_get_option( 'page_targeting_mode', 'all' );
	$include_ids = (array) virtualcode_click_to_chat_get_option( 'include_pages', array() );
	$exclude_ids = (array) virtualcode_click_to_chat_get_option( 'exclude_pages', array() );

	if ( 'all' === $mode ) {
		return true;
	}

	if ( ! is_singular() ) {
		return ( 'all' === $mode );
	}

	$current_id = get_queried_object_id();

	if ( 'include' === $mode ) {
		return in_array( (int) $current_id, array_map( 'absint', $include_ids ), true );
	}

	if ( 'exclude' === $mode ) {
		return ! in_array( (int) $current_id, array_map( 'absint', $exclude_ids ), true );
	}

	return true;
}

endif;

if ( ! function_exists( 'virtualcode_click_to_chat_is_within_business_hours' ) ) :

/**
 * Check business hours visibility - FIXED overnight logic
 */
function virtualcode_click_to_chat_is_within_business_hours() {
	$enabled = (int) virtualcode_click_to_chat_get_option( 'business_hours_enabled', 0 );
	if ( ! $enabled ) {
		return true;
	}

	$days       = (array) virtualcode_click_to_chat_get_option( 'business_days', array() );
	$start_time = virtualcode_click_to_chat_get_option( 'business_start_time', '09:00' );
	$end_time   = virtualcode_click_to_chat_get_option( 'business_end_time', '18:00' );

	if ( empty( $days ) ) {
		return false; // No days selected = don't show
	}

	$now = current_time( 'timestamp' );
	$day = strtolower( gmdate( 'l', $now ) );

	if ( ! in_array( $day, $days, true ) ) {
		return false;
	}

	list( $start_h, $start_m ) = array_map( 'intval', explode( ':', $start_time ) );
	list( $end_h, $end_m )     = array_map( 'intval', explode( ':', $end_time ) );

	$start_minutes = ( $start_h * 60 ) + $start_m;
	$end_minutes   = ( $end_h * 60 ) + $end_m;
	$current_minutes = (int) gmdate( 'H', $now ) * 60 + (int) gmdate( 'i', $now );

	// Handle overnight hours (e.g., 22:00 to 06:00)
	if ( $end_minutes < $start_minutes ) {
		// If current time is past start OR before end (for overnight)
		return ( $current_minutes >= $start_minutes || $current_minutes <= $end_minutes );
	}

	// Normal same-day hours
	return ( $current_minutes >= $start_minutes && $current_minutes <= $end_minutes );
}

endif;

if ( ! function_exists( 'virtualcode_click_to_chat_render_floating_button' ) ) :

/**
 * Render floating chat button on frontend.
 */
function virtualcode_click_to_chat_render_floating_button() {
	if ( is_admin() ) {
		return;
	}

	$enabled = (int) virtualcode_click_to_chat_get_option( 'enabled', 1 );
	if ( ! $enabled ) {
		return;
	}

	if ( ! virtualcode_click_to_chat_should_render_on_page() || ! virtualcode_click_to_chat_is_within_business_hours() ) {
		return;
	}

	$phone = virtualcode_click_to_chat_get_option( 'phone', '' );
	if ( empty( $phone ) ) {
		return;
	}

	// Device targeting using improved detection
	if ( ! virtualcode_click_to_chat_check_device() ) {
		return;
	}

	$position    = virtualcode_click_to_chat_get_option( 'position', 'right' );
	$gap         = (int) virtualcode_click_to_chat_get_option( 'gap', 20 );
	$side_gap    = (int) virtualcode_click_to_chat_get_option( 'side_gap', 20 );
	$bg_color    = virtualcode_click_to_chat_get_option( 'bg_color', '#25D366' );
	$text_color  = virtualcode_click_to_chat_get_option( 'text_color', '#ffffff' );
	$button_text = virtualcode_click_to_chat_get_option( 'button_text', __( 'Chat with us', 'virtualcode-click-to-chat' ) );
	$icon_only   = (int) virtualcode_click_to_chat_get_option( 'icon_only', 0 );
	$icon_size   = (int) virtualcode_click_to_chat_get_option( 'icon_size', 20 );
	$text_size   = (int) virtualcode_click_to_chat_get_option( 'text_size', 14 );
	$delay       = (int) virtualcode_click_to_chat_get_option( 'delay_seconds', 0 );

	$style  = 'position:fixed;bottom:' . esc_attr( $gap ) . 'px;';
	$style .= ( 'left' === $position ) ? 'left:' . esc_attr( $side_gap ) . 'px;' : 'right:' . esc_attr( $side_gap ) . 'px;';
	$style .= 'background:' . esc_attr( $bg_color ) . ';color:' . esc_attr( $text_color ) . ';';
	$style .= 'text-decoration:none;display:inline-flex;align-items:center;';
	$style .= $icon_only ? '' : 'gap:8px;';
	$style .= 'font-size:' . esc_attr( $text_size ) . 'px;line-height:1;z-index:9999;';
	$style .= 'box-shadow:0 4px 12px rgba(0,0,0,0.15);';
	$style .= $icon_only ? 'padding:12px;border-radius:50%;' : 'padding:12px 16px;border-radius:999px;';

	if ( $delay > 0 ) {
		$style .= 'display:none;';
	}

	$link = virtualcode_click_to_chat_get_whatsapp_link();
	if ( empty( $link ) ) {
		return;
	}

	$icon_url = VIRTUALCODE_CLICK_TO_CHAT_PLUGIN_URL . 'assets/img/whatsapp.svg';
	?>
	<a href="<?php echo esc_url( $link ); ?>"
	   class="vc-floating-button <?php echo esc_attr( virtualcode_click_to_chat_get_animation_class() ); ?>"
	   style="<?php echo esc_attr( $style ); ?>"
	   data-vc-delay="1"
	   data-delay="<?php echo esc_attr( $delay ); ?>"
	   target="_blank"
	   rel="noopener noreferrer nofollow"
	   aria-label="<?php echo esc_attr( $button_text ); ?>">
		<?php if ( ! empty( $icon_url ) ) : ?>
			<img src="<?php echo esc_url( $icon_url ); ?>"
			     width="<?php echo esc_attr( $icon_size ); ?>"
			     height="<?php echo esc_attr( $icon_size ); ?>"
			     alt="<?php esc_attr_e( 'WhatsApp', 'virtualcode-click-to-chat' ); ?>" />
		<?php endif; ?>
		<?php if ( ! $icon_only ) : ?>
			<span class="vc-button-text"><?php echo esc_html( $button_text ); ?></span>
		<?php endif; ?>
	</a>
	<?php
}
add_action( 'wp_footer', 'virtualcode_click_to_chat_render_floating_button' );

endif;

if ( ! function_exists( 'virtualcode_click_to_chat_shortcode_handler' ) ) :

/**
 * Shortcode: [virtualcode_click_to_chat]
 */
function virtualcode_click_to_chat_shortcode_handler( $atts ) {
	$phone = virtualcode_click_to_chat_get_option( 'phone', '' );
	if ( empty( $phone ) ) {
		return '';
	}

	$link = virtualcode_click_to_chat_get_whatsapp_link();
	if ( empty( $link ) ) {
		return '';
	}

	$bg_color    = virtualcode_click_to_chat_get_option( 'bg_color', '#25D366' );
	$text_color  = virtualcode_click_to_chat_get_option( 'text_color', '#ffffff' );
	$button_text = virtualcode_click_to_chat_get_option( 'button_text', __( 'Chat with us', 'virtualcode-click-to-chat' ) );
	$icon_only   = (int) virtualcode_click_to_chat_get_option( 'icon_only', 0 );
	$icon_size   = (int) virtualcode_click_to_chat_get_option( 'icon_size', 20 );
	$text_size   = (int) virtualcode_click_to_chat_get_option( 'text_size', 14 );

	// Shortcode attributes override
	$atts = shortcode_atts(
		array(
			'text'       => $button_text,
			'icon'       => ! $icon_only,
			'icon_size'  => $icon_size,
			'bg_color'   => $bg_color,
			'text_color' => $text_color,
		),
		$atts,
		'virtualcode_click_to_chat'
	);

	$style  = 'display:inline-flex;align-items:center;text-decoration:none;';
	$style .= 'background:' . esc_attr( $atts['bg_color'] ) . ';color:' . esc_attr( $atts['text_color'] ) . ';';
	$style .= $atts['icon'] ? 'gap:8px;' : '';
	$style .= 'padding:10px 14px;border-radius:999px;';
	$style .= 'font-size:' . esc_attr( $atts['icon_size'] ) . 'px;line-height:1;';
	$style .= 'box-shadow:0 4px 10px rgba(0,0,0,0.15);';

	$icon_url = VIRTUALCODE_CLICK_TO_CHAT_PLUGIN_URL . 'assets/img/whatsapp.svg';

	ob_start();
	?>
	<a href="<?php echo esc_url( $link ); ?>"
	   class="vc-shortcode-button <?php echo esc_attr( virtualcode_click_to_chat_get_animation_class() ); ?>"
	   style="<?php echo esc_attr( $style ); ?>"
	   target="_blank"
	   rel="noopener noreferrer nofollow"
	   aria-label="<?php echo esc_attr( $atts['text'] ); ?>">
		<?php if ( $atts['icon'] ) : ?>
			<img src="<?php echo esc_url( $icon_url ); ?>"
			     width="<?php echo esc_attr( $atts['icon_size'] ); ?>"
			     height="<?php echo esc_attr( $atts['icon_size'] ); ?>"
			     alt="<?php esc_attr_e( 'WhatsApp', 'virtualcode-click-to-chat' ); ?>" />
		<?php endif; ?>
		<span class="vc-button-text"><?php echo esc_html( $atts['text'] ); ?></span>
	</a>
	<?php
	return ob_get_clean();
}
add_shortcode( 'virtualcode_click_to_chat', 'virtualcode_click_to_chat_shortcode_handler' );

// Register old shortcode for backward compatibility
add_shortcode( 'chatlink', 'virtualcode_click_to_chat_shortcode_handler' );

endif;

/**
 * Safe getter for plugin options - DEPRECATED but kept for backward compatibility
 */
if ( ! function_exists( 'chatlink_safe_get_option' ) ) {
	function chatlink_safe_get_option( $key, $default = null ) {
		return virtualcode_click_to_chat_get_option( $key, $default );
	}
}
<?php
/**
 * Appearance settings tab for Virtualcode Click to Chat.
 *
 * @package Virtualcode_Click_To_Chat
 */

defined( 'ABSPATH' ) || exit;

// Fetch saved options and defaults.
$vc_defaults = virtualcode_click_to_chat_get_default_options();
$vc_options  = (array) get_option( 'virtualcode_click_to_chat_settings', $vc_defaults );
$vc_options  = wp_parse_args( $vc_options, $vc_defaults );

// Size controls.
$vc_icon_size = isset( $vc_options['icon_size'] ) ? absint( $vc_options['icon_size'] ) : 20;
$vc_text_size = isset( $vc_options['text_size'] ) ? absint( $vc_options['text_size'] ) : 14;

// Current position - FIXED: Use prefixed variable name
$vc_current_position = isset( $vc_options['position'] ) ? $vc_options['position'] : 'right';
?>

<form method="post" action="options.php" id="vc-appearance-form">
	<?php
	settings_fields( 'virtualcode_click_to_chat_settings_group' );
	wp_nonce_field( 'virtualcode_click_to_chat_settings_action', 'virtualcode_click_to_chat_settings_nonce' );
	?>

	<!-- Styles Section -->
	<h2 class="title"><?php esc_html_e( 'Styles', 'virtualcode-click-to-chat' ); ?></h2>
	<p class="description"><?php esc_html_e( 'Control layout, position, spacing, and background styles.', 'virtualcode-click-to-chat' ); ?></p>

	<table class="form-table" role="presentation">
		<tbody>
			
			<!-- Button Position - Simple Toggle -->
<tr>
	<th scope="row"><?php esc_html_e( 'Button Position', 'virtualcode-click-to-chat' ); ?></th>
	<td>
		<div class="vc-simple-toggle">
			<input 
				type="radio" 
				name="virtualcode_click_to_chat_settings[position]" 
				id="vc_position_left" 
				value="left" 
				<?php checked( $vc_current_position, 'left' ); ?>
			>
			<label for="vc_position_left" class="toggle-left <?php echo $vc_current_position === 'left' ? 'active' : ''; ?>">
				<?php esc_html_e( 'Left', 'virtualcode-click-to-chat' ); ?>
			</label>

			<input 
				type="radio" 
				name="virtualcode_click_to_chat_settings[position]" 
				id="vc_position_right" 
				value="right" 
				<?php checked( $vc_current_position, 'right' ); ?>
			>
			<label for="vc_position_right" class="toggle-right <?php echo $vc_current_position === 'right' ? 'active' : ''; ?>">
				<?php esc_html_e( 'Right', 'virtualcode-click-to-chat' ); ?>
			</label>
		</div>
		<p class="description"><?php esc_html_e( 'Choose button position on screen.', 'virtualcode-click-to-chat' ); ?></p>
	</td>
</tr>

			<!-- Button Gaps -->
			<tr>
				<th scope="row"><?php esc_html_e( 'Button Gaps (px)', 'virtualcode-click-to-chat' ); ?></th>
				<td>
					<div style="margin-bottom:8px;">
						<label for="vc_gap" style="display:inline-block; min-width:100px;">
							<?php esc_html_e( 'Bottom Gap:', 'virtualcode-click-to-chat' ); ?>
						</label>
						<input
							type="number"
							id="vc_gap"
							name="virtualcode_click_to_chat_settings[gap]"
							value="<?php echo esc_attr( $vc_options['gap'] ); ?>"
							min="0"
							step="1"
							class="small-text"
						/>
						<span class="description">px</span>
					</div>
					
					<div>
						<label for="vc_side_gap" style="display:inline-block; min-width:100px;">
							<?php esc_html_e( 'Side Gap:', 'virtualcode-click-to-chat' ); ?>
						</label>
						<input
							type="number"
							id="vc_side_gap"
							name="virtualcode_click_to_chat_settings[side_gap]"
							value="<?php echo esc_attr( $vc_options['side_gap'] ); ?>"
							min="0"
							step="1"
							class="small-text"
						/>
						<span class="description">px</span>
					</div>
					
					<p class="description"><?php esc_html_e( 'Distance from screen edges.', 'virtualcode-click-to-chat' ); ?></p>
				</td>
			</tr>

			<!-- Button Background Color -->
			<tr>
				<th scope="row">
					<label for="vc_bg_color"><?php esc_html_e( 'Background Color', 'virtualcode-click-to-chat' ); ?></label>
				</th>
				<td>
					<input
						type="text"
						id="vc_bg_color"
						name="virtualcode_click_to_chat_settings[bg_color]"
						value="<?php echo esc_attr( $vc_options['bg_color'] ); ?>"
						class="vc-color-field"
						data-default-color="#25D366"
					/>
					<p class="description"><?php esc_html_e( 'Button background color.', 'virtualcode-click-to-chat' ); ?></p>
				</td>
			</tr>
		</tbody>
	</table>

	<hr />

	<!-- Icons & Text Section -->
	<h2 class="title"><?php esc_html_e( 'Icons & Text', 'virtualcode-click-to-chat' ); ?></h2>
	<p class="description"><?php esc_html_e( 'Configure icon display, button text, colors, and sizes.', 'virtualcode-click-to-chat' ); ?></p>

	<table class="form-table" role="presentation">
		<tbody>
			<!-- Icon Display Mode -->
			<tr>
				<th scope="row"><?php esc_html_e( 'Icon Display', 'virtualcode-click-to-chat' ); ?></th>
				<td>
					<fieldset>
						<legend class="screen-reader-text">
							<span><?php esc_html_e( 'Icon Display Mode', 'virtualcode-click-to-chat' ); ?></span>
						</legend>
						
						<label for="vc_icon_only_yes" style="margin-right:20px;">
							<input
								type="radio"
								id="vc_icon_only_yes"
								name="virtualcode_click_to_chat_settings[icon_only]"
								value="1"
								<?php checked( ! empty( $vc_options['icon_only'] ), 1 ); ?>
							/>
							<?php esc_html_e( 'Icon Only', 'virtualcode-click-to-chat' ); ?>
						</label>

						<label for="vc_icon_only_no">
							<input
								type="radio"
								id="vc_icon_only_no"
								name="virtualcode_click_to_chat_settings[icon_only]"
								value="0"
								<?php checked( empty( $vc_options['icon_only'] ), 1 ); ?>
							/>
							<?php esc_html_e( 'Icon + Text', 'virtualcode-click-to-chat' ); ?>
						</label>
					</fieldset>
					<p class="description"><?php esc_html_e( 'Choose button display style.', 'virtualcode-click-to-chat' ); ?></p>
				</td>
			</tr>

			<!-- Button Text -->
			<tr>
				<th scope="row">
					<label for="vc_button_text"><?php esc_html_e( 'Button Text', 'virtualcode-click-to-chat' ); ?></label>
				</th>
				<td>
					<input
						type="text"
						id="vc_button_text"
						name="virtualcode_click_to_chat_settings[button_text]"
						value="<?php echo esc_attr( $vc_options['button_text'] ); ?>"
						class="regular-text"
						placeholder="<?php esc_attr_e( 'Chat with us', 'virtualcode-click-to-chat' ); ?>"
						style="max-width: 450px;"
					/>
					<p class="description"><?php esc_html_e( 'Text displayed next to icon.', 'virtualcode-click-to-chat' ); ?></p>
				</td>
			</tr>

			<!-- Button Text Color -->
			<tr>
				<th scope="row">
					<label for="vc_text_color"><?php esc_html_e( 'Text Color', 'virtualcode-click-to-chat' ); ?></label>
				</th>
				<td>
					<input
						type="text"
						id="vc_text_color"
						name="virtualcode_click_to_chat_settings[text_color]"
						value="<?php echo esc_attr( $vc_options['text_color'] ); ?>"
						class="vc-color-field"
						data-default-color="#ffffff"
					/>
					<p class="description"><?php esc_html_e( 'Button text color.', 'virtualcode-click-to-chat' ); ?></p>
				</td>
			</tr>

			<!-- Text Size -->
			<tr>
				<th scope="row">
					<label for="vc_text_size"><?php esc_html_e( 'Text Size', 'virtualcode-click-to-chat' ); ?></label>
				</th>
				<td>
					<input
						type="number"
						id="vc_text_size"
						name="virtualcode_click_to_chat_settings[text_size]"
						value="<?php echo esc_attr( $vc_text_size ); ?>"
						min="10"
						max="32"
						step="1"
						class="small-text"
					/>
					<span class="description">px</span>
					<p class="description"><?php esc_html_e( 'Button text size (10-32px).', 'virtualcode-click-to-chat' ); ?></p>
				</td>
			</tr>

			<!-- Icon Size -->
			<tr>
				<th scope="row">
					<label for="vc_icon_size"><?php esc_html_e( 'Icon Size', 'virtualcode-click-to-chat' ); ?></label>
				</th>
				<td>
					<input
						type="number"
						id="vc_icon_size"
						name="virtualcode_click_to_chat_settings[icon_size]"
						value="<?php echo esc_attr( $vc_icon_size ); ?>"
						min="12"
						max="64"
						step="1"
						class="small-text"
					/>
					<span class="description">px</span>
					<p class="description"><?php esc_html_e( 'Icon width and height (12-64px).', 'virtualcode-click-to-chat' ); ?></p>
				</td>
			</tr>
		</tbody>
	</table>

	<?php submit_button( __( 'Save Changes', 'virtualcode-click-to-chat' ) ); ?>
</form>
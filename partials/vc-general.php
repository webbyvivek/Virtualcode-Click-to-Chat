<?php
/**
 * General settings tab for Virtualcode Click to Chat.
 *
 * @package Virtualcode_Click_To_Chat
 */

defined( 'ABSPATH' ) || exit;

// Load options with defaults.
$vc_defaults = virtualcode_click_to_chat_get_default_options();
$vc_options  = (array) get_option( 'virtualcode_click_to_chat_settings', $vc_defaults );
$vc_options  = wp_parse_args( $vc_options, $vc_defaults );

// Device target - FIXED: Use prefixed variable name
$vc_device_target = isset( $vc_options['device_target'] ) ? $vc_options['device_target'] : 'both';

// Get enabled status - FIXED: Use prefixed variable name
$vc_enabled = ! empty( $vc_options['enabled'] );
?>

<form method="post" action="options.php" id="vc-general-form">
	<?php
	settings_fields( 'virtualcode_click_to_chat_settings_group' );
	wp_nonce_field( 'virtualcode_click_to_chat_settings_action', 'virtualcode_click_to_chat_settings_nonce' );
	?>

	<!-- Hidden field to ensure unchecked checkbox sends value -->
	<input type="hidden" name="virtualcode_click_to_chat_settings[enabled]" value="0" />

	<table class="form-table" role="presentation">
		<tbody>
			<!-- Enable Chat Link - Toggle button -->
			<tr>
				<th scope="row">
					<?php esc_html_e( 'Enable Chat Link', 'virtualcode-click-to-chat' ); ?>
				</th>
				<td>
					<label for="vc_enabled" class="toggle-label">
						<input
							type="checkbox"
							id="vc_enabled"
							name="virtualcode_click_to_chat_settings[enabled]"
							value="1"
							<?php checked( $vc_enabled ); ?>
							aria-describedby="vc-enabled-description"
						/>
						<span class="toggle-switch"></span>
						<span class="toggle-text">
							<?php echo $vc_enabled ? esc_html__( 'Enabled', 'virtualcode-click-to-chat' ) : esc_html__( 'Disabled', 'virtualcode-click-to-chat' ); ?>
						</span>
					</label>
					<p id="vc-enabled-description" class="description">
						<?php esc_html_e( 'Toggle to activate or deactivate the floating WhatsApp chat button.', 'virtualcode-click-to-chat' ); ?>
					</p>
				</td>
			</tr>

			<!-- WhatsApp Phone Number -->
			<tr>
				<th scope="row">
					<label for="vc_phone">
						<?php esc_html_e( 'WhatsApp Phone Number', 'virtualcode-click-to-chat' ); ?>
					</label>
				</th>
				<td>
					<input
						type="tel"
						id="vc_phone"
						name="virtualcode_click_to_chat_settings[phone]"
						value="<?php echo esc_attr( $vc_options['phone'] ); ?>"
						class="regular-text"
						placeholder="919876543210"
						autocomplete="off"
						aria-describedby="vc-phone-description"
					/>
					<p id="vc-phone-description" class="description">
						<?php esc_html_e( 'Enter your WhatsApp number with country code (e.g., 919876543210 for India).', 'virtualcode-click-to-chat' ); ?>
					</p>
				</td>
			</tr>

			<!-- Prefilled Message -->
			<tr>
				<th scope="row">
					<label for="vc_message">
						<?php esc_html_e( 'Prefilled Message', 'virtualcode-click-to-chat' ); ?>
					</label>
				</th>
				<td>
					<textarea
						id="vc_message"
						name="virtualcode_click_to_chat_settings[message]"
						rows="4"
						cols="50"
						class="large-text"
						style="max-width: 450px;"
						aria-describedby="vc-message-description"
					><?php echo esc_textarea( $vc_options['message'] ); ?></textarea>
					<p id="vc-message-description" class="description">
						<?php esc_html_e( 'This message will be pre-filled in the WhatsApp chat.', 'virtualcode-click-to-chat' ); ?>
					</p>
				</td>
			</tr>

			<!-- Display on Devices -->
			<tr>
				<th scope="row">
					<?php esc_html_e( 'Display on Devices', 'virtualcode-click-to-chat' ); ?>
				</th>
				<td>
					<fieldset>
						<legend class="screen-reader-text">
							<span><?php esc_html_e( 'Display on Devices', 'virtualcode-click-to-chat' ); ?></span>
						</legend>
						
						<label for="vc_device_target_both" style="margin-right:20px;">
							<input
								type="radio"
								id="vc_device_target_both"
								name="virtualcode_click_to_chat_settings[device_target]"
								value="both"
								<?php checked( $vc_device_target, 'both' ); ?>
							/>
							<?php esc_html_e( 'All Devices', 'virtualcode-click-to-chat' ); ?>
						</label>

						<label for="vc_device_target_desktop" style="margin-right:20px;">
							<input
								type="radio"
								id="vc_device_target_desktop"
								name="virtualcode_click_to_chat_settings[device_target]"
								value="desktop"
								<?php checked( $vc_device_target, 'desktop' ); ?>
							/>
							<?php esc_html_e( 'Desktop Only', 'virtualcode-click-to-chat' ); ?>
						</label>

						<label for="vc_device_target_mobile">
							<input
								type="radio"
								id="vc_device_target_mobile"
								name="virtualcode_click_to_chat_settings[device_target]"
								value="mobile"
								<?php checked( $vc_device_target, 'mobile' ); ?>
							/>
							<?php esc_html_e( 'Mobile Only', 'virtualcode-click-to-chat' ); ?>
						</label>
					</fieldset>
					<p class="description">
						<?php esc_html_e( 'Choose which devices to show the chat button on.', 'virtualcode-click-to-chat' ); ?>
					</p>
				</td>
			</tr>
		</tbody>
	</table>

	<?php submit_button( __( 'Save Changes', 'virtualcode-click-to-chat' ) ); ?>
</form>
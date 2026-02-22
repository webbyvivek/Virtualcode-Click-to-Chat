<?php
/**
 * Advanced settings tab for Virtualcode Click to Chat.
 *
 * Page Targeting, Delay Popup, Business Hours Visibility
 *
 * @package Virtualcode_Click_To_Chat
 */

defined( 'ABSPATH' ) || exit;

// Load defaults & options safely.
$vc_defaults = function_exists( 'virtualcode_click_to_chat_get_default_options' ) ? virtualcode_click_to_chat_get_default_options() : array();
$vc_options  = (array) get_option( 'virtualcode_click_to_chat_settings', $vc_defaults );
$vc_options  = wp_parse_args( $vc_options, $vc_defaults );

// Values.
$vc_page_targeting_mode = ! empty( $vc_options['page_targeting_mode'] ) ? $vc_options['page_targeting_mode'] : 'all';
$vc_include_pages       = ! empty( $vc_options['include_pages'] ) && is_array( $vc_options['include_pages'] ) ? $vc_options['include_pages'] : array();
$vc_exclude_pages       = ! empty( $vc_options['exclude_pages'] ) && is_array( $vc_options['exclude_pages'] ) ? $vc_options['exclude_pages'] : array();
$vc_delay_seconds       = isset( $vc_options['delay_seconds'] ) ? absint( $vc_options['delay_seconds'] ) : 0;

// Business hours values.
$vc_business_enabled    = ! empty( $vc_options['business_hours_enabled'] );
$vc_business_days       = ! empty( $vc_options['business_days'] ) && is_array( $vc_options['business_days'] ) ? $vc_options['business_days'] : array();
$vc_business_start_time = ! empty( $vc_options['business_start_time'] ) ? $vc_options['business_start_time'] : '09:00';
$vc_business_end_time   = ! empty( $vc_options['business_end_time'] ) ? $vc_options['business_end_time'] : '18:00';

// Fetch pages.
$vc_pages = function_exists( 'get_pages' ) ? get_pages() : array();
$vc_pages = is_array( $vc_pages ) ? $vc_pages : array();

// Days list.
$vc_days = array(
	'monday'    => __( 'Monday', 'virtualcode-click-to-chat' ),
	'tuesday'   => __( 'Tuesday', 'virtualcode-click-to-chat' ),
	'wednesday' => __( 'Wednesday', 'virtualcode-click-to-chat' ),
	'thursday'  => __( 'Thursday', 'virtualcode-click-to-chat' ),
	'friday'    => __( 'Friday', 'virtualcode-click-to-chat' ),
	'saturday'  => __( 'Saturday', 'virtualcode-click-to-chat' ),
	'sunday'    => __( 'Sunday', 'virtualcode-click-to-chat' ),
);
?>

<form method="post" action="options.php" id="vc-advanced-form">
	<?php
	settings_fields( 'virtualcode_click_to_chat_settings_group' );
	wp_nonce_field( 'virtualcode_click_to_chat_settings_action', 'virtualcode_click_to_chat_settings_nonce' );
	?>

	<!-- Hidden fields to ensure unchecked checkboxes send values -->
	<input type="hidden" name="virtualcode_click_to_chat_settings[include_pages]" value="" />
	<input type="hidden" name="virtualcode_click_to_chat_settings[exclude_pages]" value="" />
	<input type="hidden" name="virtualcode_click_to_chat_settings[business_days]" value="" />
	<input type="hidden" name="virtualcode_click_to_chat_settings[business_hours_enabled]" value="0" />

	<!-- Page Targeting Section -->
	<h2 class="title"><?php esc_html_e( 'Page Targeting', 'virtualcode-click-to-chat' ); ?></h2>
	<p class="description"><?php esc_html_e( 'Control on which pages the chat button appears.', 'virtualcode-click-to-chat' ); ?></p>

	<table class="form-table" role="presentation">
		<tbody>
			<tr>
				<th scope="row"><?php esc_html_e( 'Display On', 'virtualcode-click-to-chat' ); ?></th>
				<td>
					<fieldset>
						<legend class="screen-reader-text">
							<span><?php esc_html_e( 'Page Targeting Mode', 'virtualcode-click-to-chat' ); ?></span>
						</legend>
						
						<label for="vc_page_targeting_all" style="display:block; margin-bottom:8px;">
							<input
								type="radio"
								id="vc_page_targeting_all"
								name="virtualcode_click_to_chat_settings[page_targeting_mode]"
								value="all"
								<?php checked( $vc_page_targeting_mode, 'all' ); ?>
							/>
							<?php esc_html_e( 'Entire Website', 'virtualcode-click-to-chat' ); ?>
						</label>

						<label for="vc_page_targeting_include" style="display:block; margin-bottom:8px;">
							<input
								type="radio"
								id="vc_page_targeting_include"
								name="virtualcode_click_to_chat_settings[page_targeting_mode]"
								value="include"
								<?php checked( $vc_page_targeting_mode, 'include' ); ?>
							/>
							<?php esc_html_e( 'Include Specific Pages', 'virtualcode-click-to-chat' ); ?>
						</label>

						<label for="vc_page_targeting_exclude" style="display:block;">
							<input
								type="radio"
								id="vc_page_targeting_exclude"
								name="virtualcode_click_to_chat_settings[page_targeting_mode]"
								value="exclude"
								<?php checked( $vc_page_targeting_mode, 'exclude' ); ?>
							/>
							<?php esc_html_e( 'Exclude Specific Pages', 'virtualcode-click-to-chat' ); ?>
						</label>
					</fieldset>
				</td>
			</tr>

			<!-- Include Pages - Disabled when not in Include mode -->
			<tr id="vc-include-pages-row" class="vc-page-targeting-dependent" style="<?php echo 'include' !== $vc_page_targeting_mode ? 'opacity:0.6;' : ''; ?>">
				<th scope="row"><?php esc_html_e( 'Include Pages', 'virtualcode-click-to-chat' ); ?></th>
				<td>
					<div class="vc-page-checkboxes" style="max-width: 450px;">
						<?php if ( empty( $vc_pages ) ) : ?>
							<p class="description"><?php esc_html_e( 'No pages found.', 'virtualcode-click-to-chat' ); ?></p>
						<?php else : ?>
							<?php foreach ( $vc_pages as $vc_page ) : ?>
								<label style="display:block; margin-bottom:4px;">
									<input
										type="checkbox"
										name="virtualcode_click_to_chat_settings[include_pages][]"
										value="<?php echo esc_attr( $vc_page->ID ); ?>"
										<?php checked( in_array( (int) $vc_page->ID, $vc_include_pages, true ) ); ?>
										<?php echo 'include' !== $vc_page_targeting_mode ? 'disabled' : ''; ?>
									/>
									<?php echo esc_html( $vc_page->post_title ); ?>
								</label>
							<?php endforeach; ?>
						<?php endif; ?>
					</div>
					<p class="description"><?php esc_html_e( 'Select pages where the button should appear.', 'virtualcode-click-to-chat' ); ?></p>
				</td>
			</tr>

			<!-- Exclude Pages - Disabled when not in Exclude mode -->
			<tr id="vc-exclude-pages-row" class="vc-page-targeting-dependent" style="<?php echo 'exclude' !== $vc_page_targeting_mode ? 'opacity:0.6;' : ''; ?>">
				<th scope="row"><?php esc_html_e( 'Exclude Pages', 'virtualcode-click-to-chat' ); ?></th>
				<td>
					<div class="vc-page-checkboxes" style="max-width: 450px;">
						<?php if ( empty( $vc_pages ) ) : ?>
							<p class="description"><?php esc_html_e( 'No pages found.', 'virtualcode-click-to-chat' ); ?></p>
						<?php else : ?>
							<?php foreach ( $vc_pages as $vc_page ) : ?>
								<label style="display:block; margin-bottom:4px;">
									<input
										type="checkbox"
										name="virtualcode_click_to_chat_settings[exclude_pages][]"
										value="<?php echo esc_attr( $vc_page->ID ); ?>"
										<?php checked( in_array( (int) $vc_page->ID, $vc_exclude_pages, true ) ); ?>
										<?php echo 'exclude' !== $vc_page_targeting_mode ? 'disabled' : ''; ?>
									/>
									<?php echo esc_html( $vc_page->post_title ); ?>
								</label>
							<?php endforeach; ?>
						<?php endif; ?>
					</div>
					<p class="description"><?php esc_html_e( 'Select pages where the button should NOT appear.', 'virtualcode-click-to-chat' ); ?></p>
				</td>
			</tr>
		</tbody>
	</table>

	<hr />

	<!-- Delay Popup Section -->
	<h2 class="title"><?php esc_html_e( 'Delay Popup', 'virtualcode-click-to-chat' ); ?></h2>
	<p class="description"><?php esc_html_e( 'Delay the appearance of the chat button.', 'virtualcode-click-to-chat' ); ?></p>

	<table class="form-table" role="presentation">
		<tbody>
			<tr>
				<th scope="row">
					<label for="vc_delay_seconds"><?php esc_html_e( 'Show after (seconds)', 'virtualcode-click-to-chat' ); ?></label>
				</th>
				<td>
					<input
						type="number"
						id="vc_delay_seconds"
						name="virtualcode_click_to_chat_settings[delay_seconds]"
						value="<?php echo esc_attr( $vc_delay_seconds ); ?>"
						min="0"
						max="300"
						step="1"
						class="small-text"
						aria-describedby="vc-delay-description"
					/>
					<span class="description"><?php esc_html_e( 'seconds', 'virtualcode-click-to-chat' ); ?></span>
					<p id="vc-delay-description" class="description">
						<?php esc_html_e( 'Set 0 to display immediately. Maximum 300 seconds (5 minutes).', 'virtualcode-click-to-chat' ); ?>
					</p>
				</td>
			</tr>
		</tbody>
	</table>

	<hr />

	<!-- Business Hours Section -->
<h2 class="title"><?php esc_html_e( 'Business Hours Visibility', 'virtualcode-click-to-chat' ); ?></h2>
<p class="description"><?php esc_html_e( 'Show button only during specific hours and days.', 'virtualcode-click-to-chat' ); ?></p>

<table class="form-table" role="presentation">
	<tbody>
		<tr>
			<th scope="row"><?php esc_html_e( 'Enable Business Hours', 'virtualcode-click-to-chat' ); ?></th>
			<td>
				<label for="vc_business_hours_enabled" class="toggle-label">
					<input
						type="checkbox"
						id="vc_business_hours_enabled"
						name="virtualcode_click_to_chat_settings[business_hours_enabled]"
						value="1"
						<?php checked( $vc_business_enabled, true ); ?>
						aria-describedby="vc-business-hours-description"
					/>
					<span class="toggle-switch"></span>
					<span class="toggle-text">
						<?php echo $vc_business_enabled ? esc_html__( 'Enabled', 'virtualcode-click-to-chat' ) : esc_html__( 'Disabled', 'virtualcode-click-to-chat' ); ?>
					</span>
				</label>
				<p id="vc-business-hours-description" class="description">
					<?php esc_html_e( 'Enable to restrict chat button visibility to specific days and times.', 'virtualcode-click-to-chat' ); ?>
				</p>
			</td>
		</tr>

		<tr class="vc-business-hours-fields" id="vc-business-days-row" style="<?php echo ! $vc_business_enabled ? 'opacity:0.6;' : ''; ?>">
			<th scope="row"><?php esc_html_e( 'Business Days', 'virtualcode-click-to-chat' ); ?></th>
			<td>
				<div style="max-width: 450px;">
					<?php foreach ( $vc_days as $vc_day_key => $vc_day_label ) : ?>
						<label style="display:inline-block; margin-right:15px; margin-bottom:8px;">
							<input
								type="checkbox"
								name="virtualcode_click_to_chat_settings[business_days][]"
								value="<?php echo esc_attr( $vc_day_key ); ?>"
								<?php checked( in_array( $vc_day_key, $vc_business_days, true ) ); ?>
								<?php echo ! $vc_business_enabled ? 'disabled' : ''; ?>
							/>
							<?php echo esc_html( $vc_day_label ); ?>
						</label>
					<?php endforeach; ?>
				</div>
				<p class="description"><?php esc_html_e( 'Select days when the chat button should be visible.', 'virtualcode-click-to-chat' ); ?></p>
			</td>
		</tr>

		<tr class="vc-business-hours-fields" id="vc-business-time-row" style="<?php echo ! $vc_business_enabled ? 'opacity:0.6;' : ''; ?>">
			<th scope="row"><?php esc_html_e( 'Business Time', 'virtualcode-click-to-chat' ); ?></th>
			<td>
				<label for="vc_business_start_time" style="margin-right:20px;">
					<?php esc_html_e( 'Start:', 'virtualcode-click-to-chat' ); ?>
					<input
						type="time"
						id="vc_business_start_time"
						name="virtualcode_click_to_chat_settings[business_start_time]"
						value="<?php echo esc_attr( $vc_business_start_time ); ?>"
						<?php echo ! $vc_business_enabled ? 'disabled' : ''; ?>
						aria-describedby="vc-business-time-description"
					/>
				</label>
				
				<label for="vc_business_end_time">
					<?php esc_html_e( 'End:', 'virtualcode-click-to-chat' ); ?>
					<input
						type="time"
						id="vc_business_end_time"
						name="virtualcode_click_to_chat_settings[business_end_time]"
						value="<?php echo esc_attr( $vc_business_end_time ); ?>"
						<?php echo ! $vc_business_enabled ? 'disabled' : ''; ?>
					/>
				</label>
				<p id="vc-business-time-description" class="description">
					<?php esc_html_e( 'Set your business hours (24-hour format).', 'virtualcode-click-to-chat' ); ?>
				</p>
			</td>
		</tr>
	</tbody>
</table>

	<?php submit_button( __( 'Save Changes', 'virtualcode-click-to-chat' ) ); ?>
</form>
/**
 * Virtualcode Click to Chat Main Admin JavaScript
 * 
 * Consolidated admin functionality for all tabs
 * Optimized for performance with single file approach
 * 
 * @package Virtualcode_Click_To_Chat
 */

(function($) {
	'use strict';

	// Cache DOM elements
	var $document = $(document);
	var $window = $(window);
	var formChanged = false;
	var currentTab = vcAdmin.currentTab || 'general';

	/**
	 * Initialize all admin functionality based on current tab
	 */
	function initVCAdmin() {
		initColorPicker();
		initNumberInputs();
		initUnsavedWarning();
		initToggleButtons();
		
		// Tab-specific initializations
		switch (currentTab) {
			case 'general':
				initGeneralTab();
				break;
			case 'appearance':
				initAppearanceTab();
				break;
			case 'advanced':
				initAdvancedTab();
				break;
		}
		
		console.log('VC Admin initialized for tab: ' + currentTab);
	}

	/**
	 * Initialize WordPress color picker
	 */
	function initColorPicker() {
		if ($.fn.wpColorPicker) {
			$('.vc-color-field').wpColorPicker({
				change: function() {
					markFormChanged();
				}
			});
		}
	}

	/**
	 * Handle number inputs to ensure valid ranges
	 */
	function initNumberInputs() {
		$('input[type="number"][min]').on('change', function() {
			var $this = $(this);
			var min = parseInt($this.attr('min'), 10) || 0;
			var max = parseInt($this.attr('max'), 10) || 999;
			var value = parseInt($this.val(), 10) || min;
			
			if (value < min) {
				$this.val(min);
			} else if (value > max) {
				$this.val(max);
			}
			
			markFormChanged();
		});
	}

	/**
	 * Mark form as changed for unsaved warning
	 */
	function markFormChanged() {
		formChanged = true;
	}

	/**
	 * Initialize unsaved changes warning
	 */
	function initUnsavedWarning() {
		$('form[id^="vc-"]').on('change', 'input, select, textarea', markFormChanged);

		$window.on('beforeunload', function() {
			if (formChanged) {
				return vcAdmin.strings.unsaved;
			}
		});

		$('form[id^="vc-"]').on('submit', function() {
			formChanged = false;
		});
	}

	/**
	 * Initialize toggle buttons (used in General and Advanced tabs)
	 */
	function initToggleButtons() {
		$('.toggle-label input[type="checkbox"]').each(function() {
			var $checkbox = $(this);
			var $toggleText = $checkbox.closest('.toggle-label').find('.toggle-text');
			
			function updateToggleText() {
				if ($toggleText.length) {
					$toggleText.text($checkbox.is(':checked') ? 
						vcAdmin.strings.enabled : 
						vcAdmin.strings.disabled);
				}
			}
			
			// Set initial text
			updateToggleText();
			
			// Update on change
			$checkbox.on('change', function() {
				updateToggleText();
				markFormChanged();
			});
		});
	}

	/**
	 * Initialize General tab functionality
	 */
	function initGeneralTab() {
		console.log('General Tab: Initializing...');
		
		// Phone number validation
		$('#vc_phone').on('input', function() {
			var $this = $(this);
			var value = $this.val().replace(/[^0-9]/g, '');
			$this.val(value);
		});
	}

	/**
	 * Initialize Appearance tab functionality
	 */
	function initAppearanceTab() {
		console.log('Appearance Tab: Initializing...');
		
		// Position toggle functionality
		var $leftRadio = $('#vc_position_left');
		var $rightRadio = $('#vc_position_right');
		var $leftLabel = $('label[for="vc_position_left"]');
		var $rightLabel = $('label[for="vc_position_right"]');
		
		if ($leftRadio.length && $rightRadio.length) {
			
			function updatePositionToggle() {
				if ($leftRadio.is(':checked')) {
					$leftLabel.addClass('active');
					$rightLabel.removeClass('active');
				} else {
					$rightLabel.addClass('active');
					$leftLabel.removeClass('active');
				}
			}
			
			// Set initial state
			updatePositionToggle();
			
			// Update on change
			$leftRadio.add($rightRadio).on('change', function() {
				updatePositionToggle();
				markFormChanged();
			});
		}
		
		// Icon only toggle affects text fields
		var $iconOnlyRadios = $('input[name="virtualcode_click_to_chat_settings[icon_only]"]');
		var $textFields = $('.vc-text-dependent-fields');
		
		if ($iconOnlyRadios.length && $textFields.length) {
			function toggleTextFields() {
				var isIconOnly = $('#vc_icon_only_yes').is(':checked');
				if (isIconOnly) {
					$textFields.addClass('vc-text-disabled');
					$textFields.find('input, select, textarea').prop('disabled', true);
				} else {
					$textFields.removeClass('vc-text-disabled');
					$textFields.find('input, select, textarea').prop('disabled', false);
				}
			}
			
			// Set initial state
			toggleTextFields();
			
			// Update on change
			$iconOnlyRadios.on('change', toggleTextFields);
		}
	}

	/**
	 * Initialize Advanced tab functionality
	 */
	function initAdvancedTab() {
		console.log('Advanced Tab: Initializing...');
		
		// Page targeting elements
		var $pageRadios = $('input[name="virtualcode_click_to_chat_settings[page_targeting_mode]"]');
		var $includeRow = $('#vc-include-pages-row');
		var $excludeRow = $('#vc-exclude-pages-row');
		
		if ($pageRadios.length) {
			
			function togglePageTargeting() {
				var mode = $pageRadios.filter(':checked').val();
				
				// Include Pages row
				if (mode === 'include') {
					$includeRow.css('opacity', '1');
					$includeRow.find('input').prop('disabled', false);
				} else {
					$includeRow.css('opacity', '0.6');
					$includeRow.find('input').prop('disabled', true);
				}
				
				// Exclude Pages row
				if (mode === 'exclude') {
					$excludeRow.css('opacity', '1');
					$excludeRow.find('input').prop('disabled', false);
				} else {
					$excludeRow.css('opacity', '0.6');
					$excludeRow.find('input').prop('disabled', true);
				}
			}
			
			// Set initial state
			togglePageTargeting();
			
			// Update on change
			$pageRadios.on('change', function() {
				togglePageTargeting();
				markFormChanged();
			});
		}
		
		// Business hours fields toggle
		var $businessToggle = $('#vc_business_hours_enabled');
		var $businessFields = $('.vc-business-hours-fields');
		
		if ($businessToggle.length && $businessFields.length) {
			
			function toggleBusinessHours() {
				var enabled = $businessToggle.is(':checked');
				
				if (enabled) {
					$businessFields.css('opacity', '1');
					$businessFields.find('input, select').prop('disabled', false);
				} else {
					$businessFields.css('opacity', '0.6');
					$businessFields.find('input, select').prop('disabled', true);
				}
			}
			
			// Set initial state
			toggleBusinessHours();
			
			// Update on change
			$businessToggle.on('change', function() {
				toggleBusinessHours();
				markFormChanged();
			});
		}
	}

	// Initialize on document ready
	$document.ready(function() {
		initVCAdmin();
	});

})(jQuery);
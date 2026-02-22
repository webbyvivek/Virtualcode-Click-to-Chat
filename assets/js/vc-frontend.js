/**
 * Virtualcode Click to Chat Frontend JavaScript
 * 
 * Handles frontend functionality including delay popup
 * Optimized for performance and compatibility
 * 
 * @package Virtualcode_Click_To_Chat
 */

(function() {
	'use strict';

	// Cache DOM elements
	var buttons = [];
	var vcFrontend = window.vcFrontend || { 
		strings: { chatNow: 'Chat with us on WhatsApp' } 
	};

	/**
	 * Initialize frontend functionality
	 */
	function initVC() {
		// Find all floating buttons with delay enabled
		buttons = document.querySelectorAll('.vc-floating-button[data-vc-delay="1"]');
		
		if (!buttons.length) {
			return;
		}

		// Process each button
		buttons.forEach(function(button) {
			var delay = parseInt(button.getAttribute('data-delay'), 10);
			
			// Validate delay
			if (isNaN(delay) || delay <= 0) {
				// Show immediately if no delay
				button.style.display = 'inline-flex';
				return;
			}

			// Ensure button is hidden initially
			button.style.display = 'none';
			
			// Show after specified delay
			setTimeout(function() {
				button.style.display = 'inline-flex';
				
				// Add entrance animation class
				button.classList.add('vc-entrance');
				
				// Remove animation class after it completes
				setTimeout(function() {
					button.classList.remove('vc-entrance');
				}, 500);
			}, delay * 1000);
		});

		// Initialize additional frontend features
		initAnalyticsTracking();
		initAccessibilityFeatures();
	}

	/**
	 * Initialize analytics tracking
	 */
	function initAnalyticsTracking() {
		var allButtons = document.querySelectorAll('.vc-floating-button, .vc-shortcode-button');
		
		allButtons.forEach(function(button) {
			button.addEventListener('click', function(e) {
				// Track click event if analytics is available
				if (typeof gtag !== 'undefined') {
					gtag('event', 'chat_click', {
						'event_category': 'WhatsApp',
						'event_label': 'Chat Button'
					});
				} else if (typeof _gaq !== 'undefined') {
					_gaq.push(['_trackEvent', 'WhatsApp', 'Click', 'Chat Button']);
				}
				
				// Custom event for other analytics
				var event = new CustomEvent('vc_click', {
					detail: { button: button }
				});
				document.dispatchEvent(event);
			});
		});
	}

	/**
	 * Initialize accessibility features
	 */
	function initAccessibilityFeatures() {
		var allButtons = document.querySelectorAll('.vc-floating-button, .vc-shortcode-button');
		
		allButtons.forEach(function(button) {
			// Ensure proper ARIA labels
			if (!button.getAttribute('aria-label')) {
				var buttonText = button.querySelector('.vc-button-text');
				if (buttonText) {
					button.setAttribute('aria-label', buttonText.textContent.trim() + ' - ' + vcFrontend.strings.chatNow);
				} else {
					button.setAttribute('aria-label', vcFrontend.strings.chatNow);
				}
			}
			
			// Add keyboard support
			button.addEventListener('keydown', function(e) {
				if (e.key === 'Enter' || e.key === ' ') {
					e.preventDefault();
					this.click();
				}
			});
		});
	}

	/**
	 * Handle reduced motion preference
	 */
	function handleReducedMotion() {
		var motionQuery = window.matchMedia('(prefers-reduced-motion: reduce)');
		
		if (motionQuery.matches) {
			document.documentElement.classList.add('reduce-motion');
		}
		
		motionQuery.addEventListener('change', function(e) {
			if (e.matches) {
				document.documentElement.classList.add('reduce-motion');
			} else {
				document.documentElement.classList.remove('reduce-motion');
			}
		});
	}

	/**
	 * Initialize based on document ready state
	 */
	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', function() {
			initVC();
			handleReducedMotion();
		});
	} else {
		// DOM is already ready
		initVC();
		handleReducedMotion();
	}

	// Also initialize on window load for any dynamically added buttons
	window.addEventListener('load', function() {
		// Re-check for any dynamically added buttons
		initVC();
	});

})();
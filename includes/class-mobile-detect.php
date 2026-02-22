<?php
/**
 * Mobile Detection Class for Virtualcode Click to Chat
 *
 * Simple mobile detection without external dependencies
 * 
 * @package Virtualcode_Click_To_Chat
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Virtualcode_Click_To_Chat_MobileDetect' ) ) :

class Virtualcode_Click_To_Chat_MobileDetect {
    
    /**
     * User agent string
     *
     * @var string
     */
    private $user_agent;
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->user_agent = isset( $_SERVER['HTTP_USER_AGENT'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ) : '';
    }
    
    /**
     * Check if device is mobile
     *
     * @return bool
     */
    public function isMobile() {
        if ( empty( $this->user_agent ) ) {
            return false;
        }
        
        // Common mobile devices
        $mobile_agents = array(
            'Mobile',
            'Android',
            'Silk/',
            'Kindle',
            'BlackBerry',
            'Opera Mini',
            'Opera Mobi',
            'iPhone',
            'iPad',
            'iPod',
            'Windows Phone',
            'IEMobile',
            'WPDesktop',
            'webOS',
            'hpwOS',
            'Tablet',
            'PlayBook',
            'BB10',
            'RIM Tablet'
        );
        
        foreach ( $mobile_agents as $agent ) {
            if ( stripos( $this->user_agent, $agent ) !== false ) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Check if device is tablet
     *
     * @return bool
     */
    public function isTablet() {
        if ( empty( $this->user_agent ) ) {
            return false;
        }
        
        // Common tablet devices
        $tablet_agents = array(
            'iPad',
            'Kindle',
            'Silk/',
            'Tablet',
            'PlayBook',
            'TouchPad'
        );
        
        foreach ( $tablet_agents as $agent ) {
            if ( stripos( $this->user_agent, $agent ) !== false ) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Check if device is desktop
     *
     * @return bool
     */
    public function isDesktop() {
        return ! $this->isMobile() && ! $this->isTablet();
    }
}

endif;
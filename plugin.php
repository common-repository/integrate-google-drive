<?php

/**
 * Plugin Name: Integrate Google Drive
 * Plugin URI:  https://softlabbd.com/integrate-google-drive
 * Description: Seamless Google Drive integration for WordPress, allowing you to embed, share, play, and download documents and media files directly from Google Drive to your WordPress site.
 * Version:     1.4.4
 * Author:      SoftLab
 * Author URI:  https://softlabbd.com/
 * Text Domain: integrate-google-drive
 * Domain Path: /languages/
 *
 */
// don't call the file directly
if ( !defined( 'ABSPATH' ) ) {
    wp_die( __( 'You can\'t access this page', 'integrate-google-drive' ) );
}
if ( function_exists( 'igd_fs' ) ) {
    igd_fs()->set_basename( false, __FILE__ );
} else {
    // DO NOT REMOVE THIS IF, IT IS ESSENTIAL FOR THE `function_exists` CALL ABOVE TO PROPERLY WORK.
    if ( !function_exists( 'igd_fs' ) ) {
        // Create a helper function for easy SDK access.
        function igd_fs() {
            global $igd_fs;
            if ( !isset( $igd_fs ) ) {
                // Include Freemius SDK.
                require_once dirname( __FILE__ ) . '/freemius/start.php';
                $igd_fs = fs_dynamic_init( array(
                    'id'             => '9618',
                    'slug'           => 'integrate-google-drive',
                    'type'           => 'plugin',
                    'public_key'     => 'pk_eb27e7eaa4f2692b385aec28288f2',
                    'is_premium'     => false,
                    'premium_suffix' => '(PRO)',
                    'has_addons'     => false,
                    'has_paid_plans' => true,
                    'trial'          => array(
                        'days'               => 3,
                        'is_require_payment' => true,
                    ),
                    'menu'           => array(
                        'slug'       => 'integrate-google-drive',
                        'first-path' => 'admin.php?page=integrate-google-drive-getting-started',
                        'contact'    => false,
                        'support'    => false,
                    ),
                    'is_live'        => true,
                ) );
            }
            return $igd_fs;
        }

        // Init Freemius.
        igd_fs();
        // Signal that SDK was initiated.
        do_action( 'igd_fs_loaded' );
    }
    /** define constants */
    define( 'IGD_VERSION', '1.4.4' );
    define( 'IGD_DB_VERSION', '1.4.4' );
    define( 'IGD_FILE', __FILE__ );
    define( 'IGD_PATH', dirname( IGD_FILE ) );
    define( 'IGD_INCLUDES', IGD_PATH . '/includes' );
    define( 'IGD_URL', plugins_url( '', IGD_FILE ) );
    define( 'IGD_ASSETS', IGD_URL . '/assets' );
    // Check min-php version
    if ( version_compare( PHP_VERSION, '7.0', '<=' ) ) {
        deactivate_plugins( plugin_basename( IGD_FILE ) );
        $notice = sprintf( 'Unsupported PHP version. %1$s requires WordPress version %2$s or greater. Please update your PHP to the latest version.', '<strong>Google Drive to WordPress</strong>', '<strong>5.6.0</strong>' );
        wp_die( $notice );
    }
    // Check min-wp version
    if ( !version_compare( get_bloginfo( 'version' ), '5.0', '>=' ) ) {
        deactivate_plugins( plugin_basename( IGD_FILE ) );
        $notice = sprintf( 'Unsupported WordPress version. %1$s requires WordPress version %2$s or greater. Please update your WordPress to the latest version.', '<strong>Google Drive to WordPress</strong>', '<strong>5.0</strong>' );
        wp_die( $notice );
    }
    //Include the base plugin file.
    include_once IGD_INCLUDES . '/base.php';
}
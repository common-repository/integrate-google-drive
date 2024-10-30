<?php

namespace IGD;

defined( 'ABSPATH' ) || exit;
/**
 * Class Install
 */
class Install {
    /**
     * Plugin activation logic
     *
     * @since 1.0.0
     */
    public static function activate() {
        self::load_dependencies();
        $updater = Update::instance();
        if ( $updater->needs_update() ) {
            self::safe_update( $updater );
        } else {
            self::create_default_data();
            self::add_default_settings();
            self::create_tables();
        }
    }

    /**
     * Load required dependencies for the plugin activation and update process.
     */
    private static function load_dependencies() {
        if ( !class_exists( 'IGD\\Update' ) ) {
            require_once IGD_INCLUDES . '/class-update.php';
        }
    }

    /**
     * Safely perform updates, with error handling and logging.
     *
     * @param Update $updater Instance of the Update class
     */
    private static function safe_update( $updater ) {
        try {
            $updater->perform_updates();
        } catch ( \Exception $e ) {
            error_log( '[IGD Update Error] ' . $e->getMessage() );
        }
    }

    /**
     * Add default settings during plugin activation.
     */
    public static function add_default_settings() {
        // Only add settings if this is a fresh install
        if ( get_option( 'igd_version' ) ) {
            return;
        }
        $settings = igd_get_settings();
        // Setup default integrations
        $integrations = [
            'classic-editor',
            'gutenberg-editor',
            'elementor',
            'divi'
        ];
        $settings['integrations'] = $integrations;
        update_option( 'igd_settings', $settings );
    }

    /**
     * Deactivate the plugin and clean up scheduled events.
     */
    public static function deactivate() {
        self::remove_scheduled_events();
    }

    /**
     * Remove scheduled cron events during deactivation.
     */
    private static function remove_scheduled_events() {
        $hooks = [
            'igd_sync_interval',
            'igd_statistics_daily_report',
            'igd_statistics_weekly_report',
            'igd_statistics_monthly_report'
        ];
        foreach ( $hooks as $hook ) {
            $timestamp = wp_next_scheduled( $hook );
            if ( $timestamp ) {
                wp_unschedule_event( $timestamp, $hook );
            }
        }
    }

    /**
     * Create required database tables during plugin activation.
     */
    public static function create_tables() {
        global $wpdb;
        $wpdb->hide_errors();
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        $tables = self::get_table_definitions();
        foreach ( $tables as $table ) {
            dbDelta( $table );
        }
    }

    /**
     * Get the table creation SQL for the plugin.
     *
     * @return array List of SQL table creation statements
     */
    private static function get_table_definitions() {
        global $wpdb;
        return [
            // Files table
            "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}integrate_google_drive_files (\n                id VARCHAR(60) NOT NULL,\n                name TEXT NULL,\n                size BIGINT NULL,\n                parent_id TEXT,\n                account_id TEXT NOT NULL,\n                type VARCHAR(255) NOT NULL,\n                extension VARCHAR(10) NOT NULL,\n                data LONGTEXT,\n                is_computers TINYINT(1) DEFAULT 0,\n                is_shared_with_me TINYINT(1) DEFAULT 0,\n                is_starred TINYINT(1) DEFAULT 0,\n                is_shared_drive TINYINT(1) DEFAULT 0,\n                created TEXT NULL,\n                updated TEXT NULL,\n                PRIMARY KEY (id)\n            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",
            // Shortcodes table
            "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}integrate_google_drive_shortcodes (\n                id BIGINT(20) NOT NULL AUTO_INCREMENT,\n                title VARCHAR(255) NULL,\n                status VARCHAR(6) NULL DEFAULT 'on',\n                config LONGTEXT NULL,\n                locations LONGTEXT NULL,\n                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,\n                updated_at TIMESTAMP NULL,\n                PRIMARY KEY (id)\n            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",
            // Logs table
            "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}integrate_google_drive_logs (\n                id INT NOT NULL AUTO_INCREMENT,\n                type VARCHAR(255) NULL,\n                user_id INT NULL,\n                file_id TEXT NOT NULL,\n                file_type TEXT NULL,\n                file_name TEXT NULL,\n                account_id TEXT NOT NULL,\n                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,\n                PRIMARY KEY (id)\n            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",
        ];
    }

    /**
     * Create default data for the plugin (version, installation time, etc.).
     */
    private static function create_default_data() {
        if ( !get_option( 'igd_version' ) ) {
            update_option( 'igd_version', IGD_VERSION );
        }
        if ( !get_option( 'igd_db_version' ) ) {
            update_option( 'igd_db_version', IGD_DB_VERSION );
        }
        if ( !get_option( 'igd_install_time' ) ) {
            update_option( 'igd_install_time', current_time( 'mysql' ) );
        }
        set_transient( 'igd_rating_notice_interval', 'off', 10 * DAY_IN_SECONDS );
    }

}

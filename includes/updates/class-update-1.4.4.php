<?php

namespace IGD;

defined( 'ABSPATH' ) || exit();

class Update_1_4_4 {
	private static $instance;

	public function __construct() {
		self::create_tables();
	}

	/**
	 * Create required database tables during plugin activation.
	 */
	public static function create_tables() {
		global $wpdb;

		$wpdb->hide_errors();
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$tables = self::get_table_definitions();
		$table_names = self::get_table_names();

		// Check if the tables already exist before running dbDelta
		foreach ( $table_names as $table_name ) {
			$table_exists = $wpdb->get_var( "SHOW TABLES LIKE '{$table_name}'" );
			if ( $table_exists !== $table_name ) {
				// If the table does not exist, run dbDelta
				dbDelta( $tables[ $table_name ] );
			}
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
			"{$wpdb->prefix}integrate_google_drive_files" => "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}integrate_google_drive_files (
                id VARCHAR(60) NOT NULL,
                name TEXT NULL,
                size BIGINT NULL,
                parent_id TEXT,
                account_id TEXT NOT NULL,
                type VARCHAR(255) NOT NULL,
                extension VARCHAR(10) NOT NULL,
                data LONGTEXT,
                is_computers TINYINT(1) DEFAULT 0,
                is_shared_with_me TINYINT(1) DEFAULT 0,
                is_starred TINYINT(1) DEFAULT 0,
                is_shared_drive TINYINT(1) DEFAULT 0,
                created TEXT NULL,
                updated TEXT NULL,
                PRIMARY KEY (id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

			// Shortcodes table
			"{$wpdb->prefix}integrate_google_drive_shortcodes" => "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}integrate_google_drive_shortcodes (
                id BIGINT(20) NOT NULL AUTO_INCREMENT,
                title VARCHAR(255) NULL,
                status VARCHAR(6) NULL DEFAULT 'on',
                config LONGTEXT NULL,
                locations LONGTEXT NULL,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP NULL,
                PRIMARY KEY (id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

			// Logs table
			"{$wpdb->prefix}integrate_google_drive_logs" => "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}integrate_google_drive_logs (
                id INT NOT NULL AUTO_INCREMENT,
                type VARCHAR(255) NULL,
                user_id INT NULL,
                file_id TEXT NOT NULL,
                file_type TEXT NULL,
                file_name TEXT NULL,
                account_id TEXT NOT NULL,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;"
		];
	}

	/**
	 * Get the table names for easy reference.
	 *
	 * @return array List of table names
	 */
	private static function get_table_names() {
		global $wpdb;

		return [
			"{$wpdb->prefix}integrate_google_drive_files",
			"{$wpdb->prefix}integrate_google_drive_shortcodes",
			"{$wpdb->prefix}integrate_google_drive_logs"
		];
	}

	public static function instance() {
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

}

Update_1_4_4::instance();

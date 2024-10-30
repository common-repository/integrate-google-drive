<?php

namespace IGD;

defined( 'ABSPATH' ) || exit();

class Update {

	private static $instance = null;

	/**
	 * Array of plugin versions that require database upgrades.
	 *
	 * @var array
	 */
	private static $required_updates = array(
		'1.4.1',
		'1.4.3',
		'1.4.4',
	);

	/**
	 * Get the installed plugin version from the database.
	 *
	 * @return string
	 */
	public function get_installed_version() {
		return get_option( 'igd_version', 0 );
	}

	/**
	 * Get the installed database schema version.
	 *
	 * @return string
	 */
	public function get_installed_db_version() {
		return get_option( 'igd_db_version', 0 );
	}

	/**
	 * Check if a database or plugin update is required.
	 *
	 * @return bool
	 */
	public function needs_update() {
		$plugin_version = $this->get_installed_version();
		$db_version     = $this->get_installed_db_version();

		// If both versions are empty, it's likely a fresh install, no updates required
		// Check $db_version in future
		if ( empty( $plugin_version ) ) {
			return false;
		}

		// Check if the installed DB version is older than the current version
		// TODO: check db version in future
		return version_compare( $plugin_version, IGD_VERSION, '<' );
	}

	/**
	 * Perform all the necessary updates.
	 *
	 * @return void
	 */
	public function perform_updates() {
		$installed_version    = $this->get_installed_version();
		$installed_db_version = $this->get_installed_db_version();

		foreach ( self::$required_updates as $version ) {
			// Check if this update needs to be applied
			// TODO: also check db version in future
			if ( version_compare( $installed_version, $version, '<' ) ) {
				$this->apply_update( $version );
			}
		}

		// Finalize by setting the version numbers after updates are complete
		$this->update_version_options( IGD_VERSION, IGD_DB_VERSION );
	}

	/**
	 * Apply updates for a specific version.
	 *
	 * @param string $version Version to be updated.
	 *
	 * @return void
	 */
	private function apply_update( $version ) {
		$update_file = IGD_INCLUDES . "/updates/class-update-$version.php";

		if ( file_exists( $update_file ) ) {
			include_once $update_file;
		} else {
			error_log( "[IGD Update Error] Update file for version $version not found." );
		}

		// Optionally add logging or error handling here
		$this->update_version_options( $version, $version ); // Assuming plugin and DB versions sync
	}

	/**
	 * Update the version options in the database.
	 *
	 * @param string $plugin_version
	 * @param string $db_version
	 *
	 * @return void
	 */
	private function update_version_options( $plugin_version, $db_version ) {
		update_option( 'igd_version', $plugin_version );
		update_option( 'igd_db_version', $db_version );
	}

	/**
	 * Get singleton instance of the Update class.
	 *
	 * @return Update
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

}

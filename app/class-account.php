<?php

namespace IGD;

defined( 'ABSPATH' ) || exit();

class Account {

	private static $instance = null;

	public $user_id = 'admin';

	public function __construct( $user_id = null ) {
		$this->user_id = $user_id ?: $this->get_account_user_id();
	}

	/**
	 * @param $id
	 *
	 * @return array|false|mixed|null
	 */
	public function get_accounts( $id = null ) {

		$accounts = array_filter( get_option( 'igd_accounts', [] ) );

		if ( $id ) {
			return ! empty( $accounts[ $id ] ) ? $accounts[ $id ] : [];
		}

		if ( ! empty( $this->user_id ) ) {
			//accounts added by users
			$accounts = array_filter( $accounts, function ( $account ) {
				$user_ids = ! empty( $account['user_id'] ) ? $account['user_id'] : [ 'admin' ];

				return in_array( $this->user_id, $user_ids );
			} );

		} else {
			$accounts = array_filter( $accounts, function ( $account ) {
				$user_ids = ! empty( $account['user_id'] ) ? $account['user_id'] : [ 'admin' ];

				return in_array( 'admin', $user_ids );
			} );
		}

		return ! empty( $accounts ) ? $accounts : [];
	}

	/**
	 * Add new account or e previous account
	 *
	 * @param $data
	 */
	public function update_account( $data ) {
		// Retrieve and filter existing accounts
		$accounts = array_filter( get_option( 'igd_accounts', [] ) );

		// If the account already exists, merge user IDs ensuring uniqueness
		if ( ! empty( $accounts[ $data['id'] ] ) ) {
			$existing_account = $accounts[ $data['id'] ];
			$data['user_id']  = array_unique( array_merge( $existing_account['user_id'], $data['user_id'] ) );
		}

		// Update the account with new data
		$accounts[ $data['id'] ] = $data;

		// Save the updated accounts and reset the account notice
		update_option( 'igd_accounts', $accounts );
		update_option( 'igd_account_notice', false );

		return $data;
	}

	public function set_active_account_id( $account_id ) {
		$accounts = $this->get_accounts();

		if ( ! empty( $accounts[ $account_id ] ) ) {
			$this->set_active_account_id_cookie( $account_id );
		} elseif ( ! empty( $accounts ) ) {
			$account = @array_shift( $accounts );
			$this->set_active_account_id_cookie( $account['id'] );
		} else {
			$this->clear_active_account_id_cookie();
		}
	}

	public function get_active_account() {
		$accounts = $this->get_accounts();

		$account_id = $this->get_active_account_id_from_cookie();

		if ( ! empty( $account_id ) ) {

			$account = ! empty( $accounts[ $account_id ] ) ? $accounts[ $account_id ] : [];

			// If user ID doesn't match, clear cookie and set account to the first available one
			if ( ! empty( $this->user_id ) && ! empty( $account['user_id'] ) && ! in_array( $this->user_id, (array) $account['user_id'] ) ) {
				$this->clear_active_account_id_cookie();
				$account = array_shift( $accounts ) ?? [];
			}

			// If account ID in the cookie is invalid, clear the cookie
			if ( ! empty( $account['id'] ) && empty( $accounts[ $account['id'] ] ) ) {
				$this->clear_active_account_id_cookie();
				$account = array_shift( $accounts ) ?? [];
			}

		} else {
			// Set account to the first available one if no valid cookie found
			$account = array_shift( $accounts ) ?? [];
		}

		return $account;

	}

	// Helper functions for setting, getting, and clearing the cookie
	private function set_active_account_id_cookie( $account_id ) {
		setcookie( 'igd_active_account_id', $account_id, time() + ( 30 * DAY_IN_SECONDS ), "/" );
	}

	private function clear_active_account_id_cookie() {
		setcookie( 'igd_active_account_id', '', time() - 3600, "/" );
	}

	private function get_active_account_id_from_cookie() {
		return $_COOKIE['igd_active_account_id'] ?? '';
	}

	/**
	 * @param $account_id
	 *
	 * @return void
	 */
	public function delete_account( $account_id ) {
		$accounts = array_filter( get_option( 'igd_accounts', [] ) );

		$removed_account = $accounts[ $account_id ];

		// Check if account has only one user then remove account
		if ( empty( $removed_account['user_id'] ) || count( $removed_account['user_id'] ) == 1 ) {

			// Delete all the account files
			igd_delete_cache( [], $account_id );

			// Delete token
			$authorization = new Authorization( $removed_account );
			$authorization->remove_token();

			// Remove account data from saved accounts
			unset( $accounts[ $account_id ] );

		} else {
			// Remove user from account
			$removed_account['user_id'] = array_unique( array_diff( $removed_account['user_id'], [ $this->user_id ] ) );
			$accounts[ $account_id ]    = $removed_account;
		}

		$active_account = $this->get_active_account();

		// Update active account
		if ( ! empty( $active_account ) && $account_id == $active_account['id'] ) {
			if ( count( $accounts ) ) {
				self::set_active_account_id( array_key_first( $accounts ) );
			}
		}

		update_option( 'igd_accounts', $accounts );
	}

	public function get_account_user_id() {
		// Default user_id is 'admin'
		$user_id = 'admin';

		// Get the referer URL to determine the context
		$referer = wp_get_referer();

		// Define patterns to search in the referer that indicate we should get the current user ID
		$patterns = [
			'_dokan_edit_product_nonce',
			'_dokan_add_product_nonce', // Dokan plugin actions
			'/settings/google-drive/', // Shared setting page for both plugins
			'/create-course' // Tutor plugin action
		];

		// Check if any pattern matches the referer
		foreach ( $patterns as $pattern ) {
			if ( strpos( $referer, $pattern ) !== false ) {
				$user_id = get_current_user_id();
				break; // Exit the loop once a match is found
			}
		}

		return $user_id;
	}

	public function get_root_id( $account_id = null ) {
		$account = $this->get_accounts( $account_id );

		return ! empty( $account['root_id'] ) ? $account['root_id'] : false;
	}

	public function get_active_account_id() {
		$account = $this->get_active_account();

		return $account['id'] ?? false;
	}

	public function get_storage_usage( $account_id = null ) {

		if ( ! $account_id ) {
			$account_id = $this->get_active_account_id();
		}

		$service = App::instance( $account_id )->getService();

		try {
			$about = $service->about->get( [ 'fields' => 'storageQuota' ] );
			$usage = $about->getStorageQuota()->getUsage();

			// Update active account data
			$accounts                                    = $this->get_accounts();
			$accounts[ $account_id ]['storage']['usage'] = $usage;

			update_option( 'igd_accounts', $accounts );

			return $usage;

		} catch ( \Exception $exception ) {
			error_log( sprintf( 'IGD Error: syncing storage size: %s', $exception->getMessage() ) );
		}

		return false;

	}

	public function get_specific_folders( $account_id ) {
		$account = $this->get_accounts( $account_id );

		return ! empty( $account['specific_folders'] ) ? $account['specific_folders'] : [];
	}

	public function get_access_token( $account_id ) {
		$tokens = get_option( 'igd_tokens', [] );

		return ! empty( $tokens[ $account_id ] ) ? $tokens[ $account_id ] : false;
	}

	public static function instance( $user_id = null ) {
		if ( ! self::$instance || self::$instance->user_id != $user_id ) {
			self::$instance = new self( $user_id );
		}

		return self::$instance;
	}

}

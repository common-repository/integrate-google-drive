<?php

namespace IGD;

defined( 'ABSPATH' ) || exit();

class Update_1_4_1 {
	private static $instance;

	public function __construct() {
		if ( ! empty( get_option( 'igd_accounts' ) ) ) {
			update_option( 'igd_account_notice', true );
			update_option( 'igd_accounts', [] );
		}

	}

	public static function instance() {
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

}

Update_1_4_1::instance();
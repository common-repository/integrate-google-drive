<?php

namespace IGD;

defined( 'ABSPATH' ) || exit();

class Update_1_4_3 {
	private static $instance;

	public function __construct() {
		global $wpdb;
		$wpdb->query( "UPDATE $wpdb->usermeta SET meta_key = 'igd_folders' WHERE meta_key = '{$wpdb->prefix}folders'" );
	}

	public static function instance() {
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

}

Update_1_4_3::instance();
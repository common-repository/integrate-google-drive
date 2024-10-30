<?php

namespace IGD;

defined( 'ABSPATH' ) || exit;

class Stream {

	protected static $instance = null;
	private $file;

	public function __construct( $file_id, $account_id, $ignore_limit = false ) {

		// Check download restrictions
		if ( ! $ignore_limit && igd_fs()->can_use_premium_code__premium_only() && $limit_message = Restrictions::instance()->has_reached_download_limit( $file_id, 'stream' ) ) {
			Restrictions::display_error( $limit_message );
		}

		$app = App::instance( $account_id );

		$file = $app->get_file_by_id( $file_id );

		// Check if shortcut file then get the original file
		if ( igd_is_shortcut( $file['type'] ) ) {
			$file = $app->get_file_by_id( $file['shortcutDetails']['targetId'] );
		}

		$this->file = $file;

		wp_using_ext_object_cache( false );
	}

	public function stream_content() {

		$referrer     = wp_get_raw_referer();
		$is_tutor_lms = strpos( $referrer, '/courses/' ) !== false;

		if ( igd_get_settings( 'secureVideoPlayback' ) && empty( $referrer ) ) {
			wp_die( 'Unauthorized access' );
		}

		do_action( 'igd_insert_log', 'stream', $this->file['id'], $this->file['accountId'] );

		if ( function_exists( 'apache_setenv' ) ) {
			@apache_setenv( 'no-gzip', 1 );
		}

		@ini_set( 'zlib.output_compression', 'Off' );
		@session_write_close();

		// Stop WP from buffering
		wp_ob_end_flush_all();

		$chunk_size = $this->get_chunk_size( $is_tutor_lms ? 'high' : '' );

		$size = $this->file['size'] ?? 0; // Assuming you have the file size

		$length = $size;           // Content length
		$start  = 0;               // Start byte
		$end    = $size - 1;       // End byte
		header( 'Accept-Ranges: bytes' );
		header( 'Content-Type: ' . $this->file['type'] );

		$seconds_to_cache = 60 * 60 * 24;
		$ts               = gmdate( 'D, d M Y H:i:s', time() + $seconds_to_cache ) . ' GMT';
		header( "Expires: {$ts}" );
		header( 'Pragma: cache' );
		header( "Cache-Control: max-age={$seconds_to_cache}" );

		if ( isset( $_SERVER['HTTP_RANGE'] ) ) {
			$c_end = $end;
			list( , $range ) = explode( '=', $_SERVER['HTTP_RANGE'], 2 );

			if ( false !== strpos( $range, ',' ) ) {
				header( 'HTTP/1.1 416 Requested Range Not Satisfiable' );
				header( "Content-Range: bytes {$start}-{$end}/{$size}" );

				exit;
			}

			if ( '-' == $range ) {
				$c_start = $size - substr( $range, 1 );
			} else {
				$range   = explode( '-', $range );
				$c_start = (int) $range[0];

				if ( isset( $range[1] ) && is_numeric( $range[1] ) ) {
					$c_end = (int) $range[1];
				} else {
					$c_end = $size;
				}

				if ( $c_end - $c_start > $chunk_size ) {
					$c_end = $c_start + $chunk_size;
				}
			}
			$c_end = ( $c_end > $end ) ? $end : $c_end;

			if ( $c_start > $c_end || $c_start > $size - 1 || $c_end >= $size ) {
				header( 'HTTP/1.1 416 Requested Range Not Satisfiable' );
				header( "Content-Range: bytes {$start}-{$end}/{$size}" );

				exit;
			}

			$start = $c_start;

			$end    = $c_end;
			$length = $end - $start + 1;
			header( 'HTTP/1.1 206 Partial Content' );
		}

		header( "Content-Range: bytes {$start}-{$end}/{$size}" );
		header( 'Content-Length: ' . $length );

		$chunk_start = $start;

		@ini_set( 'max_execution_time', 0 );

		while ( $chunk_start <= $end ) {
			// Output the chunk
			$chunk_end = ( ( ( $chunk_start + $chunk_size ) > $end ) ? $end : $chunk_start + $chunk_size );
			$this->stream_get_chunk( $chunk_start, $chunk_end );

			$chunk_start = $chunk_end + 1;

			igd_server_throttle( $is_tutor_lms ? 'high' : '' );
		}
	}

	private function stream_get_chunk( $start, $end, $chunked = true ) {
		if ( $chunked ) {
			$headers = [ 'Range' => 'bytes=' . $start . '-' . $end ];
		}

		// Add Resources key to give permission to access the item
		if ( $this->file['resourceKey'] ) {
			$headers['X-Goog-Drive-Resource-Keys'] = $this->file['id'] . '/' . $this->file['resourceKey'];
		}

		$request = new \IGDGoogle_Http_Request( $this->get_api_url(), 'GET', $headers );
		$request->disableGzip();

		$client = App::instance()->client;

		$client->getIo()->setOptions(
			[
				CURLOPT_RETURNTRANSFER => false,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_RANGE          => null,
				CURLOPT_NOBODY         => null,
				CURLOPT_HEADER         => false,
				CURLOPT_WRITEFUNCTION  => [ $this, 'stream_chunk_to_output' ],
				CURLOPT_CONNECTTIMEOUT => null,
				CURLOPT_TIMEOUT        => null,
			]
		);

		$client->getAuth()->authenticatedRequest( $request );
	}

	/**
	 * Callback function for CURLOPT_WRITEFUNCTION, This is what prints the chunk.
	 *
	 * @param \CurlHandle $ch
	 * @param string $str
	 *
	 * @return int
	 */
	public function stream_chunk_to_output( $ch, $str ) {
		echo $str;

		return strlen( $str );
	}


	private function get_chunk_size( $value = '' ) {
		$value = $value ?: igd_get_settings( 'serverThrottle', 'off' );

		switch ( $value ) {
			case 'high':
				$chunk_size = 1024 * 1024 * 2;
				break;
			case 'medium':
				$chunk_size = 1024 * 1024 * 10;
				break;
			case 'low':
				$chunk_size = 1024 * 1024 * 20;
				break;
			case 'off':
			default:
				$chunk_size = 1024 * 1024 * 50;
				break;
		}

		return min( igd_get_free_memory_available() - ( 1024 * 1024 * 5 ), $chunk_size ); // Chunks size or less if memory isn't sufficient;
	}

	public function get_api_url() {
		return 'https://www.googleapis.com/drive/v3/files/' . $this->file['id'] . '?alt=media';
	}

	/**
	 * Returns an instance of this class.
	 *
	 * @param $file_id int
	 * @param  $account_id string
	 *
	 * @return Stream|null The instance of the class.
	 */
	public static function instance( $file_id, $account_id, $ignore_limit = false ) {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self( $file_id, $account_id, $ignore_limit );
		}

		return self::$instance;
	}
}

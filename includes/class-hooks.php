<?php

namespace IGD;

defined( 'ABSPATH' ) || exit;
class Hooks {
    /**
     * @var null
     */
    protected static $instance = null;

    public function __construct() {
        // Handle uninstall
        igd_fs()->add_action( 'after_uninstall', [$this, 'uninstall'] );
        // Set custom app credentials
        $clientID = igd_get_settings( 'clientID' );
        $clientSecret = igd_get_settings( 'clientSecret' );
        $ownApp = igd_get_settings( 'ownApp' );
        if ( !empty( $ownApp ) && !empty( $clientID ) && !empty( $clientSecret ) ) {
            add_filter( 'igd/client_id', function () use($clientID) {
                return $clientID;
            } );
            add_filter( 'igd/client_secret', function () use($clientSecret) {
                return $clientSecret;
            } );
            add_filter( 'igd/redirect_uri', function () {
                return admin_url( '?action=integrate-google-drive-authorization' );
            } );
        }
        // IGD render form upload field data
        add_filter(
            'igd_render_form_field_data',
            [$this, 'render_form_field_data'],
            10,
            2
        );
        // Handle oAuth authorization
        add_action( 'admin_init', [$this, 'handle_authorization'] );
        // Register query var
        add_filter( 'query_vars', [$this, 'add_query_vars'] );
        // Get preview thumbnail
        add_action( 'template_redirect', [$this, 'preview_image'] );
        add_action( 'template_redirect', [$this, 'direct_content'] );
        // Handle direct download
        add_action( 'template_redirect', [$this, 'direct_download'] );
        // Handle direct stream
        add_action( 'template_redirect', [$this, 'direct_stream'] );
        // Update lost account
        add_action( 'igd_lost_authorization_notice', [$this, 'update_lost_account'] );
    }

    public function update_lost_account( $account_id = null ) {
        if ( !$account_id ) {
            $account_id = igd_get_active_account_id();
        }
        $account = Account::instance()->get_accounts( $account_id );
        if ( empty( $account ) ) {
            return;
        }
        $account['lost'] = true;
        Account::instance()->update_account( $account );
    }

    public function direct_download() {
        if ( get_query_var( 'igd_download' ) ) {
            $file_id = ( !empty( $_REQUEST['id'] ) ? sanitize_text_field( $_REQUEST['id'] ) : '' );
            $file_ids = ( !empty( $_REQUEST['file_ids'] ) ? json_decode( base64_decode( sanitize_text_field( $_REQUEST['file_ids'] ) ) ) : [] );
            $request_id = ( !empty( $_REQUEST['id'] ) ? sanitize_text_field( $_REQUEST['id'] ) : '' );
            $account_id = ( !empty( $_REQUEST['accountId'] ) ? sanitize_text_field( $_REQUEST['accountId'] ) : '' );
            $mimetype = ( !empty( $_REQUEST['mimetype'] ) ? sanitize_text_field( $_REQUEST['mimetype'] ) : 'default' );
            $ignore_limit = !empty( $_REQUEST['ignore_limit'] );
            if ( !empty( $file_ids ) ) {
                igd_download_zip( $file_ids, $request_id, $account_id );
            } elseif ( !empty( $file_id ) ) {
                Download::instance(
                    $file_id,
                    $account_id,
                    $mimetype,
                    false,
                    $ignore_limit
                )->start_download();
            }
            exit;
        }
    }

    public function direct_stream() {
        if ( get_query_var( 'igd_stream' ) ) {
            $file_id = ( !empty( $_REQUEST['id'] ) ? sanitize_text_field( $_REQUEST['id'] ) : '' );
            $account_id = ( !empty( $_REQUEST['account_id'] ) ? sanitize_text_field( $_REQUEST['account_id'] ) : '' );
            Stream::instance( $file_id, $account_id )->stream_content();
            exit;
        }
    }

    public function add_query_vars( $vars ) {
        $vars[] = 'igd_preview_image';
        $vars[] = 'igd_download';
        $vars[] = 'igd_stream';
        $vars[] = 'direct_file';
        return $vars;
    }

    public function preview_image() {
        if ( $preview_image = get_query_var( 'igd_preview_image' ) ) {
            $image_data = json_decode( base64_decode( sanitize_text_field( $preview_image ) ), true );
            $id = $image_data['id'] ?? '';
            $account_id = $image_data['accountId'] ?? '';
            $size = $image_data['size'] ?? 'medium';
            $from_server = false;
            $transient = get_transient( 'igd_latest_fetch_' . $id );
            if ( !$transient ) {
                $from_server = true;
                set_transient( 'igd_latest_fetch_' . $id, true, 60 * MINUTE_IN_SECONDS );
            }
            $file = App::instance( $account_id )->get_file_by_id( $id, $from_server );
            if ( empty( $file ) ) {
                wp_die( 'File not found', 404 );
            }
            $thumbnailLink = $file['thumbnailLink'] ?? '';
            if ( 'custom' == $size ) {
                $w = $image_data['w'] ?? 300;
                $h = $image_data['h'] ?? 300;
                $thumb_url = str_replace( '=s220', "=w{$w}-h{$h}", $thumbnailLink );
            } else {
                if ( 'small' === $size ) {
                    $thumb_url = str_replace( '=s220', '=w300-h300', $thumbnailLink );
                } else {
                    if ( 'medium' === $size ) {
                        $thumb_url = str_replace( '=s220', '=h600-nu', $thumbnailLink );
                    } else {
                        if ( 'large' === $size ) {
                            $thumb_url = str_replace( '=s220', '=w1024-h768-p-k-nu', $thumbnailLink );
                        } else {
                            if ( 'full' === $size ) {
                                $thumb_url = str_replace( '=s220', '', $thumbnailLink );
                            } else {
                                $thumb_url = str_replace( '=s220', '=w200-h190-p-k-nu', $thumbnailLink );
                            }
                        }
                    }
                }
            }
            wp_redirect( $thumb_url, 301 );
            exit;
        }
    }

    public function handle_authorization() {
        if ( empty( $_GET['action'] ) ) {
            return;
        }
        if ( 'authorization' == sanitize_key( $_GET['action'] ) ) {
            $client = Client::instance();
            $client->create_access_token();
            echo '<script type="text/javascript">window.opener.parent.location.reload(); window.close();</script>';
            exit;
        }
    }

    public function create_user_folder( $user_id ) {
        $allowed_user_roles = igd_get_settings( 'privateFolderRoles', ['editor', 'contributor', 'author'] );
        // Check if user role is allowed
        if ( !in_array( 'all', $allowed_user_roles ) ) {
            $user = get_user_by( 'id', $user_id );
            if ( !in_array( $user->roles[0], $allowed_user_roles ) ) {
                return;
            }
        }
        Private_Folders::instance()->create_user_folder( $user_id );
    }

    public function delete_user_folder( $user_id ) {
        Private_Folders::instance()->delete_user_folder( $user_id );
    }

    public function direct_content() {
        if ( $direct_file = get_query_var( 'direct_file' ) ) {
            $file = json_decode( base64_decode( $direct_file ), true );
            if ( empty( $file['permissions'] ) ) {
                $file['permissions'] = [];
            }
            $is_dir = igd_is_dir( $file );
            add_filter( 'show_admin_bar', '__return_false' );
            // Remove all WordPress actions
            remove_all_actions( 'wp_head' );
            remove_all_actions( 'wp_print_styles' );
            remove_all_actions( 'wp_print_head_scripts' );
            remove_all_actions( 'wp_footer' );
            // Handle `wp_head`
            add_action( 'wp_head', 'wp_enqueue_scripts', 1 );
            add_action( 'wp_head', 'wp_print_styles', 8 );
            add_action( 'wp_head', 'wp_print_head_scripts', 9 );
            add_action( 'wp_head', 'wp_site_icon' );
            // Handle `wp_footer`
            add_action( 'wp_footer', 'wp_print_footer_scripts', 20 );
            // Handle `wp_enqueue_scripts`
            remove_all_actions( 'wp_enqueue_scripts' );
            // Also remove all scripts hooked into after_wp_tiny_mce.
            remove_all_actions( 'after_wp_tiny_mce' );
            if ( $is_dir ) {
                Enqueue::instance()->frontend_scripts();
            }
            $type = ( $is_dir ? 'browser' : 'embed' );
            ?>

            <!doctype html>
            <html lang="<?php 
            language_attributes();
            ?>">
            <head>
                <meta charset="<?php 
            bloginfo( 'charset' );
            ?>">
                <meta name="viewport"
                      content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
                <meta http-equiv="X-UA-Compatible" content="ie=edge">
                <title><?php 
            echo esc_html( $file['name'] );
            ?></title>

				<?php 
            wp_enqueue_style( 'google-font-roboto', 'https://fonts.googleapis.com/css?family=Roboto:400,500,700&display=swap' );
            ?>

				<?php 
            do_action( 'wp_head' );
            ?>

				<?php 
            if ( 'embed' == $type ) {
                ?>
                    <style>
                        html, body {
                            margin: 0;
                            padding: 0;
                            width: 100%;
                            height: 100%;
                        }

                        #igd-direct-content {
                            width: 100%;
                            height: 100vh;
                            overflow: hidden;
                            position: relative;
                        }

                        #igd-direct-content .igd-embed {
                            width: 100%;
                            height: 100%;
                            border: none;
                        }
                    </style>
				<?php 
            }
            ?>

            </head>
            <body>

            <div id="igd-direct-content">
				<?php 
            $data = [
                'folders'            => [$file],
                'type'               => $type,
                'allowPreviewPopout' => false,
            ];
            echo Shortcode::instance()->render_shortcode( [], $data );
            ?>
            </div>

			<?php 
            do_action( 'wp_footer' );
            ?>

            </body>
            </html>

			<?php 
            exit;
        }
    }

    public function render_form_field_data( $data, $as_html ) {
        $uploaded_files = json_decode( $data, 1 );
        if ( empty( $uploaded_files ) ) {
            return $data;
        }
        $file_count = count( $uploaded_files );
        // Render TEXT only
        if ( !$as_html ) {
            $formatted_value = sprintf( _n(
                '%d file uploaded to Google Drive',
                '%d files uploaded to Google Drive',
                $file_count,
                'integrate-google-drive'
            ), $file_count );
            $formatted_value .= "\r\n";
            foreach ( $uploaded_files as $file ) {
                $view_link = sprintf( 'https://drive.google.com/file/d/%1$s/view', $file['id'] );
                $formatted_value .= $file['name'] . " - (" . $view_link . "), \r\n";
            }
            return $formatted_value;
        }
        $heading = sprintf( '<h3 style="margin-bottom: 15px;">%s</h3>', sprintf( 
            // translators: %d: number of files
            _n(
                '%d file uploaded to Google Drive',
                '%d files uploaded to Google Drive',
                $file_count,
                'integrate-google-drive'
            ),
            $file_count
         ) );
        // Render HTML
        ob_start();
        echo $heading;
        foreach ( $uploaded_files as $file ) {
            $file_url = sprintf( 'https://drive.google.com/file/d/%1$s/view', $file['id'] );
            ?>
            <div style="display: block; margin-bottom: 5px;font-weight: 600;">
				<?php 
            echo esc_html( $file['name'] );
            ?> -
                <a style="text-decoration: none;font-weight: 400;"
                   href="<?php 
            echo esc_url_raw( $file_url );
            ?>"
                   target="_blank"><?php 
            echo esc_url_raw( $file_url );
            ?></a>
            </div>
		<?php 
        }
        //Remove any newlines
        return trim( preg_replace( '/\\s+/', ' ', ob_get_clean() ) );
    }

    public function uninstall() {
        // Remove cron
        $timestamp = wp_next_scheduled( 'igd_sync_interval' );
        if ( $timestamp ) {
            wp_unschedule_event( $timestamp, 'igd_sync_interval' );
        }
        //Delete data
        if ( igd_get_settings( 'deleteData', false ) ) {
            delete_option( 'igd_tokens' );
            delete_option( 'igd_accounts' );
            delete_option( 'igd_settings' );
            delete_option( 'igd_cached_folders' );
            igd_delete_cache();
            // Clear Attachments
            Ajax::instance()->clear_attachments();
        }
    }

    /**
     * @return Hooks|null
     */
    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

}

Hooks::instance();
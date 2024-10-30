<?php

namespace IGD;

defined( 'ABSPATH' ) || exit;
class Ajax {
    private static $instance = null;

    public function __construct() {
        /*--- Admin Actions ---*/
        // Get Shortcodes
        add_action( 'wp_ajax_igd_get_shortcodes', [$this, 'get_shortcodes'] );
        // Update Shortcode
        add_action( 'wp_ajax_igd_update_shortcode', [$this, 'update_shortcode'] );
        // Duplicate Shortcode
        add_action( 'wp_ajax_igd_duplicate_shortcode', [$this, 'duplicate_shortcode'] );
        // Delete Shortcode
        add_action( 'wp_ajax_igd_delete_shortcode', [$this, 'delete_shortcode'] );
        // Get Embed Content
        add_action( 'wp_ajax_igd_get_embed_content', [$this, 'get_embed_content'] );
        // Get Shortcode Content
        add_action( 'wp_ajax_igd_get_shortcode_content', [$this, 'get_shortcode_content'] );
        // Clear cache files
        add_action( 'wp_ajax_igd_clear_cache', [$this, 'clear_cache'] );
        // Save Settings
        add_action( 'wp_ajax_igd_save_settings', [$this, 'save_settings'] );
        // Get Users Data
        add_action( 'wp_ajax_igd_get_users_data', [$this, 'get_users_data'] );
        // Update User Folders
        add_action( 'wp_ajax_igd_update_user_folders', [$this, 'update_user_folders'] );
        // Delete attachments
        add_action( 'wp_ajax_igd_media_clear_attachments', [$this, 'clear_attachments'] );
        // Get Export Data
        add_action( 'wp_ajax_igd_get_export_data', [$this, 'get_export_data'] );
        // Import Data
        add_action( 'wp_ajax_igd_import_data', [$this, 'import_data'] );
        // Hide Recommended Plugins
        add_action( 'wp_ajax_igd_hide_recommended_plugins', [$this, 'hide_recommended_plugins'] );
        // Handle admin  notice
        add_action( 'wp_ajax_igd_hide_review_notice', [$this, 'hide_review_notice'] );
        add_action( 'wp_ajax_igd_review_feedback', [$this, 'handle_review_feedback'] );
        // Delete Account
        add_action( 'wp_ajax_igd_delete_account', [$this, 'delete_account'] );
        // Reset Usage Limits
        add_action( 'wp_ajax_igd_reset_usage_limits', [$this, 'reset_current_usage'] );
        // Reset shortcode transient
        add_action( 'wp_ajax_igd_reset_shortcode_transient', [$this, 'reset_shortcode_transient'] );
        // Update storage size
        add_action( 'wp_ajax_igd_update_storage_size', [$this, 'update_storage_size'] );
        /*--- Frontend Actions ---*/
        // Search Files
        add_action( 'wp_ajax_igd_search_files', [$this, 'search_files'] );
        add_action( 'wp_ajax_nopriv_igd_search_files', [$this, 'search_files'] );
        // Get File
        add_action( 'wp_ajax_igd_get_file', [$this, 'get_file'] );
        add_action( 'wp_ajax_nopriv_igd_get_file', [$this, 'get_file'] );
        // Delete Files
        add_action( 'wp_ajax_igd_delete_files', [$this, 'delete_files'] );
        add_action( 'wp_ajax_nopriv_igd_delete_files', [$this, 'delete_files'] );
        // Preview Content
        add_action( 'wp_ajax_igd_preview', [$this, 'preview'] );
        add_action( 'wp_ajax_nopriv_igd_preview', [$this, 'preview'] );
        // Get share URL
        add_action( 'wp_ajax_igd_get_share_link', [$this, 'get_share_link'] );
        add_action( 'wp_ajax_nopriv_igd_get_share_link', [$this, 'get_share_link'] );
        // Download file
        add_action( 'wp_ajax_igd_download', [$this, 'download'] );
        add_action( 'wp_ajax_nopriv_igd_download', [$this, 'download'] );
        // Get download status
        add_action( 'wp_ajax_igd_download_status', [$this, 'get_download_status'] );
        add_action( 'wp_ajax_nopriv_igd_download_status', [$this, 'get_download_status'] );
        // Get upload direct url
        add_action( 'wp_ajax_igd_get_upload_url', [$this, 'get_upload_url'] );
        add_action( 'wp_ajax_nopriv_igd_get_upload_url', [$this, 'get_upload_url'] );
        // Stream
        add_action( 'wp_ajax_igd_stream', [$this, 'stream_content'] );
        add_action( 'wp_ajax_nopriv_igd_stream', [$this, 'stream_content'] );
        // Get Files
        add_action( 'wp_ajax_igd_get_files', [$this, 'get_files'] );
        add_action( 'wp_ajax_nopriv_igd_get_files', [$this, 'get_files'] );
        // Remove uploaded files
        add_action( 'wp_ajax_igd_upload_remove_file', [$this, 'remove_upload_file'] );
        add_action( 'wp_ajax_nopriv_igd_upload_remove_file', [$this, 'remove_upload_file'] );
        // Upload post process
        add_action( 'wp_ajax_igd_file_uploaded', [$this, 'upload_post_process'] );
        add_action( 'wp_ajax_nopriv_igd_file_uploaded', [$this, 'upload_post_process'] );
        // Move File
        add_action( 'wp_ajax_igd_move_file', [$this, 'move_file'] );
        add_action( 'wp_ajax_nopriv_igd_move_file', [$this, 'move_file'] );
        // Rename File
        add_action( 'wp_ajax_igd_rename_file', [$this, 'rename_file'] );
        add_action( 'wp_ajax_nopriv_igd_rename_file', [$this, 'rename_file'] );
        // Copy Files
        add_action( 'wp_ajax_igd_copy_file', [$this, 'copy_file'] );
        add_action( 'wp_ajax_nopriv_igd_copy_file', [$this, 'copy_file'] );
        // New Folder
        add_action( 'wp_ajax_igd_new_folder', [$this, 'new_folder'] );
        add_action( 'wp_ajax_nopriv_igd_new_folder', [$this, 'new_folder'] );
        // Switch Account
        add_action( 'wp_ajax_igd_switch_account', [$this, 'switch_account'] );
        add_action( 'wp_ajax_nopriv_igd_switch_account', [$this, 'switch_account'] );
        // Create Doc
        add_action( 'wp_ajax_igd_create_doc', [$this, 'create_doc'] );
        add_action( 'wp_ajax_nopriv_igd_create_doc', [$this, 'create_doc'] );
        // Update Description
        add_action( 'wp_ajax_igd_update_description', [$this, 'update_description'] );
        add_action( 'wp_ajax_nopriv_igd_update_description', [$this, 'update_description'] );
    }

    public function get_shortcodes() {
        // Check nonce
        if ( !check_ajax_referer( 'igd', 'nonce', false ) ) {
            wp_send_json_error( __( 'Invalid request', 'integrate-google-drive' ) );
        }
        // Check permission
        if ( !igd_user_can_access( 'shortcode_builder' ) ) {
            wp_send_json_error( __( 'You do not have permission to access this page', 'integrate-google-drive' ) );
        }
        $page = ( !empty( $_POST['page'] ) ? intval( $_POST['page'] ) : 1 );
        $per_page = ( !empty( $_POST['per_page'] ) ? intval( $_POST['per_page'] ) : 10 );
        $order_by = ( !empty( $_POST['sort_by'] ) ? sanitize_text_field( $_POST['sort_by'] ) : 'created_at' );
        $order = ( !empty( $_POST['sort_order'] ) ? sanitize_text_field( $_POST['sort_order'] ) : 'desc' );
        $args = [];
        $args['offset'] = 10 * ($page - 1);
        $args['limit'] = $per_page;
        $args['order_by'] = $order_by;
        $args['order'] = $order;
        $shortcodes = Shortcode::get_shortcodes( $args );
        $return_data = [];
        $formatted = [];
        if ( !empty( $shortcodes ) ) {
            foreach ( $shortcodes as $shortcode ) {
                $shortcode->config = maybe_unserialize( $shortcode->config );
                $shortcode->locations = ( !empty( $shortcode->locations ) ? array_values( maybe_unserialize( $shortcode->locations ) ) : [] );
                $formatted[] = $shortcode;
            }
        }
        $return_data['shortcodes'] = $formatted;
        $return_data['total'] = Shortcode::get_shortcodes_count();
        wp_send_json_success( $return_data );
    }

    public function delete_shortcode() {
        // Check nonce
        if ( !check_ajax_referer( 'igd', 'nonce', false ) ) {
            wp_send_json_error( __( 'Invalid request', 'integrate-google-drive' ) );
        }
        // Check permission
        if ( !igd_user_can_access( 'shortcode_builder' ) ) {
            wp_send_json_error( __( 'You do not have permission to access this page', 'integrate-google-drive' ) );
        }
        $id = ( !empty( $_POST['id'] ) ? intval( $_POST['id'] ) : '' );
        Shortcode::delete_shortcode( $id );
        wp_send_json_success();
    }

    public function get_embed_content() {
        // Check nonce
        if ( !check_ajax_referer( 'igd', 'nonce', false ) ) {
            wp_send_json_error( __( 'Invalid request', 'integrate-google-drive' ) );
        }
        // Check permission
        if ( !igd_user_can_access( 'shortcode_builder' ) ) {
            wp_send_json_error( __( 'You do not have permission to perform this action.', 'integrate-google-drive' ) );
        }
        $data = ( !empty( $_POST['data'] ) ? igd_sanitize_array( $_POST['data'] ) : [] );
        $content = igd_get_embed_content( $data );
        wp_send_json_success( $content );
    }

    public function get_shortcode_content() {
        // Check nonce
        if ( !check_ajax_referer( 'igd', 'nonce', false ) ) {
            wp_send_json_error( __( 'Invalid request', 'integrate-google-drive' ) );
        }
        // Check permission
        if ( !igd_user_can_access( 'shortcode_builder' ) ) {
            wp_send_json_error( __( 'You do not have permission to perform this action.', 'integrate-google-drive' ) );
        }
        $data = ( !empty( $_POST['data'] ) ? igd_sanitize_array( $_POST['data'] ) : [] );
        Shortcode::reset_shortcode_transients( $data );
        $html = Shortcode::instance()->render_shortcode( [], $data );
        wp_send_json_success( $html );
    }

    public function hide_recommended_plugins() {
        // Check nonce
        if ( !check_ajax_referer( 'igd', 'nonce', false ) ) {
            wp_send_json_error( __( 'Invalid request', 'integrate-google-drive' ) );
        }
        // Check permission
        if ( !current_user_can( 'manage_options' ) ) {
            wp_send_json_error( __( 'You do not have permission to perform this action', 'integrate-google-drive' ) );
        }
        update_option( "igd_hide_recommended_plugins", true );
        wp_send_json_success();
    }

    public function hide_review_notice() {
        // Check nonce
        if ( !check_ajax_referer( 'igd', 'nonce', false ) ) {
            wp_send_json_error( __( 'Invalid request', 'integrate-google-drive' ) );
        }
        // Check permission
        if ( !current_user_can( 'manage_options' ) ) {
            wp_send_json_error( __( 'You do not have permission to perform this action', 'integrate-google-drive' ) );
        }
        update_option( 'igd_rating_notice', 'off' );
        wp_send_json_success();
    }

    public function handle_review_feedback() {
        // Check nonce
        if ( !check_ajax_referer( 'igd', 'nonce', false ) ) {
            wp_send_json_error( __( 'Invalid request', 'integrate-google-drive' ) );
        }
        // Check permission
        if ( !current_user_can( 'manage_options' ) ) {
            wp_send_json_error( __( 'You do not have permission to perform this action', 'integrate-google-drive' ) );
        }
        $feedback = ( !empty( $_POST['feedback'] ) ? sanitize_textarea_field( $_POST['feedback'] ) : '' );
        if ( !empty( $feedback ) ) {
            $feedback = sanitize_textarea_field( $feedback );
            $website_url = get_bloginfo( 'url' );
            /* translators: %s: User feedback */
            $feedback = sprintf( __( 'Feedback: %s', 'integrate-google-drive' ), $feedback );
            $feedback .= '<br>';
            /* translators: %s: Website URL */
            $feedback .= sprintf( __( 'Website URL: %s', 'integrate-google-drive' ), $website_url );
            /* translators: %s: Plugin name */
            $subject = sprintf( __( 'Feedback for %s', 'integrate-google-drive' ), 'Integrate Google Drive' );
            $to = 'israilahmed5@gmail.com';
            $headers = ['Content-Type: text/html; charset=UTF-8', 'From: ' . get_bloginfo( 'name' ) . ' <' . get_bloginfo( 'admin_email' ) . '>'];
            wp_mail(
                $to,
                $subject,
                $feedback,
                $headers
            );
            update_option( 'igd_rating_notice', 'off' );
            wp_send_json_success();
        } else {
            wp_send_json_error();
        }
    }

    public function duplicate_shortcode() {
        // Check nonce
        if ( !check_ajax_referer( 'igd', 'nonce', false ) ) {
            wp_send_json_error( __( 'Invalid request', 'integrate-google-drive' ) );
        }
        // Check permission
        if ( !igd_user_can_access( 'shortcode_builder' ) ) {
            wp_send_json_error( __( 'You do not have permission to access this page', 'integrate-google-drive' ) );
        }
        $ids = ( !empty( $_POST['ids'] ) ? igd_sanitize_array( $_POST['ids'] ) : [] );
        $data = [];
        if ( !empty( $ids ) ) {
            foreach ( $ids as $id ) {
                $data[] = Shortcode::duplicate_shortcode( $id );
            }
        }
        wp_send_json_success( $data );
    }

    public function update_shortcode() {
        // Check nonce
        if ( !check_ajax_referer( 'igd', 'nonce', false ) ) {
            wp_send_json_error( __( 'Invalid request', 'integrate-google-drive' ) );
        }
        // Check permission
        if ( !igd_user_can_access( 'shortcode_builder' ) ) {
            wp_send_json_error( __( 'You do not have permission to access this page', 'integrate-google-drive' ) );
        }
        $data = ( !empty( $_POST['data'] ) ? json_decode( base64_decode( $_POST['data'] ), true ) : [] );
        $id = Shortcode::update_shortcode( $data );
        $data = [
            'id'         => $id,
            'config'     => $data,
            'title'      => $data['title'],
            'status'     => $data['status'],
            'created_at' => ( !empty( $data['created_at'] ) ? $data['created_at'] : date( 'Y-m-d H:i:s', time() ) ),
        ];
        wp_send_json_success( $data );
    }

    public function get_users_data() {
        // Check nonce
        if ( !check_ajax_referer( 'igd', 'nonce', false ) ) {
            wp_send_json_error( __( 'Invalid request', 'integrate-google-drive' ) );
        }
        // Check permission
        if ( !igd_user_can_access( 'shortcode_builder' ) ) {
            wp_send_json_error( __( 'You do not have permission to access this page', 'integrate-google-drive' ) );
        }
        $search = ( !empty( $_POST['search'] ) ? sanitize_text_field( $_POST['search'] ) : '' );
        $role = ( !empty( $_POST['role'] ) ? sanitize_text_field( $_POST['role'] ) : '' );
        $page = ( !empty( $_POST['page'] ) ? intval( $_POST['page'] ) : 1 );
        $number = ( !empty( $_POST['number'] ) ? intval( $_POST['number'] ) : 999 );
        $offset = 10 * ($page - 1);
        $args = [
            'number' => $number,
            'role'   => ( 'all' != $role ? $role : '' ),
            'offset' => $offset,
            'search' => ( !empty( $search ) ? "*{$search}*" : '' ),
        ];
        $user_data = Private_Folders::instance()->get_user_data( $args );
        wp_send_json_success( $user_data );
    }

    public function update_user_folders() {
        // Check nonce
        if ( !check_ajax_referer( 'igd', 'nonce', false ) ) {
            wp_send_json_error( __( 'Invalid request', 'integrate-google-drive' ) );
        }
        // Check performance
        if ( !igd_user_can_access( 'shortcode_builder' ) ) {
            wp_send_json_error( __( 'You do not have permission to access this page', 'integrate-google-drive' ) );
        }
        $user_id = ( !empty( $_POST['id'] ) ? intval( $_POST['id'] ) : 0 );
        $folders = ( !empty( $_POST['folders'] ) ? igd_sanitize_array( $_POST['folders'] ) : [] );
        update_user_meta( $user_id, 'igd_folders', $folders );
        wp_send_json_success();
    }

    public function clear_cache() {
        // Check nonce
        if ( !check_ajax_referer( 'igd', 'nonce', false ) ) {
            wp_send_json_error( __( 'Invalid request', 'integrate-google-drive' ) );
        }
        // Check permission
        if ( !current_user_can( 'manage_options' ) ) {
            wp_send_json_error( __( 'You do not have permission to perform this action', 'integrate-google-drive' ) );
        }
        igd_delete_cache();
        wp_send_json_success();
    }

    public function update_storage_size() {
        // Check nonce
        if ( !check_ajax_referer( 'igd', 'nonce', false ) ) {
            wp_send_json_error( __( 'Invalid request', 'integrate-google-drive' ) );
        }
        $usage = Account::instance()->get_storage_usage();
        wp_send_json_success( $usage );
    }

    /**
     * Clear Google Drive Inserted Attachments from Media Library
     *
     * @return void
     */
    public function clear_attachments() {
        // Check permission
        if ( !current_user_can( 'manage_options' ) ) {
            wp_send_json_error( __( 'You do not have permission to access this page', 'integrate-google-drive' ) );
        }
        global $wpdb;
        $wpdb->query( "\n\t\t\t    DELETE p, pm\n\t\t\t    FROM {$wpdb->posts} p\n\t\t\t    JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id\n\t\t\t    WHERE p.ID IN (\n\t\t\t        SELECT * FROM (\n\t\t\t            SELECT pm1.post_id\n\t\t\t            FROM {$wpdb->postmeta} pm1\n\t\t\t            INNER JOIN {$wpdb->postmeta} pm2 ON pm1.post_id = pm2.post_id\n\t\t\t            WHERE pm1.meta_key = '_igd_media_folder_id' AND pm1.meta_value IS NOT NULL\n\t\t\t        ) AS temp_table\n\t\t\t    )\n\t\t\t" );
        // Delete cached folders option
        delete_option( 'igd_media_inserted_folders' );
        wp_send_json_success();
    }

    public function get_export_data() {
        // Check nonce
        if ( !check_ajax_referer( 'igd', 'nonce', false ) ) {
            wp_send_json_error( __( 'Invalid request', 'integrate-google-drive' ) );
        }
        // Check permission
        if ( !current_user_can( 'manage_options' ) ) {
            wp_send_json_error( __( 'You do not have permission to access this page', 'integrate-google-drive' ) );
        }
        $type = ( !empty( $_POST['$type'] ) ? sanitize_text_field( $_POST['$type'] ) : 'all' );
        $export_data = array();
        // Settings
        if ( 'all' == $type || 'settings' == $type ) {
            $export_data['settings'] = igd_get_settings();
        }
        // Shortcodes
        if ( 'all' == $type || 'shortcodes' == $type ) {
            $export_data['shortcodes'] = Shortcode::get_shortcodes();
        }
        // User Private Files
        if ( 'all' == $type || 'user_files' == $type ) {
            $user_files = array();
            $users = get_users();
            foreach ( $users as $user ) {
                $folders = get_user_meta( $user->ID, 'igd_folders', true );
                $user_files[$user->ID] = ( !empty( $folders ) ? $folders : array() );
            }
            $export_data['user_files'] = $user_files;
        }
        wp_send_json_success( $export_data );
    }

    public function import_data() {
        // Check nonce
        if ( !check_ajax_referer( 'igd', 'nonce', false ) ) {
            wp_send_json_error( __( 'Invalid request', 'integrate-google-drive' ) );
        }
        // Check permission
        if ( !current_user_can( 'manage_options' ) ) {
            wp_send_json_error( __( 'You do not have permission to access this page', 'integrate-google-drive' ) );
        }
        $json_string = ( !empty( $_POST['data'] ) ? stripslashes( $_POST['data'] ) : [] );
        $json_decoded = json_decode( $json_string, 1 );
        $return_data = [];
        foreach ( $json_decoded as $key => $data ) {
            if ( empty( $data ) ) {
                continue;
            }
            if ( 'settings' == $key ) {
                update_option( 'igd_settings', $data );
                $return_data['settings'] = $data;
            }
            if ( 'shortcodes' == $key ) {
                Shortcode::delete_shortcode();
                foreach ( $data as $shortcode ) {
                    Shortcode::update_shortcode( $shortcode, true );
                }
            }
            if ( 'user_files' == $key ) {
                foreach ( $data as $user_id => $files ) {
                    update_user_meta( $user_id, 'igd_folders', $files );
                }
            }
        }
        wp_send_json_success( $return_data );
    }

    public function save_settings() {
        // Check nonce
        if ( !check_ajax_referer( 'igd', 'nonce', false ) ) {
            wp_send_json_error( __( 'Invalid request', 'integrate-google-drive' ) );
        }
        // Check permission
        if ( !igd_user_can_access( 'settings' ) ) {
            wp_send_json_error( __( 'You do not have permission to access this page', 'integrate-google-drive' ) );
        }
        $settings = ( !empty( $_POST['settings'] ) ? json_decode( base64_decode( $_POST['settings'] ), true ) : [] );
        update_option( 'igd_settings', $settings );
        wp_send_json_success();
    }

    public function delete_account() {
        // Check nonce
        if ( !check_ajax_referer( 'igd', 'nonce', false ) ) {
            wp_send_json_error( __( 'Invalid request', 'integrate-google-drive' ) );
        }
        // Check permission
        if ( !igd_user_can_access( 'shortcode_builder' ) ) {
            wp_send_json_error( __( 'You do not have permission to access this page', 'integrate-google-drive' ) );
        }
        $id = ( !empty( $_POST['id'] ) ? sanitize_text_field( $_POST['id'] ) : '' );
        Account::instance()->delete_account( $id );
        wp_send_json_success();
    }

    public function reset_current_usage() {
        // Check nonce
        if ( !check_ajax_referer( 'igd', 'nonce', false ) ) {
            wp_send_json_error( __( 'Invalid request', 'integrate-google-drive' ) );
        }
        // Check permission
        if ( !igd_user_can_access( 'settings' ) ) {
            wp_send_json_error( __( 'You do not have permission to perform this action', 'integrate-google-drive' ) );
        }
        // Delete usage for all users
        delete_metadata(
            'user',
            null,
            'igd_usage_limits',
            null,
            true
        );
        // Delete usage for all not logged in users
        igd_delete_transients_with_prefix( 'igd_usage_limits_' );
        wp_send_json_success();
    }

    public function reset_shortcode_transient() {
        // Check nonce
        if ( !check_ajax_referer( 'igd', 'nonce', false ) ) {
            wp_send_json_error( __( 'Invalid request', 'integrate-google-drive' ) );
        }
        // Check permission
        if ( !igd_user_can_access( 'shortcode_builder' ) ) {
            wp_send_json_error( __( 'You do not have permission to perform this action', 'integrate-google-drive' ) );
        }
        if ( empty( $_POST['data'] ) ) {
            wp_send_json_error( __( 'Invalid shortcode data', 'integrate-google-drive' ) );
        }
        $data = igd_sanitize_array( $_POST['data'] );
        Shortcode::reset_shortcode_transients( $data );
        wp_send_json_success();
    }

    /**
     * Get the Google Drive files and folders
     *
     * @return void
     */
    public function get_files() {
        // Check nonce
        $this->check_nonce();
        // Set current shortcode data
        $this->set_current_shortcode_data();
        $posted = ( !empty( $_POST['data'] ) ? igd_sanitize_array( $_POST['data'] ) : [] );
        $active_account_id = igd_get_active_account_id();
        if ( empty( $active_account_id ) ) {
            wp_send_json_error( __( 'No active account found', 'integrate-google-drive' ) );
        }
        // Check permission
        if ( !Shortcode::can_do( 'get_files', $posted ) ) {
            wp_send_json_error( __( 'You do not have permission to perform this action', 'integrate-google-drive' ) );
        }
        $args = [
            'folder'      => [
                'id'         => 'root',
                'accountId'  => $active_account_id,
                'pageNumber' => 1,
            ],
            'sort'        => [
                'sortBy'        => 'name',
                'sortDirection' => 'asc',
            ],
            'from_server' => false,
            'filters'     => [],
        ];
        // Merge request params
        $args = wp_parse_args( $posted, $args );
        $account_id = ( !empty( $folder['accountId'] ) ? $folder['accountId'] : $active_account_id );
        $refresh = !empty( $args['refresh'] );
        $folder = $args['folder'];
        if ( !empty( $args['from_server'] ) ) {
            $transient = get_transient( 'igd_latest_fetch_' . $folder['id'] );
            if ( $transient ) {
                $args['from_server'] = false;
            } else {
                set_transient( 'igd_latest_fetch_' . $folder['id'], true, 60 * MINUTE_IN_SECONDS );
            }
        }
        // Check if shortcut folder
        if ( !empty( $folder['shortcutDetails'] ) ) {
            $args['folder']['id'] = $folder['shortcutDetails']['targetId'];
        }
        // Reset cache and get new files
        if ( $refresh ) {
            $refresh_args = $args;
            $refresh_args['folder']['pageNumber'] = 1;
            $refresh_args['from_server'] = true;
            igd_delete_cache( [$folder['id']] );
            $data = App::instance( $account_id )->get_files( $refresh_args );
        } else {
            $data = App::instance( $account_id )->get_files( $args );
        }
        if ( !empty( $data['error'] ) ) {
            wp_send_json_success( $data );
        }
        // Get breadcrumbs
        if ( empty( $folder['pageNumber'] ) || $folder['pageNumber'] == 1 ) {
            $data['breadcrumbs'] = igd_get_breadcrumb( $folder );
        }
        wp_send_json_success( $data );
    }

    public function search_files() {
        // Check nonce
        $this->check_nonce();
        // Set current shortcode data
        $this->set_current_shortcode_data();
        // Check permission
        if ( !Shortcode::can_do( 'search_files' ) ) {
            wp_send_json_error( __( 'You do not have permission to perform this action', 'integrate-google-drive' ) );
        }
        // Get posted data
        $folders = ( !empty( $_POST['folders'] ) ? igd_sanitize_array( $_POST['folders'] ) : [] );
        $keyword = ( !empty( $_POST['keyword'] ) ? sanitize_text_field( $_POST['keyword'] ) : '' );
        $account_id = ( !empty( $_POST['accountId'] ) ? sanitize_text_field( $_POST['accountId'] ) : '' );
        $sort = ( !empty( $_POST['sort'] ) ? igd_sanitize_array( $_POST['sort'] ) : [] );
        $full_text_search = ( isset( $_POST['fullTextSearch'] ) ? filter_var( $_POST['fullTextSearch'], FILTER_VALIDATE_BOOLEAN ) : true );
        $files = App::instance( $account_id )->get_search_files(
            $keyword,
            $folders,
            $sort,
            $full_text_search
        );
        if ( !empty( $files['error'] ) ) {
            wp_send_json_success( $files );
        }
        $data = [
            'files' => array_values( $files ),
        ];
        wp_send_json_success( $data );
    }

    public function get_file() {
        // Check nonce
        $this->check_nonce();
        // Set current shortcode data
        $this->set_current_shortcode_data();
        // Check permission
        if ( !Shortcode::can_do( 'get_file' ) ) {
            wp_send_json_error( __( 'You do not have permission to perform this action', 'integrate-google-drive' ) );
        }
        $file_id = ( !empty( $_POST['id'] ) ? sanitize_text_field( $_POST['id'] ) : '' );
        $account_id = ( !empty( $_POST['accountId'] ) ? sanitize_text_field( $_POST['accountId'] ) : '' );
        $file = App::instance( $account_id )->get_file_by_id( $file_id );
        wp_send_json_success( $file );
    }

    public function delete_files() {
        // Check nonce
        $this->check_nonce();
        // Set current shortcode data
        $this->set_current_shortcode_data();
        // Check permission
        if ( !Shortcode::can_do( 'delete_files' ) ) {
            wp_send_json_error( __( 'You do not have permission to perform this action', 'integrate-google-drive' ) );
        }
        $file_ids = ( !empty( $_POST['file_ids'] ) ? igd_sanitize_array( $_POST['file_ids'] ) : [] );
        $account_id = ( !empty( $_POST['account_id'] ) ? sanitize_text_field( $_POST['account_id'] ) : '' );
        // Send email notification
        if ( igd_get_settings( 'deleteNotifications', true ) ) {
            do_action(
                'igd_send_notification',
                'delete',
                $file_ids,
                $account_id
            );
        }
        wp_send_json_success( App::instance( $account_id )->delete( $file_ids ) );
    }

    public function new_folder() {
        // Check nonce
        $this->check_nonce();
        // Set current shortcode data
        $this->set_current_shortcode_data();
        // Check permission
        if ( !Shortcode::can_do( 'new_folder' ) ) {
            wp_send_json_error( __( 'You do not have permission to perform this action', 'integrate-google-drive' ) );
        }
        $folder_name = ( !empty( $_POST['name'] ) ? sanitize_text_field( $_POST['name'] ) : '' );
        $parent_id = ( !empty( $_POST['parent_id'] ) ? sanitize_text_field( $_POST['parent_id'] ) : '' );
        $account_id = ( !empty( $_POST['account_id'] ) ? sanitize_text_field( $_POST['account_id'] ) : '' );
        $new_folder = App::instance( $account_id )->new_folder( $folder_name, $parent_id );
        wp_send_json_success( $new_folder );
    }

    public function create_doc() {
        // Check nonce
        $this->check_nonce();
        // Set current shortcode data
        $this->set_current_shortcode_data();
        // Check permission
        if ( !Shortcode::can_do( 'create_doc' ) ) {
            wp_send_json_error( __( 'You do not have permission to perform this action', 'integrate-google-drive' ) );
        }
        $name = ( !empty( $_POST['name'] ) ? sanitize_text_field( $_POST['name'] ) : 'Untitled' );
        $type = ( !empty( $_POST['type'] ) ? sanitize_text_field( $_POST['type'] ) : 'doc' );
        $folder_id = ( !empty( $_POST['folder_id'] ) ? sanitize_text_field( $_POST['folder_id'] ) : 'root' );
        $account_id = ( !empty( $_POST['account_id'] ) ? sanitize_text_field( $_POST['account_id'] ) : '' );
        $mime_type = 'application/vnd.google-apps.document';
        if ( $type == 'sheet' ) {
            $mime_type = 'application/vnd.google-apps.spreadsheet';
        } elseif ( $type == 'slide' ) {
            $mime_type = 'application/vnd.google-apps.presentation';
        }
        try {
            $item = App::instance( $account_id )->getService()->files->create( new \IGDGoogle_Service_Drive_DriveFile([
                'name'     => $name,
                'mimeType' => $mime_type,
                'parents'  => [$folder_id],
            ]), [
                'fields'            => '*',
                'supportsAllDrives' => true,
            ] );
            // add new folder to cache
            $file = igd_file_map( $item, $account_id );
            // Insert log
            do_action(
                'igd_insert_log',
                'create',
                $file['id'],
                $account_id
            );
            wp_send_json_success( $file );
        } catch ( \Exception $e ) {
            wp_send_json_error( array(
                'error' => $e->getMessage(),
            ) );
        }
    }

    public function copy_file() {
        // Check nonce
        $this->check_nonce();
        // Set current shortcode data
        $this->set_current_shortcode_data();
        // Check permission
        if ( !Shortcode::can_do( 'move_copy' ) ) {
            wp_send_json_error( __( 'You do not have permission to perform this action', 'integrate-google-drive' ) );
        }
        $files = ( !empty( $_POST['files'] ) ? igd_sanitize_array( $_POST['files'] ) : [] );
        $folder_id = ( !empty( $_POST['folder_id'] ) ? sanitize_text_field( $_POST['folder_id'] ) : '' );
        $account_id = ( !empty( $files[0]['accountId'] ) ? sanitize_text_field( $files[0]['accountId'] ) : '' );
        $copied_files = App::instance( $account_id )->copy( $files, $folder_id );
        wp_send_json_success( $copied_files );
    }

    public function move_file() {
        // Check nonce
        $this->check_nonce();
        // Set current shortcode data
        $this->set_current_shortcode_data();
        // Check permission
        if ( !Shortcode::can_do( 'move_copy' ) ) {
            wp_send_json_error( __( 'You do not have permission to perform this action', 'integrate-google-drive' ) );
        }
        $file_ids = ( !empty( $_POST['file_ids'] ) ? igd_sanitize_array( $_POST['file_ids'] ) : '' );
        $folder_id = ( !empty( $_POST['folder_id'] ) ? sanitize_text_field( $_POST['folder_id'] ) : '' );
        $account_id = ( !empty( $_POST['account_id'] ) ? sanitize_text_field( $_POST['account_id'] ) : '' );
        wp_send_json_success( App::instance( $account_id )->move_file( $file_ids, $folder_id ) );
    }

    public function rename_file() {
        // Check nonce
        $this->check_nonce();
        // Set current shortcode data
        $this->set_current_shortcode_data();
        // Check permission
        if ( !Shortcode::can_do( 'rename' ) ) {
            wp_send_json_error( __( 'You do not have permission to perform this action', 'integrate-google-drive' ) );
        }
        $name = ( !empty( $_POST['name'] ) ? sanitize_text_field( $_POST['name'] ) : '' );
        $file_id = ( !empty( $_POST['id'] ) ? sanitize_text_field( $_POST['id'] ) : '' );
        $account_id = ( !empty( $_POST['accountId'] ) ? sanitize_text_field( $_POST['accountId'] ) : '' );
        wp_send_json_success( App::instance( $account_id )->rename( $name, $file_id ) );
    }

    public function switch_account() {
        // Check nonce
        $this->check_nonce();
        // set current shortcode data
        $this->set_current_shortcode_data();
        // Check permission
        if ( !igd_user_can_access( 'switch_account' ) && !Shortcode::can_do( 'switch_account' ) ) {
            wp_send_json_error( __( 'You do not have permission to access this page', 'integrate-google-drive' ) );
        }
        $id = ( !empty( $_POST['id'] ) ? sanitize_text_field( $_POST['id'] ) : '' );
        Account::instance()->set_active_account_id( $id );
        wp_send_json_success();
    }

    public function preview() {
        // Check nonce
        $this->check_nonce();
        // Set current shortcode data
        $this->set_current_shortcode_data();
        // Check permission
        if ( !Shortcode::can_do( 'preview' ) ) {
            wp_send_json_error( __( 'You do not have permission to perform this action', 'integrate-google-drive' ) );
        }
        $file_id = sanitize_text_field( $_REQUEST['file_id'] );
        $account_id = sanitize_text_field( $_REQUEST['account_id'] );
        $popout = true;
        if ( !empty( $_REQUEST['popout'] ) ) {
            $popout = filter_var( $_REQUEST['popout'], FILTER_VALIDATE_BOOLEAN );
        }
        if ( !empty( $_REQUEST['direct_link'] ) ) {
            $popout = false;
        }
        $download = true;
        if ( !empty( $_REQUEST['download'] ) ) {
            $download = filter_var( $_REQUEST['download'], FILTER_VALIDATE_BOOLEAN );
        }
        $app = App::instance( $account_id );
        $file = $app->get_file_by_id( $file_id );
        $preview_url = igd_get_embed_url(
            $file,
            false,
            false,
            true,
            $popout,
            $download
        );
        if ( !$preview_url ) {
            _e( 'Something went wrong! Preview is not available', 'integrate-google-drive' );
            die;
        }
        do_action(
            'igd_insert_log',
            'preview',
            $file_id,
            $account_id
        );
        wp_redirect( $preview_url );
        die;
    }

    public function remove_upload_file() {
        // Check nonce
        $this->check_nonce();
        // Set current shortcode data
        $this->set_current_shortcode_data();
        // Check permission
        if ( !Shortcode::can_do( 'upload' ) ) {
            wp_send_json_error( __( 'You do not have permission to perform this action', 'integrate-google-drive' ) );
        }
        $id = ( !empty( $_POST['id'] ) ? $_POST['id'] : '' );
        $account_id = ( !empty( $_POST['account_id'] ) ? $_POST['account_id'] : '' );
        $is_woocommerce = ( !empty( $_POST['isWooCommerceUploader'] ) ? filter_var( $_POST['isWooCommerceUploader'], FILTER_VALIDATE_BOOLEAN ) : false );
        $product_id = ( !empty( $_POST['wcProductId'] ) ? sanitize_text_field( $_POST['wcProductId'] ) : 0 );
        $item_id = ( !empty( $_POST['wcItemId'] ) ? intval( $_POST['wcItemId'] ) : 0 );
        // Remove uploaded files from Google Drive
        App::instance( $account_id )->delete( [$id] );
        // Remove uploaded files from woocommerce order meta-data
        if ( $is_woocommerce ) {
            if ( $item_id ) {
                $files = array_filter( wc_get_order_item_meta( $item_id, '_igd_files', false ) );
                if ( !empty( $files ) ) {
                    foreach ( $files as $key => $file ) {
                        if ( $file['id'] === $id ) {
                            unset($files[$key]);
                        }
                    }
                    wc_update_order_item_meta( $item_id, '_igd_files', $files );
                }
            } else {
                //Remove uploaded files from wc session
                $files = WC()->session->get( 'igd_product_files_' . $product_id, [] );
                if ( !empty( $files ) ) {
                    foreach ( $files as $key => $file ) {
                        if ( $file['id'] === $id ) {
                            unset($files[$key]);
                        }
                    }
                    WC()->session->set( 'igd_product_files_' . $product_id, $files );
                }
            }
        }
        wp_send_json_success( [
            'success' => true,
        ] );
    }

    public function upload_post_process() {
        // Check nonce
        $this->check_nonce();
        // Set current shortcode data
        $this->set_current_shortcode_data();
        // Check permission
        if ( !Shortcode::can_do( 'upload' ) ) {
            wp_send_json_error( __( 'You do not have permission to perform this action', 'integrate-google-drive' ) );
        }
        $file = ( !empty( $_POST['file'] ) ? igd_sanitize_array( $_POST['file'] ) : [] );
        $account_id = ( !empty( $file['accountId'] ) ? sanitize_text_field( $file['accountId'] ) : '' );
        $formatted_file = Uploader::instance( $account_id )->upload_post_process( $file );
        // Save uploaded files in the order meta-data for order-received page and my-account page
        $item_id = ( !empty( $_POST['wcItemId'] ) ? intval( $_POST['wcItemId'] ) : false );
        $product_id = ( !empty( $_POST['wcProductId'] ) ? intval( $_POST['wcProductId'] ) : false );
        if ( $item_id ) {
            if ( function_exists( 'wc_add_order_item_meta' ) ) {
                wc_add_order_item_meta( $item_id, '_igd_files', $file );
            }
        } elseif ( $product_id ) {
            // Save uploaded files in the session for checkout page
            if ( function_exists( 'WC' ) ) {
                if ( !WC()->session->has_session() ) {
                    WC()->session->set_customer_session_cookie( true );
                }
                $files = WC()->session->get( 'igd_product_files_' . $product_id, [] );
                $files[] = $file;
                WC()->session->set( 'igd_product_files_' . $product_id, $files );
            }
        }
        do_action(
            'igd_insert_log',
            'upload',
            $formatted_file['id'],
            $account_id
        );
        do_action( 'igd_upload_post_process', $formatted_file, $account_id );
        wp_send_json_success( $formatted_file );
    }

    public function get_upload_url() {
        // Check nonce
        $this->check_nonce();
        // Set current shortcode data
        $this->set_current_shortcode_data();
        // Check permission
        if ( !Shortcode::can_do( 'upload' ) ) {
            wp_send_json_error( __( 'You do not have permission to perform this action', 'integrate-google-drive' ) );
        }
        $data = ( !empty( $_POST['data'] ) ? igd_sanitize_array( $_POST['data'] ) : [] );
        $account_id = ( !empty( $data['accountId'] ) ? sanitize_text_field( $data['accountId'] ) : '' );
        $url = Uploader::instance( $account_id )->get_resume_url( $data );
        if ( isset( $url['error'] ) ) {
            wp_send_json_error( $url );
        }
        wp_send_json_success( $url );
    }

    public function get_share_link() {
        // Check nonce
        $this->check_nonce();
        // Set current shortcode data
        $this->set_current_shortcode_data();
        // Check permission
        if ( !Shortcode::can_do( 'share' ) ) {
            wp_send_json_error( __( 'You do not have permission to perform this action', 'integrate-google-drive' ) );
        }
        $file = ( !empty( $_POST['file'] ) ? igd_sanitize_array( $_POST['file'] ) : [] );
        $embed_link = igd_get_embed_url( $file );
        if ( !$embed_link ) {
            wp_send_json_error( [
                'message' => __( 'Something went wrong! Preview is not available', 'integrate-google-drive' ),
            ] );
        }
        $view_link = str_replace( ['edit?usp=drivesdk', 'preview?rm=minimal', 'preview'], 'view', $embed_link );
        // Insert log
        do_action(
            'igd_insert_log',
            'share',
            $file['id'],
            $file['accountId']
        );
        wp_send_json_success( [
            'embedLink' => $embed_link,
            'viewLink'  => $view_link,
        ] );
    }

    public function download() {
        // Check nonce
        $this->check_nonce();
        // Set current shortcode data
        $this->set_current_shortcode_data();
        // Check permission
        if ( !Shortcode::can_do( 'download' ) ) {
            wp_send_json_error( __( 'You do not have permission to perform this action', 'integrate-google-drive' ) );
        }
        $file_id = ( !empty( $_REQUEST['id'] ) ? sanitize_text_field( $_REQUEST['id'] ) : '' );
        $file_ids = ( !empty( $_REQUEST['file_ids'] ) ? json_decode( base64_decode( sanitize_text_field( $_REQUEST['file_ids'] ) ) ) : [] );
        $request_id = ( !empty( $_REQUEST['request_id'] ) ? sanitize_text_field( $_REQUEST['request_id'] ) : '' );
        $account_id = ( !empty( $_REQUEST['accountId'] ) ? sanitize_text_field( $_REQUEST['accountId'] ) : '' );
        $mimetype = ( !empty( $_REQUEST['mimetype'] ) ? sanitize_text_field( $_REQUEST['mimetype'] ) : 'default' );
        if ( !empty( $file_ids ) ) {
            igd_download_zip( $file_ids, $request_id, $account_id );
        } elseif ( !empty( $file_id ) ) {
            Download::instance( $file_id, $account_id, $mimetype )->start_download();
        } else {
            wp_send_json_error( __( 'File not found', 'integrate-google-drive' ) );
        }
        exit;
    }

    public function get_download_status() {
        // Check nonce
        $this->check_nonce();
        $id = ( !empty( $_REQUEST['id'] ) ? sanitize_text_field( $_REQUEST['id'] ) : '' );
        $status = get_transient( 'igd_download_status_' . $id );
        wp_send_json_success( $status );
    }

    public function update_description() {
        // Check nonce
        $this->check_nonce();
        $id = ( !empty( $_POST['id'] ) ? sanitize_text_field( $_POST['id'] ) : '' );
        $account_id = ( !empty( $_POST['accountId'] ) ? sanitize_text_field( $_POST['accountId'] ) : '' );
        $description = ( !empty( $_POST['description'] ) ? sanitize_text_field( $_POST['description'] ) : '' );
        $item_id = ( !empty( $_POST['wcItemId'] ) ? intval( $_POST['wcItemId'] ) : false );
        $product_id = ( !empty( $_POST['wcProductId'] ) ? intval( $_POST['wcProductId'] ) : false );
        $updated_file = App::instance( $account_id )->update_description( $id, $description );
        if ( $item_id ) {
            if ( function_exists( 'wc_add_order_item_meta' ) ) {
                wc_add_order_item_meta( $item_id, '_igd_files', $updated_file );
            }
        } elseif ( $product_id ) {
            // Save uploaded files in the session for checkout page
            if ( function_exists( 'WC' ) ) {
                $files = WC()->session->get( 'igd_product_files_' . $product_id, [] );
                foreach ( $files as $key => $file ) {
                    if ( $file['id'] === $id ) {
                        $files[$key] = $updated_file;
                    }
                }
                WC()->session->set( 'igd_product_files_' . $product_id, $files );
            }
        }
        wp_send_json_success( $updated_file );
    }

    public function stream_content() {
        // Check nonce
        $this->check_nonce();
        // Set current shortcode data
        $this->set_current_shortcode_data();
        // Get posted data
        $file_id = ( !empty( $_REQUEST['id'] ) ? sanitize_text_field( $_REQUEST['id'] ) : '' );
        $account_id = ( !empty( $_REQUEST['account_id'] ) ? sanitize_text_field( $_REQUEST['account_id'] ) : '' );
        $ignore_limit = !empty( $_REQUEST['ignore_limit'] );
        Stream::instance( $file_id, $account_id, $ignore_limit )->stream_content();
        exit;
    }

    public function check_nonce() {
        $nonce_action = ( is_user_logged_in() ? 'igd' : 'igd-shortcode-nonce' );
        if ( !check_ajax_referer( $nonce_action, 'nonce', false ) ) {
            wp_send_json_error( __( 'Invalid nonce', 'integrate-google-drive' ) );
        }
    }

    public function set_current_shortcode_data() {
        if ( !empty( $_REQUEST['shortcodeId'] ) ) {
            $shortcode_id = sanitize_text_field( $_REQUEST['shortcodeId'] );
            Shortcode::set_current_shortcode( $shortcode_id );
        }
    }

    public static function instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

}

Ajax::instance();
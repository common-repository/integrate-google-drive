<?php

namespace IGD;

defined( 'ABSPATH' ) || exit;
class Shortcode {
    /**
     * @var null
     */
    protected static $instance = null;

    private $type = null;

    private $data;

    private static $current_shortcode;

    public function __construct() {
        add_shortcode( 'integrate_google_drive', [$this, 'render_shortcode'] );
    }

    /**
     * @param $atts array
     * @param $data mixed
     *
     * @return false|string|void
     */
    public function render_shortcode( $atts = [], $data = null ) {
        $this->fetch_data( $atts, $data );
        // If the shortcode is not found, return
        if ( empty( $this->data ) ) {
            return;
        }
        if ( !$this->check_status() ) {
            return;
        }
        // Enqueue frontend styles
        wp_enqueue_style( 'igd-frontend' );
        // Access denied message
        if ( !$this->check_should_show() || !$this->check_use_private_files() ) {
            return $this->get_access_denied_message();
        }
        // Enqueue frontend scripts
        $this->enqueue_scripts();
        $this->set_permissions();
        $this->get_initial_search_term();
        $this->set_filters();
        $this->set_notifications();
        $this->process_files();
        $this->set_account();
        // Set shortcode id, nonce and transient
        $this->set_shortcode_transient();
        return $this->generate_html();
    }

    private function fetch_data( $atts, $data ) {
        // Get the shortcode ID from attributes
        if ( empty( $data ) ) {
            if ( !empty( $atts['data'] ) ) {
                $data = json_decode( base64_decode( $atts['data'] ), true );
            } elseif ( !empty( $atts['id'] ) ) {
                $id = intval( $atts['id'] );
                if ( $id ) {
                    $shortcode = $this->get_shortcode( $id );
                    if ( !empty( $shortcode ) ) {
                        $data = unserialize( $shortcode->config );
                    }
                }
            }
        }
        $this->type = $data['type'] ?? '';
        $this->data = $data;
    }

    private function set_shortcode_transient() {
        // Set ID
        if ( empty( $this->data['id'] ) ) {
            if ( !empty( $data['uniqueId'] ) ) {
                $id = $data['uniqueId'];
            } else {
                $id = 'igd_' . md5( serialize( $this->data ) );
            }
            $this->data['id'] = $id;
        }
        $shortcode_transient = get_transient( $this->data['id'] );
        // Add nonce for non-logged in users
        if ( !is_user_logged_in() ) {
            $this->data['nonce'] = ( !empty( $shortcode_transient['nonce'] ) ? $shortcode_transient['nonce'] : wp_create_nonce( 'igd-shortcode-nonce' ) );
        }
        // Set transient
        if ( !$shortcode_transient ) {
            set_transient( $this->data['id'], $this->data, DAY_IN_SECONDS );
        }
    }

    private function check_status() {
        $status = ( !empty( $this->data['status'] ) ? $this->data['status'] : 'on' );
        // Check shortcode status
        if ( 'off' == $status ) {
            return false;
        }
        return true;
    }

    private function get_access_denied_message() {
        $default_message = '<img width="100" src="' . IGD_ASSETS . '/images/access-denied.png" ><h3 class="placeholder-title">' . __( 'Access Denied', 'integrate-google-drive' ) . '</h3><p class="placeholder-description">' . __( "We're sorry, but your account does not currently have access to this content. To gain access, please contact the site administrator who can assist in linking your account to the appropriate content. Thank you.", 'integrate-google-drive' ) . '</p>';
        $access_denied_message = ( !empty( $this->data['accessDeniedMessage'] ) ? $this->data['accessDeniedMessage'] : $default_message );
        return ( !empty( $this->data['showAccessDeniedMessage'] ) ? sprintf( '<div class="igd-access-denied-placeholder">%s</div>', $access_denied_message ) : false );
    }

    private function set_permissions() {
        // Check file actions Permissions
        if ( in_array( $this->type, [
            'browser',
            'gallery',
            'media',
            'search',
            'slider'
        ] ) ) {
            // Preview
            $this->data['preview'] = !isset( $this->data['preview'] ) || !empty( $this->data['preview'] ) && $this->check_permission( 'preview' );
            // Download
            $this->data['download'] = !empty( $this->data['download'] ) && $this->check_permission( 'download' );
            // Delete
            $this->data['canDelete'] = !empty( $this->data['canDelete'] ) && $this->check_permission( 'canDelete' );
            // Rename
            $this->data['rename'] = !empty( $this->data['rename'] ) && $this->check_permission( 'rename' );
            // Upload
            $this->data['upload'] = !empty( $this->data['upload'] ) && $this->check_permission( 'upload' );
            // New Folder
            $this->data['newFolder'] = !empty( $this->data['newFolder'] ) && $this->check_permission( 'newFolder' );
            // moveCopy
            $this->data['moveCopy'] = !empty( $this->data['moveCopy'] ) && $this->check_permission( 'moveCopy' );
            // Share
            $this->data['allowShare'] = !empty( $this->data['allowShare'] ) && $this->check_permission( 'allowShare' );
            // Search
            $this->data['allowSearch'] = 'search' == $this->type || !empty( $this->data['allowSearch'] ) && $this->check_permission( 'allowSearch' );
            // Create
            $this->data['createDoc'] = !empty( $this->data['createDoc'] ) && $this->check_permission( 'createDoc' );
            // Edit
            $this->data['edit'] = !empty( $this->data['edit'] ) && $this->check_permission( 'edit' );
            // Direct Link
            $this->data['directLink'] = !empty( $this->data['directLink'] ) && $this->check_permission( 'directLink' );
            // Details
            $this->data['details'] = !empty( $this->data['details'] ) && $this->check_permission( 'details' );
            // Details
            $this->data['comment'] = !empty( $this->data['comment'] ) && $this->check_permission( 'comment' );
            // photoProof
            $this->data['photoProof'] = !empty( $this->data['photoProof'] ) && $this->check_permission( 'photoProof' );
        }
    }

    private function check_permission( $permission_type ) {
        $typeUserKeyMap = [
            'preview'     => 'previewUsers',
            'download'    => 'downloadUsers',
            'upload'      => 'uploadUsers',
            'allowShare'  => 'shareUsers',
            'createDoc'   => 'createDocUsers',
            'edit'        => 'editUsers',
            'directLink'  => 'directLinkUsers',
            'details'     => 'detailsUsers',
            'allowSearch' => 'searchUsers',
            'canDelete'   => 'deleteUsers',
            'rename'      => 'renameUsers',
            'moveCopy'    => 'moveCopyUsers',
            'newFolder'   => 'newFolderUsers',
            'comment'     => 'commentUsers',
            'photoProof'  => 'photoProofUsers',
        ];
        $userKey = ( isset( $typeUserKeyMap[$permission_type] ) ? $typeUserKeyMap[$permission_type] : null );
        $users = ( $userKey && isset( $this->data[$userKey] ) ? $this->data[$userKey] : ['everyone'] );
        if ( in_array( 'everyone', $users ) ) {
            return true;
        } elseif ( is_user_logged_in() ) {
            $current_user = wp_get_current_user();
            if ( !empty( array_intersect( $current_user->roles, $users ) ) ) {
                // If matches roles
                return true;
            }
            if ( in_array( $current_user->ID, $users ) ) {
                // If current user_id
                return true;
            }
        }
        return false;
    }

    private function get_initial_search_term() {
        if ( !empty( $this->data['allowSearch'] ) && !empty( $this->data['initialSearchTerm'] ) && strpos( $this->data['initialSearchTerm'], '%' ) !== false ) {
            $search_template = $this->data['initialSearchTerm'];
            $tag_args = [
                'name' => $search_template,
            ];
            // Add user data
            if ( igd_contains_tags( 'user', $search_template ) ) {
                if ( is_user_logged_in() ) {
                    $tag_args['user'] = get_userdata( get_current_user_id() );
                }
            }
            // Add the current post to the args
            if ( igd_contains_tags( 'post', $search_template ) ) {
                global $post;
                if ( !empty( $post ) ) {
                    $tag_args['post'] = $post;
                    // if post is a product get the product
                    if ( $post->post_type == 'product' ) {
                        $product = wc_get_product( $post->ID );
                        if ( !empty( $product ) ) {
                            $tag_args['wc_product'] = $product;
                        }
                    }
                }
            }
            $this->data['initialSearchTerm'] = igd_replace_template_tags( $tag_args );
        }
    }

    private function check_use_private_files() {
        return true;
    }

    private function process_files() {
        // Check ACF dynamic field files
        if ( !empty( $this->data['acfDynamicFiles'] ) ) {
            $this->data['folders'] = ( !empty( $this->data['acfFieldKey'] ) ? $this->get_acf_dynamic_field_files( $this->data['acfFieldKey'] ) : [] );
        }
        // Check if uploader module and selected folder is enabled
        if ( 'uploader' == $this->type && !empty( $this->data['uploadFolderSelection'] ) ) {
            if ( empty( $this->data['folders'] ) ) {
                $this->data['folders'] = $this->data['uploadFolders'] ?? [];
            }
        }
        // First, we check if the 'type' is one of the specified values and 'folders' is not empty.
        if ( !in_array( $this->type, [
            'browser',
            'gallery',
            'media',
            'slider',
            'embed'
        ] ) || empty( $this->data['folders'] ) ) {
            return;
        }
        // Process based on whether there is a single folder or multiple.
        $is_single_folder = count( $this->data['folders'] ) == 1 && igd_is_dir( reset( $this->data['folders'] ) );
        if ( $is_single_folder ) {
            $this->process_single_folder();
        } else {
            $this->get_files_from_server();
        }
        // If the type is 'slider' and the user can use premium code, process the files accordingly.
        if ( $this->type == 'slider' ) {
            $this->get_slider_files();
        }
        // Filter files if necessary.
        if ( igd_should_filter_files( $this->data['filters'] ) ) {
            $this->check_filters();
        }
    }

    private function get_acf_dynamic_field_files( $field_key ) {
        // Return early if the field key is empty or the 'get_field' function does not exist
        if ( empty( $field_key ) || !function_exists( 'get_field' ) ) {
            return [];
        }
        // Retrieve the files using the field key
        $files = get_field( $field_key );
        // If files are not empty, process each file
        if ( !empty( $files ) && is_array( $files ) ) {
            $files = array_map( function ( $file ) {
                if ( isset( $file['account_id'] ) ) {
                    // Rename the 'account_id' key to 'accountId'
                    $file['accountId'] = $file['account_id'];
                    $file['webViewLink'] = $file['view_link'];
                }
                return $file;
            }, $files );
        }
        return ( $files ?: [] );
        // Ensure the function always returns an array
    }

    private function get_slider_files() {
        $files = [];
        foreach ( $this->data['folders'] as $key => $folder ) {
            if ( igd_is_dir( $folder ) ) {
                // Merge files from folder into $files array if it's a directory.
                $files = array_merge( $files, igd_get_all_child_files( $folder ) );
            } else {
                // Otherwise, just add the single file.
                $files[] = $folder;
            }
        }
        // Filter the $files array to exclude directories and files without 'thumbnailLink'.
        $filtered_files = array_filter( $files, function ( $file ) {
            return !igd_is_dir( $file ) && !empty( $file['thumbnailLink'] );
        } );
        // Merge and re-index the folders with the filtered files.
        $this->data['folders'] = array_values( $filtered_files );
    }

    private function process_single_folder() {
        $folder = reset( $this->data['folders'] );
        $this->data['initParentFolder'] = $folder;
        if ( is_array( $folder ) ) {
            $folder_id = $folder['id'];
            $args = [
                'folder'      => $folder,
                'sort'        => ( !empty( $this->data['sort'] ) ? $this->data['sort'] : [] ),
                'fileNumbers' => ( !empty( $this->data['fileNumbers'] ) ? $this->data['fileNumbers'] : -1 ),
                'filters'     => ( !empty( $this->data['filters'] ) ? $this->data['filters'] : [] ),
            ];
            // lazy load items
            if ( in_array( $this->type, ['browser', 'gallery'] ) ) {
                if ( !isset( $this->data['lazyLoad'] ) || !empty( $this->data['lazyLoad'] ) ) {
                    $args['limit'] = ( !empty( $this->data['lazyLoadNumber'] ) ? $this->data['lazyLoadNumber'] : 100 );
                }
            }
            // Set from server true only for browser and gallery
            if ( in_array( $this->type, ['browser', 'gallery'] ) ) {
                $args['from_server'] = true;
                $transient = get_transient( 'igd_latest_fetch_' . $folder_id );
                if ( $transient ) {
                    $args['from_server'] = false;
                } else {
                    set_transient( 'igd_latest_fetch_' . $folder_id, true, 60 * MINUTE_IN_SECONDS );
                }
            }
            // Fetch files
            $account_id = ( !empty( $folder['accountId'] ) ? $folder['accountId'] : '' );
            $files_data = App::instance( $account_id )->get_files( $args );
            if ( isset( $files_data['files'] ) ) {
                $this->data['folders'] = array_values( $files_data['files'] );
            }
            // Update the arguments for the next iteration
            $should_update_page_number = !empty( $this->data['lazyLoad'] ) && !empty( $this->data['lazyLoadType'] ) && 'pagination' != $this->data['lazyLoadType'];
            $page_number = ( $should_update_page_number ? $files_data['nextPageNumber'] ?? 0 : 1 );
            $this->data['initParentFolder']['pageNumber'] = $page_number;
            if ( !empty( $files_data['count'] ) ) {
                $this->data['initParentFolder']['count'] = $files_data['count'];
            }
        }
    }

    private function get_files_from_server() {
        $cache_key = "igd_latest_fetch_" . md5( serialize( $this->data['folders'] ) );
        // Get files from server to update the cache
        if ( !get_transient( $cache_key ) ) {
            set_transient( $cache_key, true, HOUR_IN_SECONDS );
            $account_id = reset( $this->data['folders'] )['accountId'];
            $app = App::instance( $account_id );
            $client = $app->client;
            $service = $app->getService();
            $batch = new \IGDGoogle_Http_Batch($client);
            $client->setUseBatch( true );
            foreach ( $this->data['folders'] as $key => $folder ) {
                // Check if file is drive
                try {
                    if ( !empty( $folder['shared-drives'] ) ) {
                        $request = $service->drives->get( $folder['id'], [
                            'fields' => '*',
                        ] );
                    } else {
                        $request = $service->files->get( $folder['id'], [
                            'supportsAllDrives' => true,
                            'fields'            => $app->file_fields,
                        ] );
                    }
                } catch ( \Exception $exception ) {
                    error_log( 'IGD SDK ERROR: ' . $exception->getMessage() );
                    return;
                }
                $batch->add( $request, ( $key ?: '-1' ) );
            }
            $batch_result = $batch->execute();
            $client->setUseBatch( false );
            foreach ( $batch_result as $key => $file ) {
                $index = max( 0, str_replace( 'response-', '', $key ) );
                if ( empty( $file ) || is_a( $file, 'IGDGoogle_Service_Exception' ) || is_a( $file, 'IGDGoogle_Exception' ) ) {
                    unset($this->data['folders'][$index]);
                    continue;
                }
                $fileLimitExceeded = isset( $this->data['fileNumbers'] ) && $this->data['fileNumbers'] > 0 && count( $this->data['folders'] ) > $this->data['fileNumbers'];
                if ( $fileLimitExceeded ) {
                    unset($this->data['folders'][$index]);
                    continue;
                }
                // check if file is drive
                if ( is_a( $file, 'IGDGoogle_Service_Drive_DriveList' ) ) {
                    $file = igd_drive_map( $file, $account_id );
                } else {
                    $file = igd_file_map( $file, $account_id );
                }
                Files::add_file( $file );
                $this->data['folders'][$index] = $file;
            }
        } else {
            // Get files from cache
            foreach ( $this->data['folders'] as $key => $file ) {
                $account_id = $file['accountId'];
                $file_id = $file['id'];
                $file = App::instance( $account_id )->get_file_by_id( $file_id );
                if ( empty( $file ) || !is_array( $file ) ) {
                    unset($this->data['folders'][$key]);
                    continue;
                }
                $this->data['folders'][$key] = $file;
            }
        }
        // Check max file number
        if ( isset( $this->data['fileNumbers'] ) && $this->data['fileNumbers'] > 0 && count( $this->data['folders'] ) > $this->data['fileNumbers'] ) {
            $this->data['folders'] = array_values( array_slice( $this->data['folders'], 0, $this->data['fileNumbers'] ) );
        }
    }

    private function set_filters() {
        $filters = [
            'allowExtensions'       => ( !empty( $this->data['allowExtensions'] ) ? str_replace( ' ', '', $this->data['allowExtensions'] ) : '' ),
            'allowAllExtensions'    => $this->data['allowAllExtensions'] ?? false,
            'allowExceptExtensions' => ( !empty( $this->data['allowExceptExtensions'] ) ? str_replace( ' ', '', $this->data['allowExceptExtensions'] ) : '' ),
            'allowNames'            => $this->data['allowNames'] ?? '',
            'allowAllNames'         => $this->data['allowAllNames'] ?? '',
            'allowExceptNames'      => $this->data['allowExceptNames'] ?? '',
            'nameFilterOptions'     => $this->data['nameFilterOptions'] ?? ['files'],
            'showFiles'             => $this->data['showFiles'] ?? true,
            'showFolders'           => $this->data['showFolders'] ?? true,
        ];
        if ( 'gallery' == $this->type ) {
            $filters['isGallery'] = true;
        }
        if ( 'media' == $this->type ) {
            $filters['isMedia'] = true;
        }
        $this->data['filters'] = $filters;
    }

    private function set_notifications() {
        if ( empty( $this->data['enableNotification'] ) ) {
            return;
        }
        $notifications = [
            'downloadNotification'        => $this->data['downloadNotification'] ?? true,
            'uploadNotification'          => $this->data['uploadNotification'] ?? true,
            'deleteNotification'          => $this->data['deleteNotification'] ?? true,
            'playNotification'            => $this->data['playNotification'] ?? 'media' == $this->type,
            'searchNotification'          => $this->data['searchNotification'] ?? 'search' == $this->type,
            'viewNotification'            => $this->data['viewNotification'] ?? true,
            'notificationEmail'           => $this->data['notificationEmail'] ?? '%admin_email%',
            'skipCurrentUserNotification' => $this->data['skipCurrentUserNotification'] ?? true,
        ];
        $this->data['notifications'] = $notifications;
    }

    private function check_filters() {
        if ( in_array( $this->type, [
            'browser',
            'gallery',
            'media',
            'search',
            'slider',
            'embed'
        ] ) && !empty( $this->data['folders'] ) ) {
            $filters = $this->data['filters'];
            $this->data['folders'] = array_values( array_filter( $this->data['folders'], function ( $item ) use($filters) {
                return igd_should_allow( $item, $filters );
            } ) );
        }
    }

    protected function set_account() {
        // Set active account
        if ( !empty( $this->data['folders'] ) ) {
            $folder = reset( $this->data['folders'] );
            $this->data['account'] = Account::instance()->get_accounts( $folder['accountId'] );
        }
    }

    private function generate_html() {
        $width = ( !empty( $this->data['moduleWidth'] ) ? $this->data['moduleWidth'] : '100%' );
        $height = ( !empty( $this->data['moduleHeight'] ) ? $this->data['moduleHeight'] : '' );
        switch ( $this->type ) {
            case 'embed':
                $html = igd_get_embed_content( $this->data );
                break;
            case 'download':
                $html = $this->get_download_links_html();
                break;
            case 'view':
                $html = $this->get_view_links_html();
                break;
            default:
                ob_start();
                ?>
                <div class="igd igd-shortcode-wrap igd-shortcode-<?php 
                echo esc_attr( $this->type );
                ?>"
                     data-shortcode-data="<?php 
                echo base64_encode( json_encode( $this->data ) );
                ?>"
                     style="width: <?php 
                echo esc_attr( $width );
                ?>;  <?php 
                echo ( !empty( $height ) ? esc_attr( 'height:' . $height ) . ';' : '' );
                ?>"
                ></div>
				<?php 
                $html = ob_get_clean();
                break;
        }
        return $html;
    }

    /**
     * Check if the shortcode should be shown.
     *
     * @return bool
     */
    public function check_should_show() {
        $display_for = $this->data['displayFor'] ?? 'everyone';
        if ( $display_for === 'everyone' ) {
            return true;
        }
        if ( $display_for !== 'loggedIn' || !is_user_logged_in() ) {
            return false;
        }
        $display_users = $this->data['displayUsers'] ?? [];
        $display_everyone = filter_var( $this->data['displayEveryone'] ?? false, FILTER_VALIDATE_BOOLEAN );
        $display_except = $this->data['displayExcept'] ?? [];
        $current_user = wp_get_current_user();
        $user_roles = array_filter( $display_users, 'is_string' );
        $except_user_roles = array_filter( $display_except, 'is_string' );
        // if display_everyone is true and the user is not in the exception list
        if ( $display_everyone && !in_array( $current_user->ID, $display_except ) && empty( array_intersect( $current_user->roles, $except_user_roles ) ) ) {
            return true;
        }
        // if the users list contains 'everyone' or the user's role or the user's ID
        if ( in_array( 'everyone', $user_roles ) || !empty( array_intersect( $current_user->roles, $user_roles ) ) || in_array( $current_user->ID, $display_users ) ) {
            return true;
        }
        // if no users specified and either display_everyone is true with no exceptions or display_everyone is false
        if ( empty( $display_users ) && ($display_everyone && empty( $except_users ) || !$display_everyone) ) {
            return true;
        }
        return false;
    }

    public function enqueue_scripts() {
        wp_enqueue_script( 'igd-frontend' );
    }

    private function get_view_links_html() {
        $items = $this->data['folders'] ?? [];
        $html = '';
        if ( empty( $items ) ) {
            return $html;
        }
        $should_send_notification = !empty( $this->data['notifications'] ) && !empty( $this->data['notifications']['viewNotification'] ) && !empty( $this->data['notifications']['notificationEmail'] );
        $notification_data_html = ( $should_send_notification ? ' data-notification-email="' . esc_attr( $this->data['notifications']['notificationEmail'] ) . '" data-skip-current-user-notification="' . esc_attr( $this->data['notifications']['skipCurrentUserNotification'] ) . '"' : '' );
        $is_folder_files = !empty( $this->data['folderFiles'] );
        $files = [];
        if ( $is_folder_files ) {
            foreach ( $items as $item ) {
                if ( igd_is_dir( $item ) ) {
                    // Merge files from folder into $files array if it's a directory.
                    $files = array_merge( $files, igd_get_child_items( $item ) );
                } else {
                    // Otherwise, just add the single file.
                    $files[] = $item;
                }
            }
        } else {
            $files = $items;
        }
        foreach ( $files as $file ) {
            $name = $file['name'];
            $view_link = $file['webViewLink'];
            $file_data_html = ( !empty( $should_send_notification ) ? ' data-id="' . esc_attr( $file['id'] ) . '" data-account-id="' . esc_attr( $file['accountId'] ) . '"' : '' );
            $data_html = $notification_data_html . $file_data_html;
            $html .= sprintf(
                '<a href="%1$s" class="igd-view-link" target="_blank" %2$s>%3$s</a>',
                $view_link,
                $data_html,
                $name
            );
        }
        return $html;
    }

    private function get_download_links_html() {
        $items = $this->data['folders'] ?? [];
        $html = '';
        if ( empty( $items ) ) {
            return $html;
        }
        $should_send_notification = !empty( $this->data['notifications']['viewNotification'] ) && !empty( $this->data['notifications']['notificationEmail'] );
        $notification_data_html = ( $should_send_notification ? ' data-notification-email="' . esc_attr( $this->data['notifications']['notificationEmail'] ) . '" data-skip-current-user-notification="' . esc_attr( $this->data['notifications']['skipCurrentUserNotification'] ) . '"' : '' );
        $is_folder_files = !empty( $this->data['folderFiles'] );
        $files = [];
        if ( $is_folder_files ) {
            foreach ( $items as $item ) {
                if ( igd_is_dir( $item ) ) {
                    // Merge files from folder into $files array if it's a directory.
                    $files = array_merge( $files, igd_get_child_items( $item ) );
                } else {
                    // Otherwise, just add the single file.
                    $files[] = $item;
                }
            }
        } else {
            $files = $items;
        }
        $shortcode_id = $this->data['id'];
        $nonce = ( is_user_logged_in() ? wp_create_nonce( 'igd' ) : $this->data['nonce'] );
        foreach ( $files as $file ) {
            $id = $file['id'];
            $account_id = $file['accountId'];
            $name = $file['name'];
            $download_link = admin_url( "admin-ajax.php?action=igd_download&" . (( igd_is_dir( $file ) ? "file_ids=" . base64_encode( json_encode( [$id] ) ) : "id={$id}&accountId={$account_id}&shortcodeId={$shortcode_id}&nonce={$nonce}" )) );
            $file_data_html = ( $should_send_notification ? ' data-id="' . esc_attr( $id ) . '" data-account-id="' . esc_attr( $account_id ) . '"' : '' );
            $data_html = $notification_data_html . $file_data_html;
            $html .= sprintf(
                '<a href="%1$s" class="igd-download-link" %2$s>%3$s</a>',
                $download_link,
                $data_html,
                $name
            );
        }
        return $html;
    }

    /**
     * @return Shortcode|null
     */
    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /***---- Shortcode Builder Methods ----***/
    public static function get_shortcode( $id ) {
        global $wpdb;
        $table = $wpdb->prefix . 'integrate_google_drive_shortcodes';
        return $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE id=%d", $id ) );
    }

    public static function get_shortcodes( $args = [] ) {
        $offset = ( !empty( $args['offset'] ) ? intval( $args['offset'] ) : 0 );
        $limit = ( !empty( $args['limit'] ) ? intval( $args['limit'] ) : 999 );
        $order_by = ( !empty( $args['order_by'] ) ? sanitize_key( $args['order_by'] ) : 'created_at' );
        $order = ( !empty( $args['order'] ) ? sanitize_key( $args['order'] ) : 'DESC' );
        global $wpdb;
        $table = $wpdb->prefix . 'integrate_google_drive_shortcodes';
        return $wpdb->get_results( "SELECT * FROM {$table} ORDER BY {$order_by} {$order} LIMIT {$offset}, {$limit}" );
    }

    public static function get_shortcodes_count() {
        global $wpdb;
        $table = $wpdb->prefix . 'integrate_google_drive_shortcodes';
        return $wpdb->get_var( "SELECT COUNT(*) FROM {$table}" );
    }

    public static function update_shortcode( $posted, $force_insert = false ) {
        global $wpdb;
        $table = $wpdb->prefix . 'integrate_google_drive_shortcodes';
        $id = ( !empty( $posted['id'] ) ? intval( $posted['id'] ) : '' );
        $status = ( !empty( $posted['status'] ) ? sanitize_key( $posted['status'] ) : 'on' );
        $title = ( !empty( $posted['title'] ) ? sanitize_text_field( $posted['title'] ) : '' );
        $data = [
            'title'  => $title,
            'status' => $status,
            'config' => ( !empty( $posted['config'] ) ? $posted['config'] : serialize( $posted ) ),
        ];
        $data_format = ['%s', '%s', '%s'];
        if ( !empty( $posted['created_at'] ) ) {
            $data['created_at'] = $posted['created_at'];
            $data_format[] = '%s';
        }
        if ( !empty( $posted['updated_at'] ) ) {
            $data['updated_at'] = $posted['updated_at'];
            $data_format[] = '%s';
        }
        if ( !$id || $force_insert ) {
            $wpdb->insert( $table, $data, $data_format );
            return $wpdb->insert_id;
        } else {
            $wpdb->update(
                $table,
                $data,
                [
                    'id' => $id,
                ],
                $data_format,
                ['%d']
            );
            // Reset shortcode data transient
            self::reset_shortcode_transients( $data );
            return $id;
        }
    }

    public static function duplicate_shortcode( $id ) {
        if ( empty( $id ) ) {
            return false;
        }
        $shortcode = self::get_shortcode( $id );
        if ( $shortcode ) {
            $shortcode = (array) $shortcode;
            $shortcode['title'] = 'Copy of ' . $shortcode['title'];
            $shortcode['created_at'] = current_time( 'mysql' );
            $shortcode['updated_at'] = current_time( 'mysql' );
            $shortcode['locations'] = serialize( [] );
            $insert_id = self::update_shortcode( $shortcode, true );
            $data = array_merge( $shortcode, [
                'id'        => $insert_id,
                'config'    => unserialize( $shortcode['config'] ),
                'locations' => [],
            ] );
            return $data;
        }
        return false;
    }

    public static function delete_shortcode( $id = false ) {
        global $wpdb;
        $table = $wpdb->prefix . 'integrate_google_drive_shortcodes';
        if ( $id ) {
            $wpdb->delete( $table, [
                'id' => $id,
            ], ['%d'] );
        } else {
            $wpdb->query( "TRUNCATE TABLE {$table}" );
        }
    }

    public static function view() {
        ?>
        <div id="igd-shortcode-builder"></div>
	<?php 
    }

    /***---- Shortcode Data Methods ----***/
    public static function get_shortcode_data( $shortcode_id ) {
        $data = false;
        if ( strpos( $shortcode_id, 'igd_' ) !== false ) {
            $data = get_transient( $shortcode_id );
        } else {
            $shortcode = Shortcode::get_shortcode( $shortcode_id );
            if ( !empty( $shortcode ) ) {
                $data = unserialize( $shortcode->config );
            }
        }
        return $data;
    }

    public static function set_current_shortcode( $shortcode_id ) {
        $shortcode_data = self::get_shortcode_data( $shortcode_id );
        if ( !empty( $shortcode_data ) ) {
            self::$current_shortcode = $shortcode_data;
        }
    }

    public static function get_current_shortcode() {
        return self::$current_shortcode;
    }

    /**
     * Check if the user has specific action permission
     *
     * @param string $action
     * @param $data
     * @param $shortcode_data
     *
     * @return bool
     */
    public static function can_do( $action = '', $posted = [], $shortcode_data = false ) {
        if ( !$shortcode_data ) {
            $shortcode_data = self::get_current_shortcode();
        }
        // Early exit if shortcode_data is empty
        if ( empty( $shortcode_data ) ) {
            return is_user_logged_in();
        }
        // Handle case where all folders are accessible
        if ( !empty( $shortcode_data['allFolders'] ) || !empty( $shortcode_data['privateFolders'] ) ) {
            if ( in_array( $action, ['get_files', 'search_files', 'switch_account'] ) ) {
                return true;
            }
        }
        $module_type = $shortcode_data['type'] ?? '';
        switch ( $action ) {
            case 'get_files':
                // Handle specific folder access
                if ( !empty( $folder = $posted['folder'] ) ) {
                    $shortcode_folder_ids = array_map( function ( $folder ) {
                        return $folder['id'];
                    }, $shortcode_data['folders'] );
                    $breadcrumbs_keys = array_keys( igd_get_breadcrumb( $folder ) );
                    // check if any breadcrumb is in shortcode folders
                    if ( !empty( array_intersect( $breadcrumbs_keys, $shortcode_folder_ids ) ) ) {
                        return true;
                    }
                    return false;
                }
                break;
            case 'search_files':
                // Handle file search with specific conditions
                return !empty( $shortcode_data['allowSearch'] ) || 'search' == $module_type;
            case 'get_file':
                return !empty( $shortcode_data['details'] );
            case 'delete_files':
                return !empty( $shortcode_data['canDelete'] );
            case 'new_folder':
                return !empty( $shortcode_data['newFolder'] );
            case 'move_copy':
                return !empty( $shortcode_data['moveCopy'] );
            case 'rename':
                return !empty( $shortcode_data['rename'] );
            case 'preview':
                return !isset( $shortcode_data['preview'] ) || !empty( $shortcode_data['preview'] );
            case 'create_doc':
                return !empty( $shortcode_data['createDoc'] );
            case 'update_file_permission':
                return 'media' == $module_type && !empty( $shortcode_data['allowEmbedPlayer'] );
            case 'photo_proof':
                return 'gallery' == $module_type && !empty( $shortcode_data['photoProof'] );
            case 'upload':
                return in_array( $module_type, ['uploader', 'browser'] );
            case 'share':
                return !empty( $shortcode_data['allowShare'] );
            case 'download':
                return !empty( $shortcode_data['download'] );
            default:
                // No valid action matched
                return false;
        }
        // Action provided does not result in a clear decision
        return false;
    }

    /**
     * Reset shortcode related any transients
     *
     * @param $shortcode_id
     *
     * @return void
     */
    public static function reset_shortcode_transients( $data ) {
        $shortcode_id = $data['id'] ?? $data['uniqueId'] ?? 'igd_' . md5( serialize( $data ) );
        set_transient( $shortcode_id, $data, DAY_IN_SECONDS );
    }

}

Shortcode::instance();
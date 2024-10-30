<?php

$logs = [

	'v.1.4.4' => [
		'date'        => '2024-10-09',
		'new'         => [
			'Added upload folder selection and file renaming settings in WooCommerce uploads.',
		],
		'fix' => [
			'Fixed database tables not created.',
			'Fixed file, folder not renaming properly in WooCommerce uploads.',
			'Fixed remember last folder not working issue.',
		],
	],


	'v.1.4.3' => [
		'date'        => '2024-10-03',
		'fix'         => [
			'Fixed module notifications not reaching to the correct recipients.',
		],
		'enhancement' => [
			'Improve Accounts connection UI/UX.',
		],
	],


	'v.1.4.2' => [
		'date'        => '2024-09-29',
		'new'         => [
			'Added option for specific folders accessibility in the app.',
			'Added folder selection options in the uploader.',
			'Added a "None" preloader setting option to disable the preloader during file loading.',
		],
		'enhancement' => [
			'Improved overall performance and stability.',
		],
	],

	'v.1.4.1' => [
		'date'        => '2024-09-14',
		'enhancement' => [
			'Upgraded plugin\'s integrated Google App.',
		],
	],

	'v.1.4.0' => [
		'date' => '2024-08-26',
		'new'  => [
			'Added woocommerce upload box checkout location setting.',
		],
		'fix'  => [
			'Fixed MetForm Google Drive upload filed not saving the configuration data.',
			'Fixed remove file button not working for Google Drive uploader forms field.',
			'Fixed WPForms Google Drive upload filed smart tag output issue.',
		],
	],

	'v.1.3.99' => [
		'date' => '2024-08-14',
		'fix'  => [
			'Fixed Invalid nonce error.',
			'Fixed permission check issue.',
			'Fixed conflict with the Real Media Library plugin.',
		],
	],
	'v.1.3.98' => [
		'date' => '2024-08-09',
		'fix'  => [
			'Fixed Preview not working issue.',
			'Fixed Gutenberg editor Google Drive blocks not working properly.',
			'Fixed Divi Builder reload issue.',
		],
	],

	'v.1.3.97' => [
		'date'        => '2024-08-07',
		'new'         => [
			'Added file description in the notification email.',
			'Added dynamic option to use ACF field value as module source files.',
			'Added usage limits options to control download and bandwidth usage.',
		],
		'fix'         => [
			'Fixed direct media embed not working.',
			'Fixed search results not showing proper pagination issue.',
			'Fixed Google account not switching in the media library integration.',
			'Fixed Elementor form multiple Google Drive upload field configuration issue.',
		],
		'enhancement' => [
			'Revert the Gallery Shortcode Module and Contact Form 7 Integration to the free version.',
			'Added support up to WordPress 6.6.1',
			'Improved overall performance and stability.',
		],
	],

	'v.1.3.96' => [
		'date' => '2024-06-11',
		'fix'  => [
			'Fixed PHP fatal error.',
		],
	],

	'v.1.3.95' => [
		'date'        => '2024-06-10',
		'new'         => [
			'Added thumbnail preview for selected items for the uploader module.',
			'Added server throttle setting to prevent resource issues during downloading/streaming on budget hosts.',
			'Added secure video playback setting to prevent direct access to Google Drive video files.',
		],
		'fix'         => [
			'Fixed Shared drives folder navigation not working properly.',
			'Fixed Formidable Forms file and folder naming template issue.',
			'Fixed NinjaForms file not uploading issue.',
			'Fixed Google Drive files selection from media library in mobile view.',
		],
		'enhancement' => [
			'Improved video playback speed.',
			'Improved overall performance and stability.',
		],
	],

	'v.1.3.94' => [
		'date' => '2024-04-21',
		'new'  => [
			'Added video playback thumbnail for video items in the gallery.',
			'Added option to display selected folder files for download/view links module.',
		],

		'enhancement' => [
			'Improved overall performance and security.',
		],

		'remove' => [
			'Removed Gallery module from the free version.',
			'Removed Contact Form 7 integration from the free version.',
		],
	],


];


?>

<div id="what-new" class="getting-started-content content-what-new">
    <div class="content-heading">
        <h2><?php esc_html_e( 'Exploring the Latest Updates', 'integrate-google-drive' ); ?></h2>
        <p><?php esc_html_e( 'Dive Into the Recent Change Logs for Fresh Insights', 'integrate-google-drive' ); ?></p>
    </div>

	<?php
	$i = 0;
	foreach ( $logs as $v => $log ) { ?>
        <div class="log <?php echo esc_attr( $i == 0 ? 'active' : '' ); ?>">
            <div class="log-header">
                <span class="log-version"><?php echo esc_html( $v ); ?></span>
                <span class="log-date">(<?php echo esc_html( $log['date'] ); ?>)</span>

                <i class="<?php echo esc_attr( $i == 0 ? 'dashicons-arrow-up-alt2' : 'dashicons-arrow-down-alt2' ); ?> dashicons "></i>
            </div>

            <div class="log-body">
				<?php

				if ( ! empty( $log['new'] ) ) {
					printf( '<div class="log-section new"><h3>%s</h3>', __( 'New Features', 'integrate-google-drive' ) );
					foreach ( $log['new'] as $item ) {
						echo '<div class="log-item log-item-new"><i class="dashicons dashicons-plus-alt2"></i> <span>' . $item . '</span></div>';
					}
					echo '</div>';
				}


				if ( ! empty( $log['fix'] ) ) {
					printf( '<div class="log-section fix"><h3>%s</h3>', __( 'Bug Fixes', 'integrate-google-drive' ) );
					foreach ( $log['fix'] as $item ) {
						echo '<div class="log-item log-item-fix"><i class="dashicons dashicons-saved"></i> <span>' . $item . '</span></div>';
					}
					echo '</div>';
				}

				if ( ! empty( $log['enhancement'] ) ) {
					printf( '<div class="log-section enhancement"><h3>%s</h3>', __( 'Improvements', 'integrate-google-drive' ) );
					foreach ( $log['enhancement'] as $item ) {
						echo '<div class="log-item log-item-enhancement"><i class="dashicons dashicons-star-filled"></i> <span>' . $item . '</span></div>';
					}
					echo '</div>';
				}

				if ( ! empty( $log['remove'] ) ) {
					printf( '<div class="log-section remove"><h3>%s</h3>', __( 'Removes', 'integrate-google-drive' ) );
					foreach ( $log['remove'] as $item ) {
						echo '<div class="log-item log-item-remove"><i class="dashicons dashicons-trash"></i> <span>' . $item . '</span></div>';
					}
					echo '</div>';
				}


				?>
            </div>

        </div>
		<?php
		$i ++;
	} ?>


</div>


<script>
    jQuery(document).ready(function ($) {
        $('.log-header').on('click', function () {
            $(this).next('.log-body').slideToggle();
            $(this).find('i').toggleClass('dashicons-arrow-down-alt2 dashicons-arrow-up-alt2');
            $(this).parent().toggleClass('active');
        });
    });
</script>
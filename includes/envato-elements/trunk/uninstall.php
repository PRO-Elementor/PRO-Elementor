<?php
// If uninstall not called from WordPress exit
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}

delete_option( 'envato_elements_tracker_notice' );
delete_option( 'envato_elements_version' );
delete_option( 'envato_elements_install_time' );
delete_option( '_envato_elements_installed_time' );
delete_option( 'envato_elements_license_code' );
delete_option( 'envato_elements_options' );
delete_option( 'envato_elements_photo_imports' );

// Remove the scheduled task.
wp_clear_scheduled_hook( 'envato_elements_cron' );
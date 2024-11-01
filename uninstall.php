<?php

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}

delete_option('wpis_title');
delete_option('wpis_width');
delete_option('wpis_height');
delete_option('wpis_pause');
delete_option('wpis_random');
delete_option('wpis_type');
 
// for site options in Multisite
delete_site_option('wpis_title');
delete_site_option('wpis_width');
delete_site_option('wpis_height');
delete_site_option('wpis_pause');
delete_site_option('wpis_random');
delete_site_option('wpis_type');

global $wpdb;
$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}wpis_plugin");
<?php
/*
Plugin Name: Csv Import
Plugin URI: https://akismet.com/
Description: This is simple image csv file import plugin.
Version: 1.0
Author: Hitesh
Author URI: https://automattic.com/wordpress-plugins/
*/


//If this file is called directly , abort.
if ( !defined( 'ABSPATH' ) ) { 
    exit; 
}

// constants
define ('PLUGIN_DIR_PATH',plugin_dir_path(__FILE__));
define ('PLUGIN_URL',plugins_url());
define ('PLUGIN_VERSION','1.0');

function add_csv_import_admin_menu()
{
	/*add_menu_page(
        'csvimport',             // page title
        'Csv Import',            // menu title
        'manage_options',           // capability
        'add-images',               // menu slug
        'csv_import_add_new_images',    // callback function
        'dashicons-format-image',   // icon url
        71                          // menu position
    );*/

 //    add_submenu_page(
 //        'add-images',               // parent menu slug
 //        'Add New Images',           // page title
 //        'Add New Images',         // menu title
 //        'manage_options',        // capability
 //        'add-images',            // menu slug
 //        'csv_import_add_new_images' // callback function
 //    );
	add_submenu_page( 
	'woocommerce', 
	'csvimport', 
	'Csv Import', 
	'manage_options', 
	'import-csv-file', 
	'csv_import_add_new_images' ); 
 
}

// add plugin with hook here
add_action('admin_menu','add_csv_import_admin_menu');

function csv_import_add_new_images()
{   
    // includes add new images file
    include_once PLUGIN_DIR_PATH.'/views/add_csv.php';
}


function add_csv_import_assets()
{
    wp_enqueue_script(
        'csv_ajax',                                          // unique name for js file
        PLUGIN_URL.'/csv-import/assets/js/ajax.js',  // js file path
        '', // dependencies on other file                                  // plugin version
        '',// Version
        true  // In footer
    );
    wp_localize_script('csv_ajax','csv_ajax_url', array( 'ajax_url' => admin_url('admin-ajax.php')));
}
add_action('init','add_csv_import_assets');
// generating csv import plugin table code
function csv_import_file_table(){
	global $wpdb;
	$table_name = $wpdb->prefix . "import";
	$charset_collate = $wpdb->get_charset_collate();
	$sql = "CREATE TABLE IF NOT EXISTS $table_name (
	  id mediumint(9) NOT NULL AUTO_INCREMENT,
	  product varchar(255) DEFAULT '' NOT NULL,
	  price mediumint(9) NOT NULL,
	  sku varchar(255) DEFAULT '' NOT NULL,
	  inventory mediumint(9) NOT NULL,
	  update_at timestamp DEFAULT '0000-00-00 00:00:00' NOT NULL,
	  PRIMARY KEY  (id)
	) $charset_collate;";
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );
}
register_activation_hook( __FILE__, 'csv_import_file_table' );

function deactivate_table()
{
    global $wpdb;
    $wpdb->query('Drop Table If Exists "wp_import"');
}

register_uninstall_hook(__FILE__,'deactivate_table');

function csv_import_ajax()
{
	wp_die();
}

add_action('wp_ajax_csv_import_ajax','csv_import_ajax');
add_action('wp_ajax_nopriv_csv_import_ajax','csv_import_ajax');


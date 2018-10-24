<?php
/**
 * WordPress Book List CustomAutofill Extension
 *
 * @package     WordPress Book List CustomAutofill Extension
 * @author      Jake Evans
 * @copyright   2018 Jake Evans
 * @license     GPL-2.0+
 *
 * @wordpress-plugin
 * Plugin Name: WPBookList CustomAutofill Extension
 * Plugin URI: https://www.jakerevans.com
 * Description: A Boilerplate Extension for WPBookList that creates a menu page and has it's own tabs.
 * Version: 0.0.1
 * Author: Jake Evans
 * Text Domain: wpbooklist
 * Author URI: https://www.jakerevans.com
 */

/*
 * SETUP NOTES:
 *
 * Change all filename instances from customautofill to desired plugin name
 *
 * Modify Plugin Name
 *
 * Modify Description
 *
 * Modify Version Number in Block comment and in Constant
 *
 * Find & Replace these 3 strings:
 * customautofill
 * CustomAutofill
 * CUSTOMAUTOFILL
 *
 * Install Gulp & all Plugins listed in gulpfile.js
 *
 *
 *
 *
 *
 */




// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $wpdb;

/* REQUIRE STATEMENTS */
	require_once 'includes/class-customautofill-general-functions.php';
	require_once 'includes/class-customautofill-ajax-functions.php';
/* END REQUIRE STATEMENTS */

/* CONSTANT DEFINITIONS */

	// Extension version number.
	define( 'CUSTOMAUTOFILL_VERSION_NUM', '0.0.1' );

	// Root plugin folder directory.
	define( 'CUSTOMAUTOFILL_ROOT_DIR', plugin_dir_path( __FILE__ ) );

	// Root WordPress Plugin Directory.
	define( 'CUSTOMAUTOFILL_ROOT_WP_PLUGINS_DIR', str_replace( '/wpbooklist-customautofill', '', plugin_dir_path( __FILE__ ) ) );

	// Root plugin folder URL .
	define( 'CUSTOMAUTOFILL_ROOT_URL', plugins_url() . '/wpbooklist-customautofill/' );

	// Root Classes Directory.
	define( 'CUSTOMAUTOFILL_CLASS_DIR', CUSTOMAUTOFILL_ROOT_DIR . 'includes/classes/' );

	// Root REST Classes Directory.
	define( 'CUSTOMAUTOFILL_CLASS_REST_DIR', CUSTOMAUTOFILL_ROOT_DIR . 'includes/classes/rest/' );

	// Root Compatability Classes Directory.
	define( 'CUSTOMAUTOFILL_CLASS_COMPAT_DIR', CUSTOMAUTOFILL_ROOT_DIR . 'includes/classes/compat/' );

	// Root Translations Directory.
	define( 'CUSTOMAUTOFILL_CLASS_TRANSLATIONS_DIR', CUSTOMAUTOFILL_ROOT_DIR . 'includes/classes/translations/' );

	// Root Transients Directory.
	define( 'CUSTOMAUTOFILL_CLASS_TRANSIENTS_DIR', CUSTOMAUTOFILL_ROOT_DIR . 'includes/classes/transients/' );

	// Root Image URL.
	define( 'CUSTOMAUTOFILL_ROOT_IMG_URL', CUSTOMAUTOFILL_ROOT_URL . 'assets/img/' );

	// Root Image Icons URL.
	define( 'CUSTOMAUTOFILL_ROOT_IMG_ICONS_URL', CUSTOMAUTOFILL_ROOT_URL . 'assets/img/icons/' );

	// Root CSS URL.
	define( 'CUSTOMAUTOFILL_CSS_URL', CUSTOMAUTOFILL_ROOT_URL . 'assets/css/' );

	// Root JS URL.
	define( 'CUSTOMAUTOFILL_JS_URL', CUSTOMAUTOFILL_ROOT_URL . 'assets/js/' );

	// Root UI directory.
	define( 'CUSTOMAUTOFILL_ROOT_INCLUDES_UI', CUSTOMAUTOFILL_ROOT_DIR . 'includes/ui/' );

	// Root UI Admin directory.
	define( 'CUSTOMAUTOFILL_ROOT_INCLUDES_UI_ADMIN_DIR', CUSTOMAUTOFILL_ROOT_DIR . 'includes/ui/admin/' );

	// Define the Uploads base directory.
	$uploads     = wp_upload_dir();
	$upload_path = $uploads['basedir'];
	define( 'CUSTOMAUTOFILL_UPLOADS_BASE_DIR', $upload_path . '/' );

	// Define the Uploads base URL.
	$upload_url = $uploads['baseurl'];
	define( 'CUSTOMAUTOFILL_UPLOADS_BASE_URL', $upload_url . '/' );

	// Nonces array.
	define( 'CUSTOMAUTOFILL_NONCES_ARRAY',
		wp_json_encode(array(
			'adminnonce1' => 'wpbooklist_customautofill_get_book_action_callback',
		))
	);

/* END OF CONSTANT DEFINITIONS */

/* MISC. INCLUSIONS & DEFINITIONS */

	// Loading textdomain.
	load_plugin_textdomain( 'wpbooklist', false, CUSTOMAUTOFILL_ROOT_DIR . 'languages' );

/* END MISC. INCLUSIONS & DEFINITIONS */

/* CLASS INSTANTIATIONS */

	// Call the class found in wpbooklist-functions.php.
	$customautofill_general_functions = new CustomAutofill_General_Functions();

	// Call the class found in wpbooklist-functions.php.
	$customautofill_ajax_functions = new CustomAutofill_Ajax_Functions();


/* END CLASS INSTANTIATIONS */


/* FUNCTIONS FOUND IN CLASS-WPBOOKLIST-GENERAL-FUNCTIONS.PHP THAT APPLY PLUGIN-WIDE */

	// Function that loads up the menu page entry for this Extension.
	add_filter( 'wpbooklist_add_sub_menu', array( $customautofill_general_functions, 'wpbooklist_customautofill_submenu' ) );

	// Adding the function that will take our CUSTOMAUTOFILL_NONCES_ARRAY Constant from above and create actual nonces to be passed to Javascript functions.
	add_action( 'init', array( $customautofill_general_functions, 'wpbooklist_customautofill_create_nonces' ) );

	// Function to run any code that is needed to modify the plugin between different versions.
	add_action( 'plugins_loaded', array( $customautofill_general_functions, 'wpbooklist_customautofill_update_upgrade_function' ) );

	// Adding the admin js file.
	add_action( 'admin_enqueue_scripts', array( $customautofill_general_functions, 'wpbooklist_customautofill_admin_js' ) );

	// Adding the frontend js file.
	add_action( 'wp_enqueue_scripts', array( $customautofill_general_functions, 'wpbooklist_customautofill_frontend_js' ) );

	// Adding the admin css file for this extension.
	add_action( 'admin_enqueue_scripts', array( $customautofill_general_functions, 'wpbooklist_customautofill_admin_style' ) );

	// Adding the Front-End css file for this extension.
	add_action( 'wp_enqueue_scripts', array( $customautofill_general_functions, 'wpbooklist_customautofill_frontend_style' ) );

	// Function to add table names to the global $wpdb.
	add_action( 'admin_footer', array( $customautofill_general_functions, 'wpbooklist_customautofill_register_table_name' ) );

	// Function to run any code that is needed to modify the plugin between different versions.
	add_action( 'admin_footer', array( $customautofill_general_functions, 'wpbooklist_customautofill_admin_pointers_javascript' ) );

	// Creates tables upon activation.
	register_activation_hook( __FILE__, array( $customautofill_general_functions, 'wpbooklist_customautofill_create_tables' ) );

	// Runs once upon extension activation and adds it's version number to the 'extensionversions' column in the 'wpbooklist_jre_user_options' table of the core plugin.
	register_activation_hook( __FILE__, array( $customautofill_general_functions, 'wpbooklist_customautofill_record_extension_version' ) );



/* END OF FUNCTIONS FOUND IN CLASS-WPBOOKLIST-GENERAL-FUNCTIONS.PHP THAT APPLY PLUGIN-WIDE */

/* FUNCTIONS FOUND IN CLASS-WPBOOKLIST-AJAX-FUNCTIONS.PHP THAT APPLY PLUGIN-WIDE */

	// For receiving user feedback upon deactivation & deletion.
	add_action( 'wp_ajax_wpbooklist_customautofill_get_book_action', array( $customautofill_ajax_functions, 'wpbooklist_customautofill_get_book_action_callback' ) );

	// For receiving user feedback upon deactivation & deletion.
	add_action( 'wp_ajax_customautofill_exit_results_action', array( $customautofill_ajax_functions, 'customautofill_exit_results_action_callback' ) );

/* END OF FUNCTIONS FOUND IN CLASS-WPBOOKLIST-AJAX-FUNCTIONS.PHP THAT APPLY PLUGIN-WIDE */























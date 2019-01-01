<?php
/**
 * Class CustomAutofill_General_Functions - class-customautofill-general-functions.php
 *
 * @author   Jake Evans
 * @category Admin
 * @package  Includes
 * @version  6.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'CustomAutofill_General_Functions', false ) ) :
	/**
	 * CustomAutofill_General_Functions class. Here we'll do things like enqueue scripts/css, set up menus, etc.
	 */
	class CustomAutofill_General_Functions {

		/** Function that sets the book's stock avaialbilty and sets the manage_stock option.
		 *
		 *  @param object $post - The post object.
		 */
		public function jre_custom_add_stock_on_product_creation( $product_id, $post, $update ) {

			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
				return;
			} else {

				if ( 'product' === $post->post_type ) {
					if ( isset( $_POST['wcv_custom_product_stock'] ) ) {
						$wcv_custom_product_stock = wp_unslash( $_POST['wcv_custom_product_stock'] );
						error_log('$wcv_custom_product_stock is: ' . $wcv_custom_product_stock);
						update_post_meta( $post->ID, '_manage_stock', 'yes' );

						// If user didn't enter a quantity amount, set to 1.
						if ( '' === $wcv_custom_product_stock || null === $wcv_custom_product_stock || 0 === $wcv_custom_product_stock || '0' === $wcv_custom_product_stock ) {
							update_post_meta( $post->ID, '_stock', 1 );
						} else {
							update_post_meta( $post->ID, '_stock', $wcv_custom_product_stock );
						}
					}
				}
			}
		}

		/** Functions that loads up the menu page entry for this Extension.
		 *
		 *  @param array $submenu_array - The array that contains submenu entries to add to.
		 */
		public function wpbooklist_customautofill_submenu( $submenu_array ) {
			$extra_submenu = array(
				'CustomAutofill',
			);

			// Combine the two arrays.
			$submenu_array = array_merge( $submenu_array, $extra_submenu );
			return $submenu_array;
		}

		/**
		 *  Here we take the Constant defined in wpbooklist.php that holds the values that all our nonces will be created from, we create the actual nonces using wp_create_nonce, and the we define our new, final nonces Constant, called WPBOOKLIST_FINAL_NONCES_ARRAY.
		 */
		public function wpbooklist_customautofill_create_nonces() {

			$temp_array = array();
			foreach ( json_decode( CUSTOMAUTOFILL_NONCES_ARRAY ) as $key => $noncetext ) {
				$nonce              = wp_create_nonce( $noncetext );
				$temp_array[ $key ] = $nonce;
			}

			// Defining our final nonce array.
			define( 'CUSTOMAUTOFILL_FINAL_NONCES_ARRAY', wp_json_encode( $temp_array ) );

		}

		/**
		 *  Runs once upon extension activation and adds it's version number to the 'extensionversions' column in the 'wpbooklist_jre_user_options' table of the core plugin.
		 */
		public function wpbooklist_customautofill_record_extension_version() {
			global $wpdb;
			$existing_string = $wpdb->get_row( 'SELECT * from ' . $wpdb->prefix . 'wpbooklist_jre_user_options' );

			// Check to see if Extension is already registered.
			if ( false !== strpos( $existing_string->extensionversions, 'customautofill' ) ) {
				$split_string = explode( 'customautofill', $existing_string->extensionversions );
				$first_part   = $split_string[0];
				$last_part    = substr( $split_string[1], 5 );
				$new_string   = $first_part . 'customautofill' . CUSTOMAUTOFILL_VERSION_NUM . $last_part;
			} else {
				$new_string = $existing_string->extensionversions . 'customautofill' . CUSTOMAUTOFILL_VERSION_NUM;
			}

			$data         = array(
				'extensionversions' => $new_string,
			);
			$format       = array( '%s' );
			$where        = array( 'ID' => 1 );
			$where_format = array( '%d' );
			$wpdb->update( $wpdb->prefix . 'wpbooklist_jre_user_options', $data, $where, $format, $where_format );

		}

		/**
		 *  Function to run the compatability code in the Compat class for upgrades/updates, if stored version number doesn't match the defined global in wpbooklist-customautofill.php
		 */
		public function wpbooklist_customautofill_update_upgrade_function() {

			// Get current version #.
			global $wpdb;
			$existing_string = $wpdb->get_row( 'SELECT * from ' . $wpdb->prefix . 'wpbooklist_jre_user_options' );

			// Check to see if Extension is already registered and matches this version.
			if ( false !== strpos( $existing_string->extensionversions, 'customautofill' ) ) {
				$split_string = explode( 'customautofill', $existing_string->extensionversions );
				$version      = substr( $split_string[1], 0, 5 );

				// If version number does not match the current version number found in wpbooklist.php, call the Compat class and run upgrade functions.
				if ( CUSTOMAUTOFILL_VERSION_NUM !== $version ) {
					require_once CUSTOMAUTOFILL_CLASS_COMPAT_DIR . 'class-customautofill-compat-functions.php';
					$compat_class = new CustomAutofill_Compat_Functions();
				}
			}
		}

		/**
		 * Adding the admin js file
		 */
		public function wpbooklist_customautofill_admin_js() {

			wp_register_script( 'wpbooklist_customautofill_adminjs', CUSTOMAUTOFILL_JS_URL . 'wpbooklist_customautofill_admin.min.js', array( 'jquery' ), WPBOOKLIST_VERSION_NUM, true );

			// Next 4-5 lines are required to allow translations of strings that would otherwise live in the wpbooklist-admin-js.js JavaScript File.
			require_once CUSTOMAUTOFILL_CLASS_TRANSLATIONS_DIR . 'class-wpbooklist-customautofill-translations.php';
			$trans = new WPBookList_CustomAutofill_Translations();

			// Localize the script with the appropriate translation array from the Translations class.
			$translation_array1 = $trans->trans_strings();

			// Now grab all of our Nonces to pass to the JavaScript for the Ajax functions and merge with the Translations array.
			$final_array_of_php_values = array_merge( $translation_array1, json_decode( CUSTOMAUTOFILL_FINAL_NONCES_ARRAY, true ) );

			// Adding some other individual values we may need.
			$final_array_of_php_values['CUSTOMAUTOFILL_ROOT_IMG_ICONS_URL'] = CUSTOMAUTOFILL_ROOT_IMG_ICONS_URL;
			$final_array_of_php_values['CUSTOMAUTOFILL_ROOT_IMG_URL']       = CUSTOMAUTOFILL_ROOT_IMG_URL;
			$final_array_of_php_values['FOR_TAB_HIGHLIGHT']                         = admin_url() . 'admin.php';
			$final_array_of_php_values['SAVED_ATTACHEMENT_ID']                      = get_option( 'media_selector_attachment_id', 0 );

			// Now registering/localizing our JavaScript file, passing all the PHP variables we'll need in our $final_array_of_php_values array, to be accessed from 'wphealthtracker_php_variables' object (like wphealthtracker_php_variables.nameofkey, like any other JavaScript object).
			wp_localize_script( 'wpbooklist_customautofill_adminjs', 'wpbooklistCustomAutofillPhpVariables', $final_array_of_php_values );

			wp_enqueue_script( 'wpbooklist_customautofill_adminjs' );

			return $final_array_of_php_values;

		}

		/**
		 * Adding the frontend js file
		 */
		public function wpbooklist_customautofill_frontend_js() {

			wp_register_script( 'wpbooklist_customautofill_frontendjs', CUSTOMAUTOFILL_JS_URL . 'wpbooklist_customautofill_frontend.min.js', array( 'jquery' ), CUSTOMAUTOFILL_VERSION_NUM, true );

			// Next 4-5 lines are required to allow translations of strings that would otherwise live in the wpbooklist-admin-js.js JavaScript File.
			require_once CUSTOMAUTOFILL_CLASS_TRANSLATIONS_DIR . 'class-wpbooklist-customautofill-translations.php';
			$trans = new WPBookList_CustomAutofill_Translations();

			// Localize the script with the appropriate translation array from the Translations class.
			$translation_array1 = $trans->trans_strings();

			// Now grab all of our Nonces to pass to the JavaScript for the Ajax functions and merge with the Translations array.
			$final_array_of_php_values = array_merge( $translation_array1, json_decode( CUSTOMAUTOFILL_FINAL_NONCES_ARRAY, true ) );

			// Adding some other individual values we may need.
			$final_array_of_php_values['CUSTOMAUTOFILL_ROOT_IMG_ICONS_URL'] = CUSTOMAUTOFILL_ROOT_IMG_ICONS_URL;
			$final_array_of_php_values['CUSTOMAUTOFILL_ROOT_IMG_URL']       = CUSTOMAUTOFILL_ROOT_IMG_URL;

			// Now registering/localizing our JavaScript file, passing all the PHP variables we'll need in our $final_array_of_php_values array, to be accessed from 'wphealthtracker_php_variables' object (like wphealthtracker_php_variables.nameofkey, like any other JavaScript object).
			wp_localize_script( 'wpbooklist_customautofill_frontendjs', 'wpbooklistCustomAutofillPhpVariables', $final_array_of_php_values );

			wp_enqueue_script( 'wpbooklist_customautofill_frontendjs' );

			return $final_array_of_php_values;

		}

		/**
		 * Adding the admin css file
		 */
		public function wpbooklist_customautofill_admin_style() {

			wp_register_style( 'wpbooklist_customautofill_adminui', CUSTOMAUTOFILL_CSS_URL . 'wpbooklist-customautofill-main-admin.css', null, CUSTOMAUTOFILL_VERSION_NUM );
			wp_enqueue_style( 'wpbooklist_customautofill_adminui' );

		}

		/**
		 * Adding the frontend css file
		 */
		public function wpbooklist_customautofill_frontend_style() {

			wp_register_style( 'wpbooklist_customautofill_frontendui', CUSTOMAUTOFILL_CSS_URL . 'wpbooklist-customautofill-main-frontend.css', null, CUSTOMAUTOFILL_VERSION_NUM );
			wp_enqueue_style( 'wpbooklist_customautofill_frontendui' );

		}

		/**
		 *  Function to add table names to the global $wpdb.
		 */
		public function wpbooklist_customautofill_register_table_name() {
			global $wpdb;
			//$wpdb->wpbooklist_jre_saved_book_log = "{$wpdb->prefix}wpbooklist_jre_saved_book_log";
		}

		/**
		 *  Function that calls the Style and Scripts needed for displaying of admin pointer messages.
		 */
		public function wpbooklist_customautofill_admin_pointers_javascript() {
			wp_enqueue_style( 'wp-pointer' );
			wp_enqueue_script( 'wp-pointer' );
			wp_enqueue_script( 'utils' );
		}

		/**
		 *  Runs once upon plugin activation and creates the table that holds info on WPBookList Pages & Posts.
		 */
		public function wpbooklist_customautofill_create_tables() {
			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
			global $wpdb;
			global $charset_collate;

			/*
			// Call this manually as we may have missed the init hook.
			$this->wpbooklist_customautofill_register_table_name();

			$sql_create_table1 = "CREATE TABLE {$wpdb->wpbooklist_customautofill}
			(
				ID bigint(190) auto_increment,
				getstories bigint(255),
				createpost bigint(255),
				createpage bigint(255),
				storypersist bigint(255),
				deletedefault bigint(255),
				notifydismiss bigint(255) NOT NULL DEFAULT 1,
				newnotify bigint(255) NOT NULL DEFAULT 1,
				notifymessage MEDIUMTEXT,
				storytimestylepak varchar(255) NOT NULL DEFAULT 'default',
				PRIMARY KEY  (ID),
				KEY getstories (getstories)
			) $charset_collate; ";
			dbDelta( $sql_create_table1 );
			*/
		}

	}
endif;

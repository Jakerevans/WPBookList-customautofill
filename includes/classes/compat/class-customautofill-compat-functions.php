<?php
/**
 * Class CustomAutofill_Compat_Functions - class-customautofill-compat-functions.php
 *
 * @author   Jake Evans
 * @category Admin
 * @package  Includes/Classes/Compat
 * @version  6.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'CustomAutofill_Compat_Functions', false ) ) :
	/**
	 * CustomAutofill_Compat_Functions class. Here we'll run functions that make older versions of WPBookList compatible with newest version
	 */
	class CustomAutofill_Compat_Functions {


		/** Common member variable
		 *
		 *  @var string $new_string
		 */
		public $new_string = '';

		/**
		 *  Simply sets the version number for the class
		 */
		public function __construct() {

			// Rebuild version number string.
			global $wpdb;
			$row              = $wpdb->get_row( 'SELECT * FROM ' . $wpdb->prefix . 'wpbooklist_jre_user_options' );
			$split_string     = explode( 'customautofill', $row->extensionversions );
			$first_part       = $split_string[0];
			$last_part        = substr( $split_string[1], 5 );
			$this->new_string = $first_part . 'customautofill' . CUSTOMAUTOFILL_VERSION_NUM . $last_part;

			// Now call the function that will update the version number, which will ensure none of these function ever run again until the next update/upgrade.
			$this->customautofill_update_version_number_function();

		}

		/**
		 *  Function to update the version number.
		 */
		public function customautofill_update_version_number_function() {

			global $wpdb;
			$data         = array(
				'extensionversions' => $this->new_string,
			);
			$format       = array( '%s' );
			$where        = array( 'ID' => 1 );
			$where_format = array( '%d' );
			$wpdb->update( $wpdb->prefix . 'wpbooklist_jre_user_options', $data, $where, $format, $where_format );
		}
	}
endif;

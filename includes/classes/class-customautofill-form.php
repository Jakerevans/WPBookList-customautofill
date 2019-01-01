<?php
/**
 * WPBookList WPBookList_CustomAutofill_Form Submenu Class
 *
 * @author   Jake Evans
 * @category ??????
 * @package  ??????
 * @version  1
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'WPBookList_CustomAutofill_Form', false ) ) :
/**
 * WPBookList_CustomAutofill_Form Class.
 */
class WPBookList_CustomAutofill_Form {

	public static function output_customautofill_form(){

		global $wpdb;
	
		// For grabbing an image from media library
		wp_enqueue_media();

		$string1 = '';
		
    	return $string1;
	}
}

endif;
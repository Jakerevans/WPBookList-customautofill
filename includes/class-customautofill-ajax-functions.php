<?php
/**
 * Class CustomAutofill_Ajax_Functions - class-wpbooklist-ajax-functions.php
 *
 * @author   Jake Evans
 * @category Admin
 * @package  Includes
 * @version  6.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'CustomAutofill_Ajax_Functions', false ) ) :
	/**
	 * CustomAutofill_Ajax_Functions class. Here we'll do things like enqueue scripts/css, set up menus, etc.
	 */
	class CustomAutofill_Ajax_Functions {

		/**
		 * Class Constructor - Simply calls the Translations
		 */
		public function __construct() {

		}

		/**
		 * Function to get book info based on isbn.
		 */
		public function wpbooklist_customautofill_get_book_action_callback() {

			global $wpdb;
			check_ajax_referer( 'wpbooklist_customautofill_get_book_action_callback', 'security' );

			// First set the variables we'll be passing to class-wpbooklist-book.php to ''.
			if ( isset( $_POST['isbn'] ) ) {
				$isbn = filter_var( wp_unslash( $_POST['isbn'] ), FILTER_SANITIZE_STRING );
			}

			$book_array = array(
				'amazonauth'      => 'true',
				'use_amazon_yes'  => 'true',
				'amazon_auth_yes' => 'true',
				'isbn'            => $isbn,
			);

			if ( file_exists( CLASS_DIR . 'class-book.php' ) ) {
				require_once CLASS_DIR . 'class-book.php';
			}

			if ( file_exists( CLASS_BOOK_DIR . 'class-wpbooklist-book.php' ) ) {
				require_once CLASS_BOOK_DIR . 'class-wpbooklist-book.php';
			}

			$book_class = new WPBookList_Book( 'search', $book_array, null );


			// Get edition.
			$edition_string = '';
			if ( null === $book_class->edition || '' === $book_class->edition ) {

				if ( array_key_exists( 'Edition', $book_class->amazon_array['Items']['Item'][0]['ItemAttributes'] ) ) {
					$book_class->edition = $book_class->amazon_array['Items']['Item'][0]['ItemAttributes']['Edition'];
				}

				if ( is_array( $book_class->edition ) ) {
					foreach ( $book_class->edition as $edition ) {
						$edition_string = $edition_string . ', ' . $edition;
					}
					$edition_string = rtrim( $edition_string, ', ' );
					$edition_string = ltrim( $edition_string, ', ' );
					$book_class->edition  = $edition_string;
				}
			}



			if ( null === $book_class->edition || '' === $book_class->edition ) {
				if ( array_key_exists( 'Edition', $book_class->amazon_array['Items']['Item']['ItemAttributes'] ) ) {
					$book_class->edition = $book_class->amazon_array['Items']['Item']['ItemAttributes']['Edition'];
				}
				if ( is_array( $book_class->edition ) ) {
					foreach ( $book_class->edition as $edition ) {
						$edition_string = $edition_string . ', ' . $edition;
					}
					$edition_string = rtrim( $edition_string, ', ' );
					$edition_string = ltrim( $edition_string, ', ' );
					$book_class->edition  = $edition_string;
				}
			}







			$upload_dir = wp_upload_dir();

			error_log('$book_class->image');
			error_log($book_class->image);

			$image_data = wp_remote_get( $book_class->image );

			error_log('$image_data');
			error_log( print_r( $image_data,true ));

			// Check the response code.
			$response_code    = wp_remote_retrieve_response_code( $image_data );
			$response_message = wp_remote_retrieve_response_message( $image_data );

			if ( 200 !== $response_code && ! empty( $response_message ) ) {
				return new WP_Error( $response_code, $response_message );
			} elseif ( 200 !== $response_code ) {
				return new WP_Error( $response_code, 'Unknown error occurred with wp_remote_get() trying to get an image url in the create_page_image() function' );
			} else {
				$image_data = wp_remote_retrieve_body( $image_data );
			}

			$image_url = str_replace( '%', '', $book_class->image );
			$filename  = basename( $image_url );
			error_log('filename');
			error_log($filename);
			if ( wp_mkdir_p( $upload_dir['path'] ) ) {
				$file = $upload_dir['path'] . '/' . $filename;
			} else {
				$file = $upload_dir['basedir'] . '/' . $filename;
			}

			// Initialize the WP filesystem.
			global $wp_filesystem;
			if ( empty( $wp_filesystem ) ) {
				require_once ABSPATH . '/wp-admin/includes/file.php';
				WP_Filesystem();
			}

			$result      = $wp_filesystem->put_contents( $file, $image_data );
			$wp_filetype = wp_check_filetype( $filename, null );
			$attachment  = array(
				'post_mime_type' => $wp_filetype['type'],
				'post_title'     => sanitize_file_name( $filename ),
				'post_content'   => '',
				'post_status'    => 'inherit',
			);


			error_log( print_r( $attachment, true ) );

			$attach_id   = wp_insert_attachment( $attachment, $file );
			$attach_data = wp_generate_attachment_metadata( $attach_id, $file );



			$res1        = wp_update_attachment_metadata( $attach_id, $attach_data );

			$book_class->image_attachment_id = $attach_id;
			$book_class->image_attach_data = $attach_data;

			wp_die( wp_json_encode( $book_class ) );
		}
	}
endif;

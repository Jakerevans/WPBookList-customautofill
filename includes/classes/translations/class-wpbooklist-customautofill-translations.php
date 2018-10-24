<?php
/**
 * Class WPBookList_CustomAutofill_Translations - class-wpbooklist-translations.php
 *
 * @author   Jake Evans
 * @category Translations
 * @package  Includes/Classes/Translations
 * @version  0.0.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WPBookList_CustomAutofill_Translations', false ) ) :
	/**
	 * WPBookList_CustomAutofill_Translations class. This class will house all the translations we may ever need...
	 */
	class WPBookList_CustomAutofill_Translations {

		/**
		 * Class Constructor - Simply calls the one function to return all Translated strings.
		 */
		public function __construct() {
			$this->trans_strings();
		}

		/**
		 * All the Translations.
		 */
		public function trans_strings() {
			$this->trans_1 = __( 'Search', 'wpbooklist-textdomain' );

			// The array of translation strings.
			$translation_array = array(
				'trans1' => $this->trans_1,
			);

			return $translation_array;
		}
	}
endif;

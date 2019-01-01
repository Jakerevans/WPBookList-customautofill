<?php
/**
 * WPBookList CustomAutofill Tab
 *
 * @author   Jake Evans
 * @category Extension Ui
 * @package  Includes/UI
 * @version  6.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WPBookList_CustomAutofill', false ) ) :
	/**
	 * WPBookList_Admin_Menu Class.
	 */
	class WPBookList_CustomAutofill {

		/**
		 * Class Constructor
		 */
		public function __construct() {
			require_once CLASS_DIR . 'class-admin-ui-template.php';
			require_once CUSTOMAUTOFILL_CLASS_DIR . 'class-customautofill-form.php';

			// Get Translations.
			require_once CUSTOMAUTOFILL_CLASS_TRANSLATIONS_DIR . 'class-wpbooklist-customautofill-translations.php';
			$this->trans = new WPBookList_CustomAutofill_Translations();
			$this->trans->trans_strings();

			// Instantiate the class.
			$this->template = new WPBookList_Admin_UI_Template();
			$this->form     = new WPBookList_CustomAutofill_Form();
			$this->output_open_admin_container();
			$this->output_tab_content();
			$this->output_close_admin_container();
			$this->output_admin_template_advert();
		}

		/**
		 * Opens the admin container for the tab
		 */
		private function output_open_admin_container(){
			$title = 'CustomAutofill General Settings';
			$icon_url = CUSTOMAUTOFILL_ROOT_IMG_URL.'book.svg';
			echo $this->template->output_open_admin_container($title, $icon_url);
		}

		/**
		 * Outputs actual tab contents
		 */
		private function output_tab_content(){
			echo $this->form->output_customautofill_form();
		}

		/**
		 * Closes admin container
		 */
		private function output_close_admin_container(){
			echo $this->template->output_close_admin_container();
		}

		/**
		 * Outputs advertisment area
		 */
		private function output_admin_template_advert(){
			echo $this->template->output_template_advert();
		}


	}
endif;

// Instantiate the class
$cm = new WPBookList_CustomAutofill;

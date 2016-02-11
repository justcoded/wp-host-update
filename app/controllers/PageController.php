<?php

namespace WpHUp\controllers {

	class PageController extends \WpHUp\components\BaseController
	{
		/**
		 * Main script index.
		 * Render primary interface
		 */
		public function actionIndex()
		{
			// get tables list
			$tables = $this->wpdb->get_col("SHOW TABLES LIKE '" . $this->wpdb->prefix . "%'");
			$tables = array_combine($tables, $tables);
			
			// get wp options
			$wp_options = array(
				'home' => preg_replace('/^(http)s?\:/i', '', get_option('home')),
				'wpcontent_path' => dirname(get_option('upload_path')),
			);
			
			// define new options to suggest
			$new_options = array(
				'home' => '//'.$_SERVER['HTTP_HOST'],
				'wpcontent_path' => WP_CONTENT_DIR,
				'basepath' => '/'.trim(str_replace(basename(__FILE__), '', $_SERVER['REQUEST_URI']), '/'),
			);
			$new_options['home'] .= $new_options['basepath'];
			
			// prepare an array for view
			$default_search_replace = array(
				array(
					'Old Url', $wp_options['home'],
					'New Url', $new_options['home'],
				),
				array(
					'Old file path', $wp_options['wpcontent_path'],
					'New file path', $new_options['wpcontent_path'],
				),
				array(
					'Old value', '',
					'New value', '',
				),
			);
			
			// if we don't have in DB old filepath - then user can add it manually
			if ( empty($wp_options['wpcontent_path']) ) {
				unset($default_search_replace[1]);
			}

			$this->responseStart();
			include VIEWS_PATH . '/page/index.php';
		}
		
		/**
		 * Error action to indicate that wp-config.php file is missing
		 */
		public function actionConfigError()
		{
			$this->responseStart();
			include VIEWS_PATH . '/page/wp-conf-error.php';
		}
		
		/**
		 * Initiate replace progress
		 */
		public function actionRun()
		{
			// validate input
			if ( empty($_POST['search_replace']) || empty($_POST['tables_choice']) || empty($_POST['tables_custom']) ) {
				return $this->responseJson( array('error' => 'Server replies that request is not valid. Please try again.') );
			}
			
			// check tables sizes
			$total_rows = 0;
			foreach ( $_POST['tables_custom'] as $table ) {
				$table_rows = $this->wpdb->get_col("SELECT COUNT(*) as rowscnt FROM $table");
				$total_rows += $table_rows[0];
			}
			
			ob_start();
			include VIEWS_PATH . '/page/_progressbar.php';
			$progress_html = ob_get_clean();
			
			return $this->responseJson( array(
				'progress_max' => $total_rows,
				'progress_html' => $progress_html,
			));
		}
		
		/**
		 * Thanks page
		 */
		public function actionThanks()
		{
			sleep(1);
			$this->responseStart();
			include VIEWS_PATH . '/page/_thanks.php';
		}
		
		/*
		public function actionTest() 
		{
			$this->responseStart();
			include VIEWS_PATH . '/layouts/header.php';
			include VIEWS_PATH . '/page/_thanks.php';
			include  VIEWS_PATH . '/layouts/footer.php';
		}
		 * 
		 */
	}
	
}

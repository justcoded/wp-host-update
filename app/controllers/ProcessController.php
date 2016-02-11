<?php

namespace WpHUp\controllers {

	class ProcessController extends \WpHUp\components\BaseController
	{
		/**
		 * Main script to replace strings in tables.
		 * 
		 * All data is inside $_POST:
		 *		[search_replace] => [
		 *				[ find, replace ]
		 *				...
		 *		],
		 *		[tables_choice] => all | custom
		 *		[tables_custom] => [ // all tables which should be updated
		 *			'wp_commentmeta',
		 *			'wp_comments',
		 *			...
		 *		],
		 *		[step] => 0, // current index inside tables_custom
		 */
		public function actionIndex()
		{
			sleep(1);
			return $this->responseJson( array(
				'updated' => 30,
			));
		}
		
	}
}

<?php

namespace WpHUp\controllers {

	class IndexController extends \WpHUp\components\BaseController
	{
		public function actionIndex()
		{
			include VIEWS_PATH . '/index/index.php';
		}
	}
	
}

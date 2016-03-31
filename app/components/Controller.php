<?php

abstract class BaseController
{
	/**
	 * contains wordpress database connection
	 * 
	 * @var \wpdb
	 */
	public $wpdb;

	/**
	 * init wordpress database property to prevent using global all over the code
	 * 
	 * @global wpdb $wpdb
	 */
	public function __construct()
	{
		global $wpdb;
		$this->wpdb = $wpdb;
	}

	/**
	 * function to print correct header to browser
	 * 
	 * @param string $type Response type: html|json
	 */
	public function responseStart( $type = 'html' )
	{
		if ( headers_sent() ) return;

		switch($type) {
			case 'json':
				header('Content-Type: application/json');
				break;

			default:
				header('Content-Type: text/html; charset=utf-8');
		}
	}

	/**
	 * send json response
	 */
	public function responseJson( $data )
	{
		$this->responseStart('json');
		echo json_encode($data);
	}

	abstract public function actionIndex();

}

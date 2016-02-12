<?php

namespace WpHUp\controllers {

	class ProcessController extends \WpHUp\components\BaseController
	{

		/**
		 * Main script to replace strings in tables.
		 * 
		 * All data is inside $_POST:
		 * 		[search_replace] => [
		 * 				[ find, replace ]
		 * 				...
		 * 		],
		 * 		[tables_choice] => all | custom
		 * 		[tables_custom] => [ // all tables which should be updated
		 * 			'wp_commentmeta',
		 * 			'wp_comments',
		 * 			...
		 * 		],
		 * 		[step] => 0, // current index inside tables_custom
		 */
		public function actionIndex()
		{
			global $wpdb;
			$tables = $_POST['tables_custom'];
			$step = $_POST['step'];
			$curent_table = $tables[$step];
			$updated_tables = 0;

			$select = "SELECT " . $curent_table . ".* FROM " . $curent_table;
			$datas = $wpdb->get_results($select);

			$wpdb->query("TRUNCATE TABLE " . $curent_table . ";");
			foreach ( $datas as $row ) {
				$values = array();
				$insert = "INSERT INTO $curent_table (" . implode(',', array_keys((array) $row)) . ")  VALUES ";
				$i = 1;
				$rows_counter = count((array) $row);

				foreach ( $row as $key => $value ) {
					$value = $this->recursiveReplace($value);
					$insert .= $i == 1 ? "(" : ",";
					$insert .= "'" . $this->sqlAddslashes($value) . "'";
					$insert .= $i == $rows_counter ? ")" : "";
					$i++;
				}
				$wpdb->query($insert);
				$updated_tables++;
			}

			sleep(1);
			return $this->responseJson(array(
						'updated' => $updated_tables,
			));
		}

		public function recursiveReplace( $data, $serialized = false, $parent_serialized = false )
		{
			$is_json = false;
			if ( is_string($data) && ( $unserialized = unserialize($data) ) !== false ) {
				// PHP currently has a bug that doesn't allow you to clone the DateInterval / DatePeriod classes.
				// We skip them here as they probably won't need data to be replaced anyway
				if ( is_object($unserialized) && ( $unserialized instanceof DateInterval || $unserialized instanceof DatePeriod ) ) {
					return $data;
				}
				$data = $this->recursiveReplace($unserialized, true, true);
			}
			elseif ( is_array($data) ) {
				$_tmp = array();

				foreach ( $data as $key => $value ) {
					$_tmp[$key] = $this->recursiveReplace($value, false, $parent_serialized);
				}
				$data = $_tmp;
				unset($_tmp);
			}
			// Submitted by Tina Matter
			elseif ( is_object($data) ) {
				$_tmp = clone $data;

				foreach ( $data as $key => $value ) {
					$_tmp->$key = $this->recursiveReplace($value, false, $parent_serialized);
				}
				$data = $_tmp;
				unset($_tmp);
			}
			elseif ( $this->isJson($data, true) ) {
				$_tmp = array();
				$data = json_decode($data, true);

				foreach ( $data as $key => $value ) {
					$_tmp[$key] = $this->recursiveReplace($value, false, $parent_serialized);
				}
				$data = $_tmp;
				unset($_tmp);
				$is_json = true;
			}
			elseif ( is_string($data) ) {
				$data = $this->applyReplaces($data, $parent_serialized);
			}

			if ( $serialized )
				return serialize($data);

			if ( $is_json )
				return json_encode($data);

			return $data;
		}

		public function applyReplaces( $subject, $is_serialized = false )
		{
			$search = $_POST['search_replace'];

			foreach ( $search as $replace ) {
				$subject = str_ireplace($replace[0], $replace[1], $subject);
			}
			return $subject;
		}

		function isJson( $string, $strict = false )
		{
			$json = @json_decode($string, true);

			if ( $strict == true && !is_array($json) )
				return false;

			return !( $json == NULL || $json == false );
		}

		/**
		 * Better addslashes for SQL queries.
		 * Taken from phpMyAdmin.
		 */
		public function sqlAddslashes( $string = '' )
		{
			$string = str_replace('\\', '\\\\', $string);
			return str_replace('\'', '\\\'', $string);
		}

	}

}

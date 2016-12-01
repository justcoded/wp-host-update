<?php


class ProcessController extends BaseController
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
		set_time_limit(0);

		global $wpdb;
		$tables = $_POST['tables_custom'];
		$step = $_POST['step'];
		$to_replace = $_POST['search_replace'];
		$blogs_replace = $this->prepareBlogReplace(@$_POST['domain_replace']);
		$current_table = $tables[$step];
		$updated_tables = 0;

		$select = "SELECT " . $current_table . ".* FROM " . $current_table;
		$datas = $wpdb->get_results($select);
		$primary_keys = $wpdb->get_results("SHOW KEYS FROM `$current_table` WHERE Key_name = 'PRIMARY'");

		foreach ( $datas as $row ) {
			$update = "UPDATE $current_table SET ";
			$i = 1;

			foreach ( $row as $key => $value ) {
				if ( $primary_keys[0]->Column_name == $key ) {
					$where = " WHERE $key=$value";
					$i++;
					continue;
				}

				if ( $current_table == $wpdb->blogs || $current_table == $wpdb->site ) {
					$value = ReplaceHelper::replace($value, $blogs_replace);
				}
				else {
					$value = ReplaceHelper::recursiveReplace($value, $to_replace);
				}

				$update_values[] =  $key . "='" . sql_add_slashes($value) . "'";
				$i++;
			}
			$update .= implode(',', $update_values);
			$wpdb->query($update . $where);
			$updated_tables++;
		}
		return $this->responseJson(array(
			'updated' => $updated_tables,
		));
	}
	
	protected function prepareBlogReplace($input)
	{
		if ( empty($input) || !is_array($input) ) return array();
		foreach($input as $key => $replace) {
			$replace[0] = str_replace('*.', '', $replace[0]);
			$replace[1] = str_replace('*.', '', $replace[1]);
			$input[$key] = $replace;
		}
		return $input;
	}
}


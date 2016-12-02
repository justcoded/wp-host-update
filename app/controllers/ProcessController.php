<?php


class ProcessController extends BaseController
{
	protected $skipRules;

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

		/* @var $wpdb \wpdb */
		global $wpdb;
		$tables = $_POST['tables_custom'];
		$step = $_POST['step'];
		$to_replace = $_POST['search_replace'];
		$blogs_replace = $this->prepareBlogReplace(@$_POST['domain_replace']);
		$current_table = $tables[$step];
		$clean_table_name = strtolower(preg_replace("/^$wpdb->prefix/", '', $current_table));
		$replace_method = ($_POST['replace_method'] == 'full')? 'full' : 'simple';
		if ( $replace_method == 'simple' ) $this->prepareSkipRules();
		$updated_tables = 0;

		$select = "SELECT " . $current_table . ".* FROM " . $current_table;
		$datas = $wpdb->get_results($select);
		$primary_keys = $wpdb->get_results("SHOW KEYS FROM `$current_table` WHERE Key_name = 'PRIMARY'");

		foreach ( $datas as $row ) {
			$update = "UPDATE $current_table SET ";
			$update_values = array();
			$i = 1;

			foreach ( $row as $key => $value ) {
				if ( $primary_keys[0]->Column_name == $key ) {
					$where = " WHERE $key=$value";
					$i++;
					continue;
				}

				if ( $replace_method == 'simple' && $this->canSkipColumn($clean_table_name, $key, $value) ) {
					continue;
				}

				if ( $current_table == $wpdb->blogs || $current_table == $wpdb->site ) {
					$new_value = ReplaceHelper::replace($value, $blogs_replace);
				}
				else {
					$new_value = ReplaceHelper::recursiveReplace($value, $to_replace);
				}

				if ( strcmp($new_value, $value) == 0 ) {
					continue;
				}

				$update_values[] =  $key . "='" . sql_add_slashes($new_value) . "'";
				$i++;
			}

			if (empty($update_values)) continue;

			$update .= implode(',', $update_values);
			$wpdb->query($update . $where);
			$updated_tables++;
		}
		return $this->responseJson(array(
			'table' => $clean_table_name,
			'found' => count($datas),
			'updated' => $updated_tables,
			//'last' => !empty($update_values)? $update_values : null,
		));
	}

	/**
	 * Prepare replace strings for Multisite installation
	 * Will be used to replace inside "blogs" and "sites" tables
	 *
	 * @param array $input
	 * @return array
	 */
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

	/**
	 * Check if column can be skiped in "simple" replace method.
	 * We can skip numbers, different id and key columns
	 *
	 * @param string $table
	 * @param string $column
	 * @param string $value
	 */
	protected function canSkipColumn($table, $column, $value)
	{
		// all numeric values just skiped
		if ( is_numeric($value) || '' === $value || is_null($value) ) return true;

		// check hardcoded columns
		if ( isset($this->skipRules['tables_columns'][$table][$column]) ) return true;

		// check table names
		if ( preg_match($this->skipRules['table_name']['^'], $table) ||
			preg_match($this->skipRules['table_name']['$'], $table)
		) {
			return true;
		}

		// check column names
		if ( preg_match($this->skipRules['column_name']['^'], $column) ||
			preg_match($this->skipRules['column_name']['$'], $column)
		) {
			return true;
		}

		return false;
	}

	protected function prepareSkipRules()
	{
		// table name rules
		$table_names = array(
			'^' => array( // started with
				'term_relationships',
			),
			'$' => array( // ended with
				'_?log', '_?logs',
			),
		);

		// table columns, hardcode
		$this->skipRules['tables_columns'] = array(
			'posts' => array(
				'post_password' => 1,
				'to_ping' => 1,
				'pinged' => 1,
				'post_type' => 1,
				'post_mime_type' => 1,
			),
			'options' => array(
				'option_name' => 1,
				'autoload' => 1,
			),
			'comments' => array(
				'comment_type' => 1,
			),
			'term_taxonomy' => array(
				'taxonomy' => 1,
			),
		);

		// column name rules
		$column_names = array(
			'^' => array( // started with
				'id',
				'meta_key',
				'status',
				'date_',
				'created',
				'hash',
				'md5',
			),
			'$' => array( // ended with
				'_id',
				'_status',
				'_date',
				'_date_gmt',
				'_modified',
				'_modified_gmt',
				'_md5',
				'_hash',
			),
		);

		// prepare regexps
		$this->skipRules['table_name']['^'] = '/^(' . implode('|', $table_names['^']) . ')/i';
		$this->skipRules['table_name']['$'] = '/(' . implode('|', $table_names['$']) . ')$/i';

		$this->skipRules['column_name']['^'] = '/^(' . implode('|', $column_names['^']) . ')/i';
		$this->skipRules['column_name']['$'] = '/(' . implode('|', $column_names['$']) . ')$/i';
	}
}


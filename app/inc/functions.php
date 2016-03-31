<?php

// print helper function
if( ! function_exists('pa') ) :
function pa($mixed, $stop = false) {
	$ar = debug_backtrace(); $key = pathinfo($ar[0]['file']); $key = $key['basename'].':'.$ar[0]['line'];
	$print = array($key => $mixed); print( '<pre>' . htmlentities(print_r($print,1), ENT_QUOTES, 'UTF-8') . '</pre>' );
	if($stop == 1) exit();
}
endif;

function find_wp_abspath() {
	if ( !defined('WP_CONFIG_PATH') ) {
		throw new Exception('find_wp_directory() : WP_CONFIG_PATH is not defined.');
	}
	
	$wp_conf_dir = dirname(WP_CONFIG_PATH);
	if ( is_file("$wp_conf_dir/wp-settings.php") ) 
		return "$wp_conf_dir/";
	
	$entries = scandir($wp_conf_dir);
	foreach ($entries as $entry) {
		if ( $entry == '.' || $entry == '..' || !is_dir("$wp_conf_dir/$entry") ) continue;
		if ( is_file("$wp_conf_dir/$entry/wp-settings.php") ) {
			return "$wp_conf_dir/$entry/";
		}
	}
	return false;
}

/**
 * helper func to print html <option> tags
 * 
 * @param array        $options		(value,label) pairs to be converted to options tags
 * @param string|array $selected   selected value/values. If array passed - then assuming this is multiple select
 */
function html_options( $options, $selected = null ) {
	if ( !is_array($options) || empty($options) ) 
		return;
	
	$html = '';
	foreach ($options as $value => $label) {
		$selected_attr = '';
		if ( (!is_array($selected) && strcmp($selected, $value) == 0)
				|| (is_array($selected) && in_array($value, $selected)) ) {
			$selected_attr = ' selected="selected"';
		}
		
		$html .= '<option value="' . html_encode($value) . '">' . html_encode($label) . '</option>' . "\n";
	}
	return $html;
}

/**
 * Escapes text to prevent injections
 * 
 * @param string $value
 * @return string
 */
function html_encode( $value ) {
	return htmlentities($value, ENT_QUOTES, 'UTF-8');
}

/**
 * Better addslashes for SQL queries.
 * Taken from phpMyAdmin.
 */
function sql_add_slashes( $string = '' ) {
	$string = str_replace('\\', '\\\\', $string);
	return str_replace('\'', '\\\'', $string);
}

/**
 * @param string $string
 * @param boolean $strict
 * @return boolean
 */
function is_json( $string, $strict = false ) {
	$json = @json_decode($string, true);

	if ( $strict == true && !is_array($json) )
		return false;

	return !( $json == NULL || $json == false );
}
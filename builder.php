<?php

/**
 * Special script generator. Convert multi-file app in one scingle script
 */
define('ROOT_DIR', dirname(__FILE__));
define('APP_PATH', ROOT_DIR . '/app');
define('BUILD_DIR', ROOT_DIR . '/build');
define('VIEWS_PATH', APP_PATH . '/views');

// print helper function
if ( !function_exists('pa') ) :

	function pa( $mixed, $stop = false )
	{
		$ar = debug_backtrace();
		$key = pathinfo($ar[0]['file']);
		$key = $key['basename'] . ':' . $ar[0]['line'];
		$print = array( $key => $mixed );
		print( '<pre>' . htmlentities(print_r($print, 1), ENT_QUOTES, 'UTF-8') . '</pre>');
		if ( $stop == 1 )
			exit();
	}

endif;

function get_content( $file, $content = '', $include_path = null )
{
	$pattern = '/include [a-zA-Z\.\/\_\'\- ]*;/';
	$views_pattern = '/include( *)VIEWS_PATH[a-zA-Z\.\/\_\'\- ]*;/';

	if ( !is_dir($file) ) {
		$file_content = file_get_contents($file);
		preg_match_all($pattern, $file_content, $matches);
		
		if ( !empty($content) ) {
			if ( !preg_match($views_pattern, $include_path) ) {
				$file_content = str_replace('<?php', '', str_replace('?\>', '', $file_content));
			}
			else {
				$file_content = '?' . '>' . $file_content . '<' . '?' . 'php';
			}
		}
		else {
			$content = $file_content;
		}

		if ( empty($matches[0]) ) {
			return str_replace($include_path, $file_content, $content);
		}

		foreach ( $matches[0] as $path ) {
			$new_path = change_path($path);
			$content = str_replace($include_path, $file_content, $content);
			$content = get_content($new_path, $content, $path);
		}
		return str_replace('<?php', '<' . '?' . 'php', str_replace('?>', '?' . '>', $content));
	}
}

function change_path( $path )
{
	$new_path = str_replace('include ', '', $path);
	$new_path = str_replace('APP_PATH . ', APP_PATH, trim($new_path));
	$new_path = str_replace('VIEWS_PATH . ', VIEWS_PATH, trim($new_path));
	$new_path = str_replace(';', '', $new_path);
	$new_path = str_replace('\'', '', $new_path);
	return $new_path;
}

$builder_content = get_content(APP_PATH . '/index.php');
file_put_contents(BUILD_DIR . '/wp-host-update.php', $builder_content);

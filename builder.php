<?php

/**
 * Special script generator. Convert multi-file app in one single script
 */
define('ROOT_DIR', dirname(__FILE__));
define('APP_PATH', ROOT_DIR . '/app');
define('BUILD_DIR', ROOT_DIR . '/build');
define('VIEWS_PATH', APP_PATH . '/views');

include(ROOT_DIR . '/minify/js/ClosureCompiler.php');

// print helper function
if ( !function_exists('pa') ) :
	function pa( $mixed, $stop = false ) {
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
	global $all_scripts;
	global $all_styles;
	$include_pattern = '/include [a-zA-Z\.\/\_\'\- ]*;/';
	$views_pattern = '/include( *)VIEWS_PATH[a-zA-Z\.\/\_\'\- ]*;/';
	$script_pattern = '/<script src="[a-zA-Z\.\/\_\'\-]*"><\/script>/';
	$style_pattern = '/<link rel="stylesheet" href="[a-zA-Z\.\/\_\'\-]*"(.*?)\/>/';

	$file_content = file_get_contents($file);
	preg_match_all($include_pattern, $file_content, $includes);

	if ( !empty($content) ) {
		
		if ( !preg_match($views_pattern, $include_path) ) {
			$file_content = str_replace(array( '<?php', '?>' ), '', $file_content);
		}
		
		if ( preg_match($views_pattern, $include_path) ) {
			$file_content = '?' . '>' . $file_content . '<' . '?' . 'php';
		}

		preg_match_all($script_pattern, $file_content, $scripts);
		preg_match_all($style_pattern, $file_content, $styles);

		if ( !empty($scripts[0]) ) {
			foreach ( $scripts[0] as $script ) {
				$script_path = str_replace(array( '></script>', '<script src=', '"' ), '', $script);
				$all_scripts[trim($script_path)] = '<script>';
				$all_scripts[trim($script_path)] .= Minify_JS_ClosureCompiler::minify(file_get_contents(APP_PATH . '/' . trim($script_path)));
				//$all_scripts[trim($script_path)] .= file_get_contents(APP_PATH . '/' . trim($script_path));
				$all_scripts[trim($script_path)] .= '</script>';
				$file_content = str_replace($script, '<?php global $wphu_assets; echo stripslashes($wphu_assets["js"]["' . trim($script_path) . '"]); ?>', $file_content);
			}
		}

		if ( !empty($styles[0]) ) {
			foreach ( $styles[0] as $style ) {
				$style_path = str_replace(array( '/>', '<link rel="stylesheet" href=', '"' ), '', $style);
				$all_styles[trim($style_path)] = '<style>';
				$all_styles[trim($style_path)] .= preg_replace('/(\\n|\\t|\\s)/', '', file_get_contents(APP_PATH . '/' . trim($style_path)));
				$all_styles[trim($style_path)] .= '</style>';
				$file_content = str_replace($style, '<?php global $wphu_assets; echo stripslashes($wphu_assets["css"]["' . trim($style_path) . '"]); ?>', $file_content);
			}
		}
	}
	else {
		$content = str_replace(array( '<?php', '?>' ), '', $file_content);
	}

	if ( empty($includes[0]) ) {
		return str_replace($include_path, $file_content, $content);
	}

	foreach ( $includes[0] as $path ) {
		$new_path = change_path($path);
		$content = str_replace($include_path, $file_content, $content);
		$content = get_content($new_path, $content, $path);
	}

	$content = preg_replace('@\\s*/\\*([\\s\\S]*?)\\*/\\s*@', '' . "\n", $content);
	$content = preg_replace('/<!--(.*?)-->/', '', $content);
	$content = preg_replace('/(\/\/([^\'\"])+?)$/m', '', $content);
	$content = preg_replace('/(\\s+?)$/m', '', $content);
	return $content;
}

function change_path( $path )
{
	$new_path = str_replace(array( 'include ', ';', '\'' ), '', $path);
	$new_path = str_replace('APP_PATH . ', APP_PATH, trim($new_path));
	$new_path = str_replace('VIEWS_PATH . ', VIEWS_PATH, trim($new_path));
	return $new_path;
}

$builder_content = get_content( APP_PATH . '/index.php' );
$builder = '<?php global $wphu_assets; $wphu_assets["js"] = array(';

foreach ( $all_scripts as $key => $script ) {
	$builder .= '"' . addslashes($key) . '" => "' . addslashes($script) .'"';
}
$builder .= ');';
$builder .= '$wphu_assets["css"] = array(';

foreach ( $all_styles as $key => $style ) {
	$builder .= '"' . addslashes($key) . '" => "' . addslashes($style) .'"';
}
$builder .= ');';
$builder .= $builder_content . '?>';
if ( file_put_contents(BUILD_DIR . '/wp-host-update.php', $builder) ) {
	echo 'Host update script generated. Please see ./build/wp-host-update.php';
}
else {
	echo "Script building failed!";
}

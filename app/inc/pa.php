<?php

// print helper function
if( ! function_exists('pa') ) :
function pa($mixed, $stop = false) {
	$ar = debug_backtrace(); $key = pathinfo($ar[0]['file']); $key = $key['basename'].':'.$ar[0]['line'];
	$print = array($key => $mixed); print( '<pre>' . htmlentities(print_r($print,1), ENT_QUOTES, 'UTF-8') . '</pre>' );
	if($stop == 1) exit();
}
endif;
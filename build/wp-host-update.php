<?php
/**
 * Special script to simplify DB update when moving WordPress site to another host or folder
 * 
 * @version 1.0
 * @author Alex Prokopenko
 * @package WordPress Host Update
 */

define('APP_PATH', dirname(__FILE__));
define('VIEWS_PATH', APP_PATH . '/views');
define( 'WP_INSTALLING', true );
define('WP_CONFIG_PATH', dirname(__FILE__) . '/../../mswp/wp-config.php');
// test mode:
define('ABSPATH', dirname(__FILE__) . '/../../mswp/');



// print helper function
if( ! function_exists('pa') ) :
function pa($mixed, $stop = false) {
	$ar = debug_backtrace(); $key = pathinfo($ar[0]['file']); $key = $key['basename'].':'.$ar[0]['line'];
	$print = array($key => $mixed); print( '<pre>' . htmlentities(print_r($print,1), ENT_QUOTES, 'UTF-8') . '</pre>' );
	if($stop == 1) exit();
}
endif;

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


class Router
{
	public static function callAction()
	{
		$request_callback = self::parseRequest();

		$controller = new $request_callback[0]();
		call_user_func( array($controller, $request_callback[1]) );
	}

	public static function parseRequest()
	{
		$controller = 'page';
		$action = 'index';

		// our request can be in post or get, doesn't matter
		if ( !empty($_REQUEST['r']) ) {
			$route = explode('/', $_REQUEST['r'], 2);
			if ( count($route) == 1 ) {
				$route[] = 'index';
			}

			$controller = $route[0];
			$action = $route[1];
		}

		$controller = ucfirst($controller) . 'Controller';
		$action = 'action' . ucfirst($action);

		return array($controller, $action);
	}
}




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




class PageController extends BaseController
{
	/**
	 * Main script index.
	 * Render primary interface
	 */
	public function actionIndex()
	{
		// get tables list
		$tables = $this->wpdb->get_col("SHOW TABLES LIKE '" . $this->wpdb->prefix . "%'");
		$tables = array_combine($tables, $tables);

		// get wp options
		$wp_options = array(
			'home' => preg_replace('/^(http)s?\:/i', '', get_option('home')),
			'wpcontent_path' => dirname(get_option('upload_path')),
		);

		// define new options to suggest
		$new_options = array(
			'home' => '//'.$_SERVER['HTTP_HOST'],
			'wpcontent_path' => WP_CONTENT_DIR,
			'basepath' => '/'.trim(str_replace(basename(__FILE__), '', $_SERVER['REQUEST_URI']), '/'),
		);
		$new_options['home'] .= $new_options['basepath'];

		// prepare an array for view
		$default_search_replace = array(
			array(
				'Old Url', $wp_options['home'],
				'New Url', $new_options['home'],
			),
			array(
				'Old file path', $wp_options['wpcontent_path'],
				'New file path', $new_options['wpcontent_path'],
			),
			array(
				'Old value', '',
				'New value', '',
			),
		);

		if (MULTISITE) {
			$new_domain = $_SERVER['HTTP_HOST'];
			$home_url = parse_url(get_option('home'));
			$old_domain = $home_url['host'];

			if ( SUBDOMAIN_INSTALL ) {
				$new_domain = '*.' . $new_domain;
				$old_domain = '*.' . $old_domain;
			}

			$domain_replace = array(
				'old_domain' => $old_domain,
				'new_domain' => $new_domain
			);
		}

		// if we don't have in DB old filepath - then user can add it manually
		if ( empty($wp_options['wpcontent_path']) ) {
			unset($default_search_replace[1]);
		}

		$this->responseStart();
		?><?php
/* @var $tables array */
/* @var $default_search_replace array */

?><!DOCTYPE HTML>
<html>
<head>
	<title>WordPress Host Update script</title>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />

	<!-- Bootstrap: Latest compiled and minified CSS -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">

	<!-- Bootstrap: Optional theme -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">
	
	<!-- jQuery UI theme -->
	<link rel="stylesheet" href="https://code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css" crossorigin="anonymous">
	
	<style>body{
	padding-top: 70px;
    padding-bottom: 30px;
}
.wp-logo{
	margin:7px 15px 0 0;
	background-color: #eee; 
	border-radius: 50%;
}
.jumbotron .alert {
	margin-bottom: 0;
}
#replace-form fieldset .row .glyphicon {
	margin-top: 9px;
}
#replace-form .glyphicon-align-justify {
	cursor: move;
}
#progress-log {
	max-height: 200px;
	overflow-y: auto;
	overflow-x: hidden;
}
#progress-log .row .col-md-1{
	position: relative;
}
.bs-callout {
    padding: 20px 20px 10px;
    margin: 20px 0;
    border: 1px solid #eee;
    border-left-width: 5px;
    border-radius: 3px;
}
.bs-callout h4 {
    margin-top: 0;
    margin-bottom: 5px;
}
.bs-callout-warning {
    border-left-color: #aa6708;
}
.bs-callout-warning h4 {
    color: #aa6708;
}
</style>
</head>
<body>

	<!-- Page Wrapper -->
	<div id="page-wrapper">
	
		<!-- Fixed navbar -->
		<nav class="navbar navbar-inverse navbar-fixed-top">
			<div class="container">
				<div class="navbar-header">
					<img class="pull-left wp-logo" src="https://s.w.org/about/images/logos/wordpress-logo-notext-rgb.png" width="32px" height="32px" alt="WordPress">

					<span class="navbar-brand">WordPress Host Update script</span>
				</div>

			</div>
		</nav>

		<div id="main" class="container theme-showcase" role="main">

<?php
?>


<!-- Main jumbotron for a primary marketing message or call to action -->
<div class="jumbotron">
	<h1>Moving the WordPress site?</h1>
	<p>While moving your WordPress site to another domain or folder you probably get a problem with image paths and different hard-coded URLs inside the serialized objects.</p>
	<p>The solution is here, just enter correct replace strings below and enjoy your working site!</p>
	
	<div class="alert alert-warning" role="alert">
		<strong>Warning!</strong> Please do not forget to backup your database before going next!
	</div>
</div>



<section id="replace-form">
	<form class="form">

		<!-- find replace block -->
		<div class="page-header">
			<h2>Replace options</h2>
		</div>

		<div class="row form-header">
			<div class="col-md-1"></div>
			<div class="col-md-4">
				<h4>Find</h4>
			</div>
			<div class="col-md-1"></div>
			<div class="col-md-4">
				<h4>Replace</h4>
			</div>
			<div class="col-md-1"></div>
		</div>
		<fieldset id="find-replace-rows">
			<?php foreach ($default_search_replace as $params) : ?>
			<div class="row">
				<div class="col-md-1 text-right"><span class="glyphicon glyphicon-align-justify" aria-hidden="true"></span></div>
				<div class="col-md-4">
					<div class="form-group"><input type="text" class="form-control" name="find[]" 
												   placeholder="<?php echo html_encode($params[0]); ?>" 
												   value="<?php echo html_encode($params[1]); ?>"></div>
				</div>
				<div class="col-md-1 text-center"><span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span></div>
				<div class="col-md-4">
					<div class="form-group"><input type="text" class="form-control" name="replace[]" 
												   placeholder="<?php echo html_encode($params[2]); ?>" 
												   value="<?php echo html_encode(preg_replace('/\?.*/', '', $params[3])); ?>"></div>
				</div>
				<div class="col-md-1"><a href="#" class="text-danger" title="Delete"><span class="glyphicon glyphicon-remove-circle" aria-hidden="true"></span></a></div>
			</div>
			<?php endforeach; ?>
		</fieldset>
		
		<div class="row">
			<div class="col-md-1"></div>
			<div class="col-md-4">
				<button id="find-replace-add-row" class="btn">Add Row</button>
			</div>
		</div>

		<?php if ( MULTISITE ) : ?>
			<!-- find replace block -->
			<div class="page-header">
				<h2>Multisite configuration</h2>
			</div>

			<div class="row form-header">
				<div class="col-md-1"></div>
				<div class="col-md-4">
					<h4>Old Domain</h4>
				</div>
				<div class="col-md-1"></div>
				<div class="col-md-4">
					<h4>New Domain</h4>
				</div>
				<div class="col-md-1"></div>
			</div>
			<fieldset id="find-multisite-rows">
				<div class="row">
					<div class="col-md-1 text-right"><span class="glyphicon glyphicon-align-justify" aria-hidden="true"></span></div>
					<div class="col-md-4">
						<div class="form-group"><input type="text" class="form-control" name="old_domain[]" 
													   placeholder="<?php echo 'Old domain'; ?>" 
													   value="<?php echo html_encode($domain_replace['old_domain']); ?>"></div>
					</div>
					<div class="col-md-1 text-center"><span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span></div>
					<div class="col-md-4">
						<div class="form-group"><input type="text" class="form-control" name="new_domain[]" 
													   placeholder="<?php echo 'New domain'; ?>" 
													   value="<?php echo html_encode($domain_replace['new_domain']); ?>"></div>
					</div>
					<div class="col-md-1"><a href="#" class="text-danger" title="Delete"><span class="glyphicon glyphicon-remove-circle" aria-hidden="true"></span></a></div>
				</div>
			</fieldset>
		<?php endif; ?>	

		<!-- Advanced options -->
		<div class="page-header">
			<h2>Advanced options</h2>
		</div>
		
		<div class="form-group">
			<div class="radio">
				<label>
					<input type="radio" name="tables" value="all" checked>
					Replace all tables with prefix "<?php echo $this->wpdb->prefix;?>" <span class="text-danger">TODO: use prefix from wp-config and filter tables list with this prefix</span>
				</label>
			  </div>
			<div class="radio">
				<label>
					<input type="radio" name="tables" value="custom">
					Replace only selected tables below
				</label>
			</div>
		</div>
		<div class="form-group hidden" id="custom-tables">
			<label>Tables to search/replace</label>
			<select multiple class="form-control">
				<?php echo html_options($tables); ?>
			</select>
		</div>
		
		<!-- Advanced options -->
		<div class="page-header">
			<h2>That's it!</h2>
		</div>
		<button class="btn btn-primary">Do the Magic!</button>
		
	</form>
</section>


<?php ?>		</div> <!-- #main -->
	</div> <!-- #page-wrapper -->

	<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
	<!-- jQuery UI -->
	<script src="https://code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>
	<!-- Bootstrap Latest compiled and minified JavaScript -->
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
	<!-- spinner -->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/spin.js/2.3.2/spin.min.js"></script>
	<!-- custom scripts -->
	<script>(function ($) {
  "use strict";
  
  $(document).ready(function(){
    init_form_tables_switch();
    init_form_findreplace_rows();
    init_form_processing();
  })
  
  /**
   * Safe print helper
   * @param mixed mixed
   */
  function pa(mixed) {
    if ( window.console )
      console.log(mixed);
  }
  
  /**
   * events for radio buttons switcher
   * show/hide custom tables select
   */
  function init_form_tables_switch() {
    $('#replace-form input[name=tables]').on('click', function(){
      var val = $('#replace-form input[name=tables]:checked').val();
      if ( val == 'custom' ) {
        $('#custom-tables').removeClass('hidden');
      } else {
        $('#custom-tables').addClass('hidden');
      }
    });
  }
  
  var rowClone;
  
  /**
   * init events and UI for find/replace input rows:
   * add, delete, sortable
   * 
   * @global row_clone;
   */
  function init_form_findreplace_rows() {
    rowClone = $('#find-replace-rows .row:last').clone();
    
    // add row event
    $('#find-replace-add-row').on('click', function(e){
      e.preventDefault();
      
      $('#find-replace-rows').append( rowClone.clone() );
    });
    
    // delete row event
    $(document).on('click', '#find-replace-rows a.text-danger', function(e){
      e.preventDefault();
      
      // if we have more than one - just remove
      if ( $('#find-replace-rows .row').size() > 1 ) {
        $(this).parents('.row').remove();
      } else {
        // if only one - just clean input values
        $('#find-replace-rows .row input:text').val('');
      }
    });
    
    // init sortable
    $( "#find-replace-rows" ).sortable({
      handle: ".glyphicon-align-justify"
    });
  }
  
  /**
   * form submit button click event
   * runs validation of the form
   */
  function init_form_processing() {
    $('#replace-form button.btn-primary').click(function(e){
      e.preventDefault();
      
      var replace_rows = $('#find-replace-rows .row');
      var search_condition_error = false;
      var confirm_required = false;
      for ( var i = 0; i < replace_rows.size(); i++ ) {
        var row = replace_rows[i];
        $('.form-group', row).removeClass('has-error').addClass('has-success');
        
        var search_empty = ( $.trim($('input:first', row).val()) == '' );
        var replace_empty = ( $.trim($('input:last', row).val()) == '' );
        
        if ( search_empty && !replace_empty ) {
          $('.form-group', row).addClass('has-error').removeClass('has-success');
          search_condition_error = true;
        }
        
        if ( !search_empty && replace_empty ) {
          $('.form-group', row).addClass('has-error').removeClass('has-success');
          confirm_required = true;
        }
      }
      
      if ( search_condition_error && ! alert("You specified wrond search input in some of the rows.\nPlease correct before we can do Magic!") ) {
        return false;
      }
      
      if ( confirm_required && !confirm("You specified empty replace string(s).\nThis can harm you database.\nAre you sure you want to continue?") ) {
        return false;
      }
      
      process_findreplace_form_submit();
    })
  }
  
  var progressBar = {
    spinner: null,
    max: 0,
    value: 0,
    currentStep: 0,
    formData: null
  };
  
  /**
   * form submit ajax and progress bars
   */
  function process_findreplace_form_submit() {
    // collect values
    var replace_rows = $('#find-replace-rows .row');
    var domain_rows = $('#find-multisite-rows .row');
    var tables_choice = $('#replace-form input[name=tables]:checked').val();
      // autoselect options if "all" selected
      if ( tables_choice == 'all' ) {
        $('#custom-tables select option').attr('selected', true);
      }
    var tables_custom = $('#custom-tables select').val();

    var search_replace = [];
    for ( var i=0; i < replace_rows.size(); i++ ) {
      var row = replace_rows[i];
      var search = $.trim($('input:first', row).val());
      var replace = $.trim($('input:last', row).val());

      search_replace.push( [search, replace] );
    }
    var domain_replace = [];
    for ( var i=0; i < domain_rows.size(); i++ ) {
      var row = domain_rows[i];
      var search = $.trim($('input:first', row).val());
      var replace = $.trim($('input:last', row).val());

      domain_replace.push( [search, replace] );
    }

    progressBar.formData = {
      search_replace: search_replace,
      domain_replace: domain_replace,
      tables_choice: tables_choice,
      tables_custom: tables_custom
    };

    pa(progressBar.formData);

    ajax_request('page/run', {
      data: progressBar.formData,
      success: function(resp) {
        // validate response
        if ( typeof(resp) != 'object' ) {
          alert('Bad server response');
          return;
        }
        if ( resp.error ) {
          alert(resp.error);
          return;
        }

        $('.jumbotron').remove();
        $('#replace-form').replaceWith( resp.progress_html );
        progressBar.max = resp.progress_max;
        
        process_tables_one_by_one();
      }
    });
  }
  
  var spinnerOpts = {
      lines: 7 // The number of lines to draw
    , length: 6 // The length of each line
    , width: 2 // The line thickness
    , radius: 2 // The radius of the inner circle
    , scale: 1 // Scales overall size of the spinner
    , corners: 1 // Corner roundness (0..1)
    , color: '#000' // #rgb or #rrggbb or array of colors
    , opacity: 0.25 // Opacity of the lines
    , rotate: 0 // The rotation offset
    , direction: 1 // 1: clockwise, -1: counterclockwise
    , speed: 1 // Rounds per second
    , trail: 60 // Afterglow percentage
    , fps: 20 // Frames per second when using setTimeout() as a fallback for CSS
    , zIndex: 2e9 // The z-index (defaults to 2000000000)
    , className: 'spinner' // The CSS class to assign to the spinner
    , top: '9px' // Top position relative to parent
    , left: '77%' // Left position relative to parent
    , position: 'absolute' // Element positioning    
    };
    
  /**
   * run ajax for each table in request, update progress bar
   */
  function process_tables_one_by_one() {
    var step = progressBar.currentStep;
    var lastStep = progressBar.formData.tables_custom.length;
    
    // update previous log row if not first step
    if ( step > 0 ) {
      progressBar.spinner.stop();
      
      var log = $('#progress-log .row:last');
      var wp_table = progressBar.formData.tables_custom[step-1];
      log.find('.text').html('Completed with table <span class="text-warning">' + wp_table + '</span>.');
      log.find('.col-md-1').html('<span class="text-success glyphicon glyphicon-ok"></span>');
    }

    if ( step == lastStep ) {
      process_completed_page();
      return;
    }
    
    // insert new log row
    var wp_table = progressBar.formData.tables_custom[step];
    progressBar.spinner = new Spinner(spinnerOpts).spin();

    $('#progress-log').append( '<div class="row"><div class="col-md-1 text-right indicator"></div><div class="col-md-11 text"></div></div>' );
    
    var log = $('#progress-log .row:last');
    log.find('.text').html('Processing table <span class="text-warning">' + wp_table + '</span>...');
    log.find('.col-md-1').append(progressBar.spinner.el);
    
    var data = progressBar.formData;
    data.step = progressBar.currentStep;
    ajax_request( 'process/index', {
      data:data,
      success: function(resp) {
        // TODO: validate response
        progressBar.value += resp.updated * 1;
        update_progress_bar();
        
        progressBar.currentStep++;
        process_tables_one_by_one();
      }
    })
  }
  
  function update_progress_bar() {
    var percents = Math.round( progressBar.value * 100 / progressBar.max );
    $('.progress-bar').css('width', percents+'%').attr('aria-valuenow', percents);    
  }
  
  function process_completed_page() {
    ajax_request('page/thanks', {
      success: function(resp) {
        $('#running').replaceWith(resp);
      }
    })
  }
  
  /**
   * call ajax request
   * 
   * @param string action  controller/action string
   * @param object params  ajax params
   */
  function ajax_request(action, params) {
    var basePath = window.location.pathname;
    params.url = basePath + '?r=' + action;
    
    if ( ! params.type ) params.type = 'POST';
    
    pa(params);
    
    $.ajax(params);
  }
  
}(jQuery));</script>

</body>
</html><?php ?><?php
	}

	/**
	 * Error action to indicate that wp-config.php file is missing
	 */
	public function actionConfigError()
	{
		$this->responseStart();
		?><?php ?><!DOCTYPE HTML>
<html>
<head>
	<title>WordPress Host Update script</title>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />

	<!-- Bootstrap: Latest compiled and minified CSS -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">

	<!-- Bootstrap: Optional theme -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">
	
	<!-- jQuery UI theme -->
	<link rel="stylesheet" href="https://code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css" crossorigin="anonymous">
	
	<style>body{
	padding-top: 70px;
    padding-bottom: 30px;
}
.wp-logo{
	margin:7px 15px 0 0;
	background-color: #eee; 
	border-radius: 50%;
}
.jumbotron .alert {
	margin-bottom: 0;
}
#replace-form fieldset .row .glyphicon {
	margin-top: 9px;
}
#replace-form .glyphicon-align-justify {
	cursor: move;
}
#progress-log {
	max-height: 200px;
	overflow-y: auto;
	overflow-x: hidden;
}
#progress-log .row .col-md-1{
	position: relative;
}
.bs-callout {
    padding: 20px 20px 10px;
    margin: 20px 0;
    border: 1px solid #eee;
    border-left-width: 5px;
    border-radius: 3px;
}
.bs-callout h4 {
    margin-top: 0;
    margin-bottom: 5px;
}
.bs-callout-warning {
    border-left-color: #aa6708;
}
.bs-callout-warning h4 {
    color: #aa6708;
}
</style>
</head>
<body>

	<!-- Page Wrapper -->
	<div id="page-wrapper">
	
		<!-- Fixed navbar -->
		<nav class="navbar navbar-inverse navbar-fixed-top">
			<div class="container">
				<div class="navbar-header">
					<img class="pull-left wp-logo" src="https://s.w.org/about/images/logos/wordpress-logo-notext-rgb.png" width="32px" height="32px" alt="WordPress">

					<span class="navbar-brand">WordPress Host Update script</span>
				</div>

			</div>
		</nav>

		<div id="main" class="container theme-showcase" role="main">

<?php ?>

	<div class="alert alert-danger" role="alert">
		<strong>FATAL ERROR!</strong> We can't find the <strong>wp-config.php</strong> file.
	</div>

	<p>Please make sure current script is placed in the same folder as wp-config.php file and that configuration file has appropriate permissions.</p>

<?php ?>		</div> <!-- #main -->
	</div> <!-- #page-wrapper -->

	<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
	<!-- jQuery UI -->
	<script src="https://code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>
	<!-- Bootstrap Latest compiled and minified JavaScript -->
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
	<!-- spinner -->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/spin.js/2.3.2/spin.min.js"></script>
	<!-- custom scripts -->
	<script>(function ($) {
  "use strict";
  
  $(document).ready(function(){
    init_form_tables_switch();
    init_form_findreplace_rows();
    init_form_processing();
  })
  
  /**
   * Safe print helper
   * @param mixed mixed
   */
  function pa(mixed) {
    if ( window.console )
      console.log(mixed);
  }
  
  /**
   * events for radio buttons switcher
   * show/hide custom tables select
   */
  function init_form_tables_switch() {
    $('#replace-form input[name=tables]').on('click', function(){
      var val = $('#replace-form input[name=tables]:checked').val();
      if ( val == 'custom' ) {
        $('#custom-tables').removeClass('hidden');
      } else {
        $('#custom-tables').addClass('hidden');
      }
    });
  }
  
  var rowClone;
  
  /**
   * init events and UI for find/replace input rows:
   * add, delete, sortable
   * 
   * @global row_clone;
   */
  function init_form_findreplace_rows() {
    rowClone = $('#find-replace-rows .row:last').clone();
    
    // add row event
    $('#find-replace-add-row').on('click', function(e){
      e.preventDefault();
      
      $('#find-replace-rows').append( rowClone.clone() );
    });
    
    // delete row event
    $(document).on('click', '#find-replace-rows a.text-danger', function(e){
      e.preventDefault();
      
      // if we have more than one - just remove
      if ( $('#find-replace-rows .row').size() > 1 ) {
        $(this).parents('.row').remove();
      } else {
        // if only one - just clean input values
        $('#find-replace-rows .row input:text').val('');
      }
    });
    
    // init sortable
    $( "#find-replace-rows" ).sortable({
      handle: ".glyphicon-align-justify"
    });
  }
  
  /**
   * form submit button click event
   * runs validation of the form
   */
  function init_form_processing() {
    $('#replace-form button.btn-primary').click(function(e){
      e.preventDefault();
      
      var replace_rows = $('#find-replace-rows .row');
      var search_condition_error = false;
      var confirm_required = false;
      for ( var i = 0; i < replace_rows.size(); i++ ) {
        var row = replace_rows[i];
        $('.form-group', row).removeClass('has-error').addClass('has-success');
        
        var search_empty = ( $.trim($('input:first', row).val()) == '' );
        var replace_empty = ( $.trim($('input:last', row).val()) == '' );
        
        if ( search_empty && !replace_empty ) {
          $('.form-group', row).addClass('has-error').removeClass('has-success');
          search_condition_error = true;
        }
        
        if ( !search_empty && replace_empty ) {
          $('.form-group', row).addClass('has-error').removeClass('has-success');
          confirm_required = true;
        }
      }
      
      if ( search_condition_error && ! alert("You specified wrond search input in some of the rows.\nPlease correct before we can do Magic!") ) {
        return false;
      }
      
      if ( confirm_required && !confirm("You specified empty replace string(s).\nThis can harm you database.\nAre you sure you want to continue?") ) {
        return false;
      }
      
      process_findreplace_form_submit();
    })
  }
  
  var progressBar = {
    spinner: null,
    max: 0,
    value: 0,
    currentStep: 0,
    formData: null
  };
  
  /**
   * form submit ajax and progress bars
   */
  function process_findreplace_form_submit() {
    // collect values
    var replace_rows = $('#find-replace-rows .row');
    var domain_rows = $('#find-multisite-rows .row');
    var tables_choice = $('#replace-form input[name=tables]:checked').val();
      // autoselect options if "all" selected
      if ( tables_choice == 'all' ) {
        $('#custom-tables select option').attr('selected', true);
      }
    var tables_custom = $('#custom-tables select').val();

    var search_replace = [];
    for ( var i=0; i < replace_rows.size(); i++ ) {
      var row = replace_rows[i];
      var search = $.trim($('input:first', row).val());
      var replace = $.trim($('input:last', row).val());

      search_replace.push( [search, replace] );
    }
    var domain_replace = [];
    for ( var i=0; i < domain_rows.size(); i++ ) {
      var row = domain_rows[i];
      var search = $.trim($('input:first', row).val());
      var replace = $.trim($('input:last', row).val());

      domain_replace.push( [search, replace] );
    }

    progressBar.formData = {
      search_replace: search_replace,
      domain_replace: domain_replace,
      tables_choice: tables_choice,
      tables_custom: tables_custom
    };

    pa(progressBar.formData);

    ajax_request('page/run', {
      data: progressBar.formData,
      success: function(resp) {
        // validate response
        if ( typeof(resp) != 'object' ) {
          alert('Bad server response');
          return;
        }
        if ( resp.error ) {
          alert(resp.error);
          return;
        }

        $('.jumbotron').remove();
        $('#replace-form').replaceWith( resp.progress_html );
        progressBar.max = resp.progress_max;
        
        process_tables_one_by_one();
      }
    });
  }
  
  var spinnerOpts = {
      lines: 7 // The number of lines to draw
    , length: 6 // The length of each line
    , width: 2 // The line thickness
    , radius: 2 // The radius of the inner circle
    , scale: 1 // Scales overall size of the spinner
    , corners: 1 // Corner roundness (0..1)
    , color: '#000' // #rgb or #rrggbb or array of colors
    , opacity: 0.25 // Opacity of the lines
    , rotate: 0 // The rotation offset
    , direction: 1 // 1: clockwise, -1: counterclockwise
    , speed: 1 // Rounds per second
    , trail: 60 // Afterglow percentage
    , fps: 20 // Frames per second when using setTimeout() as a fallback for CSS
    , zIndex: 2e9 // The z-index (defaults to 2000000000)
    , className: 'spinner' // The CSS class to assign to the spinner
    , top: '9px' // Top position relative to parent
    , left: '77%' // Left position relative to parent
    , position: 'absolute' // Element positioning    
    };
    
  /**
   * run ajax for each table in request, update progress bar
   */
  function process_tables_one_by_one() {
    var step = progressBar.currentStep;
    var lastStep = progressBar.formData.tables_custom.length;
    
    // update previous log row if not first step
    if ( step > 0 ) {
      progressBar.spinner.stop();
      
      var log = $('#progress-log .row:last');
      var wp_table = progressBar.formData.tables_custom[step-1];
      log.find('.text').html('Completed with table <span class="text-warning">' + wp_table + '</span>.');
      log.find('.col-md-1').html('<span class="text-success glyphicon glyphicon-ok"></span>');
    }

    if ( step == lastStep ) {
      process_completed_page();
      return;
    }
    
    // insert new log row
    var wp_table = progressBar.formData.tables_custom[step];
    progressBar.spinner = new Spinner(spinnerOpts).spin();

    $('#progress-log').append( '<div class="row"><div class="col-md-1 text-right indicator"></div><div class="col-md-11 text"></div></div>' );
    
    var log = $('#progress-log .row:last');
    log.find('.text').html('Processing table <span class="text-warning">' + wp_table + '</span>...');
    log.find('.col-md-1').append(progressBar.spinner.el);
    
    var data = progressBar.formData;
    data.step = progressBar.currentStep;
    ajax_request( 'process/index', {
      data:data,
      success: function(resp) {
        // TODO: validate response
        progressBar.value += resp.updated * 1;
        update_progress_bar();
        
        progressBar.currentStep++;
        process_tables_one_by_one();
      }
    })
  }
  
  function update_progress_bar() {
    var percents = Math.round( progressBar.value * 100 / progressBar.max );
    $('.progress-bar').css('width', percents+'%').attr('aria-valuenow', percents);    
  }
  
  function process_completed_page() {
    ajax_request('page/thanks', {
      success: function(resp) {
        $('#running').replaceWith(resp);
      }
    })
  }
  
  /**
   * call ajax request
   * 
   * @param string action  controller/action string
   * @param object params  ajax params
   */
  function ajax_request(action, params) {
    var basePath = window.location.pathname;
    params.url = basePath + '?r=' + action;
    
    if ( ! params.type ) params.type = 'POST';
    
    pa(params);
    
    $.ajax(params);
  }
  
}(jQuery));</script>

</body>
</html><?php ?>	<?php
	}

	/**
	 * Initiate replace progress
	 */
	public function actionRun()
	{
		// validate input
		if ( empty($_POST['search_replace']) || empty($_POST['tables_choice']) || empty($_POST['tables_custom']) ) {
			return $this->responseJson( array('error' => 'Server replies that request is not valid. Please try again.') );
		}

		// check tables sizes
		$total_rows = 0;
		foreach ( $_POST['tables_custom'] as $table ) {
			$table_rows = $this->wpdb->get_col("SELECT COUNT(*) as rowscnt FROM $table");
			$total_rows += $table_rows[0];
		}

		ob_start();
		?><section id="running">
	<div class="page-header">
		<h2>Doing Magic! Please be patient...</h2>
	</div>


	<div class="progress">
		<div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
			<span class="sr-only">0% Complete (success)</span>
		</div>
	</div>
	
	<br><br>
	
	<div class="panel panel-default">
		<div class="panel-heading">A bit more info while you waiting:</div>
		<div class="panel-body">
			<div id="progress-log">
			<?php /*
			<div class="row">
				<div class="col-md-1 text-right"><span class="text-success glyphicon glyphicon-ok"></span></div>
				<div class="col-md-11">Completed with table <span class="text-warning">wp_abc</span>.</div>
			</div>
			<div class="row">
				<div class="col-md-1 text-right"></div>
				<div class="col-md-11">Processing table <span class="text-warning">wp_abc</span>...</div>
			</div>
			 * 
			 */ ?>
			</div>
		</div>
	</div>
</section>
<?php
		$progress_html = ob_get_clean();

		return $this->responseJson( array(
			'progress_max' => $total_rows,
			'progress_html' => $progress_html,
		));
	}

	/**
	 * Thanks page
	 */
	public function actionThanks()
	{
		sleep(1);
		$this->responseStart();
		?><section id="thanks">
	<div class="well">
		<h1>Thank you for using our script.</h1>
		<p>We hope all went well and your site is working well!</p>
		<p><a href="?r=page/index" class="btn btn-primary">Back</a></p>
	</div>

	<div class="bs-callout bs-callout-warning">
		<h4>Having problems?</h4>
		<p>If you have problems with our script or have any suggestions, please write to us at our public repository:</p> 
		<p class="text-primary"><a href="https://bitbucket.org/justcoded/tools_wp_host_update/issues?status=new&status=open" target="_blank">http://bitbucket.org/justcoded/tools_wp_host_update</a></p>
	</div>
</section><?php
	}

}





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
		global $wpdb;
		$tables = $_POST['tables_custom'];
		$step = $_POST['step'];
		$curent_table = $tables[$step];
		$updated_tables = 0;

		$select = "SELECT " . $curent_table . ".* FROM " . $curent_table;
		$datas = $wpdb->get_results($select);

		foreach ( $datas as $row ) {
			$values = array();
			$update = "UPDATE $curent_table SET ";
			$i = 1;
			$rows_counter = count((array)$row);

			

			foreach ( $row as $key => $value ) {
				if ( $i == 1 ) {
					$where = " WHERE $key=$value";
					$i++;
					continue;
				}
				if ( strpos($curent_table, 'blogs') ) {
					$value = $this->applyReplaces($value, true);
				}
				else {
					$value = $this->recursiveReplace($value);
				}

				$update .= $i == 2 ? "" : ",";
				$update .= $key . "='" . $this->sqlAddslashes($value) . "'";
				$i++;
			}
			$wpdb->query($update . $where);
			$updated_tables++;
		}

		sleep(1);
		return $this->responseJson(array(
			'updated' => $updated_tables,
		));
	}

	/**
	 * Recursive replace values
	 * @param string|array $data
	 * @param boolean $serialized
	 * @param boolean $parent_serialized
	 * @return string
	 */
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
			$data = $this->applyReplaces($data);
		}

		if ( $serialized )
			return serialize($data);

		if ( $is_json )
			return json_encode($data);

		return $data;
	}

	/**
	 * Apply replace
	 * @param string $subject
	 * @param boolean $is_serialized
	 * @return boolean
	 */
	public function applyReplaces( $subject, $is_blogs = false )
	{
		$search = !empty($is_blogs) ? $_POST['domain_replace'] : $_POST['search_replace'];

		foreach ( $search as $replace ) {
			$subject = str_ireplace($replace[0], $replace[1], $subject);
		}
		return $subject;
	}

	/**
	 * 
	 * @param string $string
	 * @param boolean $strict
	 * @return boolean
	 */
	public function isJson( $string, $strict = false )
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



// loading wp conf file
$error_action = 'page/configError';
if ( ! is_file(WP_CONFIG_PATH) ) {
	$_REQUEST['r'] = $error_action;
} else {
	try{
		@include_once( WP_CONFIG_PATH );
	} catch (\Exception $ex) {
		$_REQUEST['r'] = $error_action;
	}
}

Router::callAction();
exit;
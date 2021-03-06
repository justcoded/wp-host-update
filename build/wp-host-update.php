<?php
/**
 * @version 2.1202.1929
 * @author JustCoded
 * @see https://bitbucket.org/justcoded/tools_wp_host_update 
 */
global $wphu_assets; $wphu_assets["js"] = array("assets/scripts.js" => "<script>(function(a){function m(a){window.console&&console.info(a)}function u(){a(\"#replace-form input[name=tables]\").on(\"click\",function(){\"custom\"==a(\"#replace-form input[name=tables]:checked\").val()?a(\"#custom-tables\").removeClass(\"hidden\"):a(\"#custom-tables\").addClass(\"hidden\")})}function v(){q=a(\"#find-replace-rows .row:last\").clone();a(\"#find-replace-add-row\").on(\"click\",function(b){b.preventDefault();a(\"#find-replace-rows\").append(q.clone())});a(document).on(\"click\",\"#find-replace-rows a.text-danger\",
function(b){b.preventDefault();1<a(\"#find-replace-rows .row\").size()?a(this).parents(\".row\").remove():a(\"#find-replace-rows .row input:text\").val(\"\")});a(\"#find-replace-rows\").sortable({handle:\".glyphicon-align-justify\"});a(\"#find-multisite-rows a.text-danger\").click(function(b){b.preventDefault();b=a(this).parents(\"div.row\");this.is_disabled=this.is_disabled?!1:!0;a(\"input\",b).attr(\"disabled\",this.is_disabled)})}function w(){a(\"#replace-form button.btn-primary\").click(function(b){b.preventDefault();
b=a(\"#find-replace-rows .row\");for(var c=!1,d=!1,h=0;h<b.size();h++){var k=b[h];a(\".form-group\",k).removeClass(\"has-error\").addClass(\"has-success\");var l=\"\"==a.trim(a(\"input:first\",k).val()),e=\"\"==a.trim(a(\"input:last\",k).val());l&&!e&&(a(\".form-group\",k).addClass(\"has-error\").removeClass(\"has-success\"),c=!0);!l&&e&&(a(\".form-group\",k).addClass(\"has-error\").removeClass(\"has-success\"),d=!0)}if(c&&!alert(\"You specified wrond search input in some of the rows.\\nPlease correct before we can do Magic!\")||
d&&!confirm(\"You specified empty replace string(s).\\nThis can harm you database.\\nAre you sure you want to continue?\"))return!1;x()})}function x(){var b=a(\"#find-replace-rows .row\"),g=a(\"#find-multisite-rows .row\"),d=a(\"#replace-form input[name=tables]:checked\").val();\"all\"==d&&a(\"#custom-tables select option\").attr(\"selected\",!0);for(var h=a(\"#custom-tables select\").val(),k=a(\"#replace-form input[name=replace_method]:checked\").val(),l=[],e=0;e<b.size();e++){var f=b[e],n=a.trim(a(\"input:first\",f).val()),
f=a.trim(a(\"input:last\",f).val());l.push([n,f])}b=[];for(e=0;e<g.size();e++)f=g[e],n=a.trim(a(\"input:first\",f).val()),f=a.trim(a(\"input:last\",f).val()),b.push([n,f]);c.formData={search_replace:l,domain_replace:b,tables_choice:d,tables_custom:h,replace_method:k};m(c.formData);p(\"page/run\",{data:c.formData,success:function(b){\"object\"!=typeof b?alert(\"Bad server response\"):b.error?alert(b.error):(a(\".jumbotron\").remove(),a(\"#replace-form\").replaceWith(b.progress_html),c.max=b.progress_max,r())}})}function r(){var b,
g,d=c.currentStep,h=c.formData.tables_custom.length;0<d&&(c.spinner.stop(),b=a(\"#progress-log .row:last\"),g=c.formData.tables_custom[d-1],b.find(\".text\").html(\'Completed with table <span class=\"text-warning\">\'+g+\"</span>.\"),b.find(\".col-md-1\").html(\'<span class=\"text-success glyphicon glyphicon-ok\"></span>\'));d==h?(m(\"Run in: \"+c.timer+\" sec\"),y()):(g=c.formData.tables_custom[d],c.spinner=(new Spinner(z)).spin(),a(\"#progress-log\").append(\'<div class=\"row\"><div class=\"col-md-1 text-right indicator\"></div><div class=\"col-md-11 text\"></div></div>\'),
b=a(\"#progress-log .row:last\"),b.find(\".text\").html(\'Processing table <span class=\"text-warning\">\'+g+\"</span>...\"),b.find(\".col-md-1\").append(c.spinner.el),t+=20,a(\"#progress-log\").animate({scrollTop:t},\"fast\"),b=c.formData,b.step=c.currentStep,p(\"process/index\",{data:b,success:function(b){\"object\"==typeof b?(m({table:b.table,rows:b.found+\" > \"+b.updated,\"in\":b[\"in\"]+\"s\"}),c.value+=1*b.found,c.timer+=b[\"in\"],b=Math.round(100*c.value/c.max),a(\".progress-bar\").css(\"width\",b+\"%\").attr(\"aria-valuenow\",
b)):(m(b),alert(\'Update failed for table \"\'+c.formData.tables_custom[c.currentStep]+\'\"\'));c.currentStep++;r()}}))}function y(){p(\"page/thanks\",{success:function(b){a(\"#running\").replaceWith(b)}})}function p(b,c){c.url=window.location.pathname+\"?r=\"+b;c.type||(c.type=\"POST\");a.ajax(c)}a(document).ready(function(){u();v();w()});var q,c={spinner:null,max:0,value:0,currentStep:0,formData:null,timer:0},z={lines:7,length:6,width:2,radius:2,scale:1,corners:1,color:\"#000\",opacity:.25,rotate:0,direction:1,
speed:1,trail:60,fps:20,zIndex:2E9,className:\"spinner\",top:\"9px\",left:\"77%\",position:\"absolute\"},t=0})(jQuery);</script>");$wphu_assets["css"] = array("assets/styles.css" => "<style>body{  padding-top: 70px;     padding-bottom: 30px; } .wp-logo{  margin:7px 15px 0 0;  background-color: #eee;   border-radius: 50%; } .jumbotron .alert {  margin-bottom: 0; } #replace-form fieldset .row .glyphicon {  margin-top: 9px; } #replace-form .glyphicon-align-justify {  cursor: move; } #progress-log {  max-height: 200px;  overflow-y: auto;  overflow-x: hidden; } #progress-log .row .col-md-1{  position: relative; } .bs-callout {     padding: 20px 20px 10px;     margin: 20px 0;     border: 1px solid #eee;     border-left-width: 5px;     border-radius: 3px; } .bs-callout h4 {     margin-top: 0;     margin-bottom: 5px; } .bs-callout-warning {     border-left-color: #aa6708; } .bs-callout-warning h4 {     color: #aa6708; } </style>");
define('APP_PATH', dirname(__FILE__));
define('VIEWS_PATH', APP_PATH . '/views');
define('WP_INSTALLING', true);
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
function html_encode( $value ) {
	return htmlentities($value, ENT_QUOTES, 'UTF-8');
}
function sql_add_slashes( $string = '' ) {
	$string = str_replace('\\', '\\\\', $string);
	return str_replace('\'', '\\\'', $string);
}
function is_json( $string, $strict = false ) {
	$json = @json_decode($string, true);
	if ( $strict == true && !is_array($json) )
		return false;
	return !( $json == NULL || $json == false );
}
define('WP_CONFIG_PATH', dirname(__FILE__) . '/wp-config.php');
define('ABSPATH', find_wp_abspath() );
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
public $wpdb;
public function __construct()
	{
		global $wpdb;
		$this->wpdb = $wpdb;
	}
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
public function responseJson( $data )
	{
		$this->responseStart('json');
		echo json_encode($data);
	}
	abstract public function actionIndex();
}
class ReplaceHelper
{
public static function recursiveReplace($data, $to_replace, $serialized = false, $parent_serialized = false)
	{
		$is_json = false;
		if ( is_string($data) && ( $unserialized = @unserialize($data) ) !== false ) {
			// PHP currently has a bug that doesn't allow you to clone the DateInterval / DatePeriod classes.
			// We skip them here as they probably won't need data to be replaced anyway
			if ( is_object($unserialized) && ( $unserialized instanceof DateInterval || $unserialized instanceof DatePeriod ) ) {
				return $data;
			}
			$data = self::recursiveReplace($unserialized, $to_replace, true, true);
		}
		elseif ( is_array($data) ) {
			$_tmp = array();
			foreach ( $data as $key => $value ) {
				$_tmp[$key] = self::recursiveReplace($value, $to_replace, false, $parent_serialized);
			}
			$data = $_tmp;
			unset($_tmp);
		}
		elseif ( is_object($data) ) {
			$_tmp = clone $data;
			foreach ( $data as $key => $value ) {
				$_tmp->$key = self::recursiveReplace($value, $to_replace, false, $parent_serialized);
			}
			$data = $_tmp;
			unset($_tmp);
		}
		elseif ( is_json($data, true) ) {
			$_tmp = array();
			$data = json_decode($data, true);
			foreach ( $data as $key => $value ) {
				$_tmp[$key] = self::recursiveReplace($value, $to_replace, false, $parent_serialized);
			}
			$data = $_tmp;
			unset($_tmp);
			$is_json = true;
		}
		elseif ( is_string($data) ) {
			$data = self::replace($data, $to_replace);
		}
		if ( $serialized )
			return serialize($data);
		if ( $is_json )
			return json_encode($data);
		return $data;
	}
public static function replace( $subject, $to_replace )
	{
		if ( empty($to_replace) || !is_array($to_replace) ) {
			return $subject;
		}
		foreach ( $to_replace as $params ) {
			$subject = str_ireplace($params[0], $params[1], $subject);
		}
		return $subject;
	}
}
class PageController extends BaseController
{
public function actionIndex()
	{
		$tables = $this->wpdb->get_col("SHOW TABLES LIKE '" . $this->wpdb->prefix . "%'");
		$tables = array_combine($tables, $tables);
		$wp_options = array(
			'home' => preg_replace('/^(http)s?\:/i', '', get_option('home')),
			'wpcontent_path' => dirname(get_option('upload_path')),
		);
		$new_options = array(
			'home' => '//' . trim($_SERVER['HTTP_HOST'], '/'),
			'wpcontent_path' => WP_CONTENT_DIR,
			'basepath' => '/' . trim(str_replace(basename(__FILE__), '', $_SERVER['REQUEST_URI']), '/'),
		);
		$new_options['home'] = rtrim($new_options['home'] . $new_options['basepath'], '/');
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
?><!DOCTYPE HTML>
<html>
<head>
	<title>WordPress Host Update script</title>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">
	<link rel="stylesheet" href="https://code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css" crossorigin="anonymous">
	<?php global $wphu_assets; echo stripslashes($wphu_assets["css"]["assets/styles.css"]); ?>
</head>
<body>
	<div id="page-wrapper">
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
					<div class="col-md-1 text-right"><span class="glyphicon glyphicon-cloud" aria-hidden="true"></span></div>
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
		<div class="page-header">
			<h2>Advanced options</h2>
		</div>
		<div class="row">
			<div class="col-md-6">
				<div class="form-group">
					<div class="radio">
						<label>
							<input type="radio" name="tables" value="all" checked>
							Replace all tables with prefix "<?php echo html_encode($this->wpdb->prefix); ?>"
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
			</div>
			<div class="col-md-6">
				<div class="form-group">
					<div class="radio">
						<label>
							<input type="radio" name="replace_method" value="simple" checked>
							Replace only content columns/string data columns
						</label>
					</div>
					<div class="radio">
						<label>
							<input type="radio" name="replace_method" value="full">
							Replace all data columns
						</label>
					</div>
				</div>
			</div>
		</div>
		<div class="page-header">
			<h2>That's it!</h2>
		</div>
		<button class="btn btn-primary">Do the Magic!</button>
	</form>
</section>
<?php ?>		</div>
	</div>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
	<script src="https://code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/spin.js/2.3.2/spin.min.js"></script>
	<?php global $wphu_assets; echo stripslashes($wphu_assets["js"]["assets/scripts.js"]); ?>
</body>
</html><?php ?><?php
	}
public function actionConfigError()
	{
		$this->responseStart();
		?><?php ?><!DOCTYPE HTML>
<html>
<head>
	<title>WordPress Host Update script</title>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">
	<link rel="stylesheet" href="https://code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css" crossorigin="anonymous">
	<?php global $wphu_assets; echo stripslashes($wphu_assets["css"]["assets/styles.css"]); ?>
</head>
<body>
	<div id="page-wrapper">
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
<?php ?>		</div>
	</div>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
	<script src="https://code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/spin.js/2.3.2/spin.min.js"></script>
	<?php global $wphu_assets; echo stripslashes($wphu_assets["js"]["assets/scripts.js"]); ?>
</body>
</html><?php ?>	<?php
	}
public function actionRun()
	{
		if ( empty($_POST['search_replace']) || empty($_POST['tables_choice']) || empty($_POST['tables_custom']) ) {
			return $this->responseJson( array('error' => 'Server replies that request is not valid. Please try again.') );
		}
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
			<?php
?>
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
		<p class="text-primary">
			<a href="https://github.com/justcoded/wp-host-update/issues" target="_blank"><?php echo htmlentities('https://github.com/justcoded/wp-host-update/issues', ENT_QUOTES, 'UTF-8'); ?></a>
		</p>
	</div>
</section><?php
	}
}
class ProcessController extends BaseController
{
	const PER_PAGE = 5000;
	protected $skipRules;
public function actionIndex()
	{
		set_time_limit(0);
		$metric_start = microtime(true);
global $wpdb;
		$tables = $_POST['tables_custom'];
		$step = $_POST['step'];
		$to_replace = $_POST['search_replace'];
		$blogs_replace = $this->prepareBlogReplace(@$_POST['domain_replace']);
		$current_table = $tables[$step];
		$clean_table_name = strtolower(preg_replace("/^$wpdb->prefix(\d+\_)?/", '', $current_table));
		$replace_method = ($_POST['replace_method'] == 'full')? 'full' : 'simple';
		if ( $replace_method == 'simple' ) $this->prepareSkipRules();
		$updated_rows = 0;
		$primary_keys = $wpdb->get_results("SHOW KEYS FROM `$current_table` WHERE Key_name = 'PRIMARY'");
		$select_count_query = "SELECT COUNT(*) as rowscnt FROM $current_table";
		$rows_cnt = $wpdb->get_var($select_count_query);
		$select_query = "SELECT * FROM $current_table";
		$select_limit = self::PER_PAGE;
		$select_offset = 0;
		while ( $select_offset < $rows_cnt ) {
			$db_rows = $wpdb->get_results("$select_query LIMIT $select_limit OFFSET $select_offset");
			foreach ( $db_rows as $row ) {
				$update_query = "UPDATE $current_table SET ";
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
				$update_query .= implode(',', $update_values);
				$wpdb->query($update_query . $where);
				$updated_rows++;
			}
			$select_offset += $select_limit;
		}
		$metric_end = microtime(true);
		return $this->responseJson(array(
			'table' => $current_table,
			'found' => $rows_cnt,
			'updated' => $updated_rows,
			'in' => round($metric_end - $metric_start, 3),
			//'last' => !empty($update_values)? $update_values : null,
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
protected function canSkipColumn($table, $column, $value)
	{
		if ( is_numeric($value) || '' === $value || is_null($value) ) return true;
		if ( isset($this->skipRules['tables_columns'][$table][$column]) ) return true;
		if ( preg_match($this->skipRules['table_name']['^'], $table) ||
			preg_match($this->skipRules['table_name']['$'], $table)
		) {
			return true;
		}
		if ( preg_match($this->skipRules['column_name']['^'], $column) ||
			preg_match($this->skipRules['column_name']['$'], $column)
		) {
			return true;
		}
		return false;
	}
	protected function prepareSkipRules()
	{
		$table_names = array(
			'^' => array(
				'term_relationships',
			),
			'$' => array(
				'_log', '_logs',
			),
		);
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
		$column_names = array(
			'^' => array(
				'id',
				'meta_key',
				'status',
				'date_',
				'created',
				'hash',
				'md5',
			),
			'$' => array(
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
		$this->skipRules['table_name']['^'] = '/^(' . implode('|', $table_names['^']) . ')/i';
		$this->skipRules['table_name']['$'] = '/(' . implode('|', $table_names['$']) . ')$/i';
		$this->skipRules['column_name']['^'] = '/^(' . implode('|', $column_names['^']) . ')/i';
		$this->skipRules['column_name']['$'] = '/(' . implode('|', $column_names['$']) . ')$/i';
	}
}
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
exit;?>
<?php

/* version 3.0 - KB */

	$errors = array();
	$messages = array();
	$is_siteurl_correct = false;
	
	$dir = dirname(__FILE__);
	if(!is_file($dir.'/wp-config.php'))
		$errors[] = 'Could not find "wp-config.php" file.<br />Please move current script to the WordPress root folder.';
	else
		include_once($dir.'/wp-config.php');
	
	if(empty($errors))
	{
		// get current base url
		$base = 'http://'.$_SERVER['HTTP_HOST'];
		$base_path = '/'.trim(str_replace(basename(__FILE__), '', $_SERVER['REQUEST_URI']), '/');
		$base.= $base_path;
		
		global $wpdb;
		$settings = array('siteurl' => '', 'home' => '', 'admin_email' => '', 'upload_path' => '');
		
		$wp_options = array();
		foreach($settings as $key=>$setting)
		{
			$option_object = new stdClass();
			$option_object->option_name = $key;
			$option_object->option_value = get_option($key);
			$wp_options[] = $option_object;
		}
		
		foreach($wp_options as $opt){
			if(isset($settings[ $opt->option_name ]))
				$settings[ $opt->option_name ] = $opt->option_value;
		}
		
		if(strcmp($settings['siteurl'], $base) == 0){
			$is_siteurl_correct = true;
			$messages[] = 'Your site url is OK.';
		}
		
		if(!empty($_POST['submitted']))
		{
			// update site urls
			if(!$is_siteurl_correct)
			{
				if( empty($_POST['siteurl']) || strpos($_POST['siteurl'], 'http://') !== 0 ){
					$errors[] = 'Enter valid site url';
				}
				elseif( strcmp($_POST['siteurl'], $_POST['siteurl_old']) != 0 ){
					$old_siteurl = mysql_escape_string( trim($_POST['siteurl_old'], '/') );
					$new_siteurl = mysql_escape_string( trim($_POST['siteurl'], '/') );
					$queries = array(
						//"UPDATE $wpdb->options SET `option_value` = REPLACE(`option_value`,'$old_siteurl/','$new_siteurl/') WHERE `option_name` IN('siteurl','home')",
						"UPDATE $wpdb->options SET `option_value` = REPLACE(`option_value`,'$old_siteurl','$new_siteurl') WHERE `option_name` IN('siteurl','home')",
						"UPDATE $wpdb->posts SET guid = REPLACE(guid,'$old_siteurl/','$new_siteurl/')",
						"UPDATE $wpdb->posts SET post_content = REPLACE(post_content,'$old_siteurl/','$new_siteurl/')",
					);
					//pa($queries,1);
					foreach($queries as $sql)
                        $wpdb->query($sql);
					
					//update custom fields in wp_postmeta table
					$sql = "SELECT meta_id, meta_value FROM $wpdb->postmeta WHERE meta_value LIKE '%$old_siteurl%' ";
					$datas = $wpdb->get_results($sql);
					//var_dump($datas);
					foreach($datas as &$meta_row){
						$meta_arr = @unserialize($meta_row->meta_value);                        
                        if ($meta_arr !== false) {
    						$meta_arr = recursion_replace($meta_arr,$old_siteurl,$new_siteurl);
    						$meta_updated = serialize($meta_arr);
                        } else {
                            $meta_updated =  str_replace($old_siteurl,$new_siteurl,$meta_row->meta_value);
                        }
						$sql = "UPDATE $wpdb->postmeta SET meta_value = '$meta_updated' WHERE meta_id = $meta_row->meta_id";                        
						$wpdb->query($sql);
					}
                    
                    //update widgets_text in wp_options table
                    $sql = "SELECT option_id, option_value FROM $wpdb->options WHERE option_name = 'widget_text' ";
                    $datas = $wpdb->get_results($sql);
                    					
					foreach($datas as &$option_row){
						$option_arr = @unserialize($option_row->option_value);                        
                        if ($option_arr !== false) {
    						$option_arr = recursion_replace($option_arr,$old_siteurl,$new_siteurl);
    						$option_updated = serialize($option_arr);
                        } else {
                            $option_updated =  str_replace($old_siteurl,$new_siteurl,$option_row->option_value);
                        }
						$sql = "UPDATE $wpdb->options SET option_value = '$option_updated' WHERE option_id = $option_row->option_id";
						$wpdb->query($sql);
					}
					
					$is_siteurl_correct = true;
					$base = trim($_POST['siteurl'], '/');
					$settings['siteurl'] = trim($_POST['siteurl'], '/');
					$messages[] = 'Options and posts have been updated.';
				}
			}
			
			// update admin email
			if(!empty($_POST['admin_email']))
			{
				$regexp = '/^\w+([-+\.]*\w+)*@\w+([-\.]\w+)*$/i';
				if( !preg_match($regexp, $_POST['admin_email']) ){
					$errors[] = 'Enter valid admin email';
				}
				elseif( strcmp($_POST['admin_email'], $settings['admin_email']) != 0 )
				{
					// update
					$settings['admin_email'] = $_POST['admin_email'];
					$wpdb->query("UPDATE $wpdb->options SET option_value='".mysql_escape_string($settings['admin_email'])."' WHERE `option_name` = 'admin_email' ");
					$messages[] = 'Admin Email has been updated.'; 
				}
			}
			
			// update upload path
			if(!empty($_POST['upload_path']))
			{
				$settings['upload_path'] = $_POST['upload_path'];
				$wpdb->query("UPDATE $wpdb->options SET option_value='".mysql_escape_string($settings['upload_path'])."' WHERE `option_name` = 'upload_path' ");
				$messages[] = 'Upload Path has been updated.'; 
			}
            
			// update folder in content
            $folder = trim($_POST['replace_folder']);
			if(!empty($folder))
			{
				$folder = trim($_POST['replace_folder']);
                $old_folder = '/'.$folder.'/wp-content/themes';
                $new_folder = '/wp-content/themes';
                
                $sql = "UPDATE $wpdb->posts SET post_content = REPLACE(post_content,'$old_folder','$new_folder')";                
				$wpdb->query($sql);
                
                //update widgets_text in wp_options table
                $sql = "SELECT option_id, option_value FROM $wpdb->options WHERE option_name = 'widget_text' ";
                $datas = $wpdb->get_results($sql);
                  					
				foreach($datas as &$option_row){
					$option_arr = @unserialize($option_row->option_value);                        
                      if ($option_arr !== false) {
  						$option_arr = recursion_replace($option_arr,$old_folder,$new_folder);
  						$option_updated = serialize($option_arr);
                      } else {
                          $option_updated =  str_replace($old_folder,$new_folder,$option_row->option_value);
                      }
					$sql = "UPDATE $wpdb->options SET option_value = '$option_updated' WHERE option_id = $option_row->option_id";                    
					$wpdb->query($sql);
				}                
                
				$messages[] = 'Folder "'.$folder.'"" in content has been updated.'; 
			}             
		}
	}
	
	function recursion_replace($data,$search='',$replace=''){
		//recursively make str_replace over array
		if(empty($search)||empty($data))return false;
		if( is_array($data) ){
			foreach($data as &$row){
				$row = recursion_replace($row,$search,$replace);
			}
			return $data;
		}else{
			return str_replace($search,$replace,$data);
		}
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr">
<html>
<head>
	<title>Wordpress Host Update script by JustCoded.com</title>
	<style type="text/css">
		body {font:13px/18px "Lucida Grande","Lucida Sans Unicode",Tahoma,Verdana,sans-serif; color:#333; padding:10px; }
		a{ color:#224466; text-decoration:none; }
		a:hover{ color:#D54E21; }
		img {border:none;}
		form {border:none;}
		form fieldset{border:1px solid #80B5D0; padding:10px; }
		form label{ font-weight:bold; padding:0 0 3px; }
		form label em { font-style:normal; font-weight:normal; }
		form input.text { padding:4px; color: #555; font-size:18px; width: 97%; }
		form input.submit {
			color:#224466;
			background-color:#CEE1EF;
			border:1px solid #80B5D0;
			cursor:default;
			font:bold 13px "Lucida Grande","Lucida Sans Unicode",Tahoma,Verdana,sans-serif;
			padding:3px 5px;
			text-decoration:none;
			-moz-border-radius-bottomleft:3px;
			-moz-border-radius-bottomright:3px;
			-moz-border-radius-topleft:3px;
			-moz-border-radius-topright:3px;
		}
		form input.submit:hover { border-color:#328AB2; color:#D54E21; }
		#header {border-bottom:5px solid #464646; padding:0 0 10px; overflow:hidden; height:70px;}
		#header .left{ float:left; }
		#header .right{ float:right; }
		#content { background:#eaf3fa; width:450px; padding:20px; margin:75px auto 0;}
		#content p { margin:0; padding: 0 0 10px; }
		#content p.buttons { margin:20px 0 0; width:435px; } 
		#content .errors { color:#d00; font-weight:bold; padding:0 0 10px; }
		#content .messages { color:#0d0; font-weight:bold; padding:0 0 10px; }
		#footer {border-top:5px solid #464646; margin:75px auto 0; padding:20px; text-align:center; font-size:14px;}
	</style>
</head>
<body>
	<div id="header">
		<a title="Powered by WordPress" href="http://wordpress.org/" class="left"><img src="wp-admin/images/logo-login.gif"></a>
		
	</div>
	<div id="content">
		<?php if ( !empty($errors) ) : ?>
			<p class="errors"><?= implode('<br /><br />', $errors); ?></p>
		<?php elseif ( !empty($messages) ) : ?>
			<p class="messages">- <?= implode('<br /><br />- ', $messages); ?></p>
		<?php endif; ?>
		<form action="" method="post">
			<fieldset>
				<?php if (!$is_siteurl_correct) : ?>
					<input type="hidden" name="siteurl_old" value="<?= $settings['siteurl'] ?>" />
					
					<label for="siteurl">Enter Your blog site url</label><br />
					<input id="siteurl" type="text" name="siteurl" size="40" value="<?= $base ?>" class="text" /><br />
					<span>current url is: <b><?= $settings['siteurl'] ?></b></span><br /><br />
				<?php endif; ?>
				<label for="siteurl">Change admin email: </label><br />
				<input id="siteurl" type="text" name="admin_email" size="40" value="<?= $settings['admin_email'] ?>" class="text" /><br /><br />
				
				<label for="siteurl">Change upload directory: </label><br />
				<input id="siteurl" type="text" name="upload_path" size="40" value="<?= $_SERVER['DOCUMENT_ROOT'].$base_path.'/wp-content/uploads' ?>" class="text" /><br />
				<span>current dir is: <b><?= $settings['upload_path'] ?></b></span><br /><br />
                
				<label for="siteurl">Remove folder name in content: </label><br />
				<input id="siteurl" type="text" name="replace_folder" size="40" value="" class="text" /><br />
                <span>Optional, leave it blank if you don't need it or paste your folder name without slashes</span>
                <br />
				<p class="buttons" align="right">
					<input type="submit" name="submitted" value="Update" class="submit" />
				</p>
			</fieldset>
		</form>
	</div>
	<div id="footer">	
		Copyright @2009 <a href="http://justcoded.com" target="_blank">JustCoded.com</a>. All rights reserved.
	</div>
</body>
</html>
<?php
/*
Plugin Name: Purlem Personalized URL
Plugin URI: http://purlem.com
Description: Personalize your blog to visitors and track results with Personalized URLs (PURLs). <strong>The Plugin Requires a <a href='http://www.purlem.com'>Purlem Account</a>.</strong>
Version: 1.0.7
Author: Marty Thomas
Author URI: http://purlem.com/company
License: A "Slug" license name e.g. GPL2


Copyright 2011  Marty Thomas  (email : support@purlem.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as 
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


add_action('update_option_purlemID', 'add_htaccess_code');
add_action('update_option_purlemURI', 'add_htaccess_code');
add_action('get_header', 'display_purl_code');
add_action('the_content', 'display_purl_content');
add_action('the_title', 'display_purl_header');
add_action('widgets_init', create_function('', 'return register_widget("PurlemWidget");'));
add_action('get_header', 'purlCSS');


function add_htaccess_code() {
	$file = '../.htaccess';
	$code = "\r\n#PURL CODE\nRewriteEngine on 
RewriteCond %{SCRIPT_FILENAME} !([A-Za-z0-9_]+)\.(html?|php|asp|css|jpg|gif|shtml|htm|xhtml|txt|ico|xml|wp-admin|admin)/?$ [NC] 
RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^([A-Za-z]+)\.([A-Za-z]+)/?$ ".get_option('purlemURI')."&purl=\\$1\\$2&ID=".get_option('purlemID')."&page=1&wordpress=Y [R]\n#END PURL CODE";
	$code_permalink = "#PURL CODE
<IfModule mod_rewrite.c>
RewriteEngine On
RewriteCond %{SCRIPT_FILENAME} !([A-Za-z0-9_]+).(html?|php|asp|css|jpg|gif|shtml|htm|xhtml|txt|ico|xml|wp-admin|admin)/?$ [NC]
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^([a-zA-Z]+)\\\\\.([a-zA-Z]+)/?$ ".get_option('purlemURI')."?purl=\\$1\\$2&ID=".get_option('purlemID')."&page=1&wordpress=Y [R,L]
</IfModule>
#END PURL CODE\n";
	$htaccess_content = @file_get_contents($file);
	if($htaccess_content) {
		if(strstr($htaccess_content,'<IfModule mod_rewrite.c>')) {
			$purlCode = $code_permalink;
		} else {
			$purlCode = $code;
		}
		$search_pattern = "/(#PURL CODE)(?:[\w\W\r\n]*?)(#END PURL CODE)/i";
		$new_content = preg_replace($search_pattern, $purlCode, $htaccess_content);
		if(!strstr($new_content,'#PURL CODE')) {
			$new_content = $purlCode."".$htaccess_content;
		}
		file_put_contents($file, stripslashes($new_content));
	} else {
		file_put_contents($file, stripslashes($code));
	}
}

function display_purl_code() {
	$data = @file_get_contents('http://www.purlapi.com/lp/index.php?ID='.$_GET["ID"].'&name='.$_GET["purl"].'&page='.$_GET["page"].'&test='.$_GET["test"].'&wordpress='.$_GET["wordpress"]); $user = json_decode($data); @session_start(); if($_GET['username']) $_SESSION['visitor']=$_GET['username']; if($user->{'login'} && ($_SESSION['visitor'] != $user->{'purl1'})) { echo $user->{'login'}; exit; }
	$_SESSION['user'] = $user;
}

function display_purl_content($content) {
	$newContent .= purl_convert($content);
	
	if($_GET['wordpress'] == 'Y') {
		$newContent .= $_SESSION['user']->{'content'};
		if(get_option('showPurlForm') == 'Y') $newContent .= $_SESSION['user']->{'form'};
		
		if(!$_SESSION['user']->{'firstName'}) {
			$newContent .= '<b>PURL NOT FOUND</b> Please try again.';
		}
	}
	
	
	
	return $newContent;
}

function display_purl_header($content) {
	$content = purl_convert($content);
	return $content;
}


// add the admin options page
add_action('admin_menu', 'plugin_admin_add_page');
	function plugin_admin_add_page() {
	add_options_page('Purlem Settings', 'Purlem', 'manage_options', 'plugin', 'plugin_options_page');
}


// display the admin options page
function plugin_options_page() {
	?>	
  <div class="wrap">
  <div id="icon-options-general" class="icon32"><br /></div>
	<h2>Purlem Settings</h2>
	by <strong>Marty Thomas</strong> of <strong>Purlem</strong><br />
	<div style="background-color:white;padding: 10px 10px 10px 10px;margin-right:15px;margin-top:10px;margin-bottom:15px;border: 1px solid #ddd; width:350px;">
	<img src="http://www.purlem.com/assets/images/logo_white.gif" width="300" height="84" alt="Purlem Personalized URL Marketing" /><br />
  <h3 style="margin-top:0px;margin-bottom:0px;">Purlem - Personal URL Marketing</h3>
	<a href="http://www.purlem.com">http://www.purlem.com</a></div>
  <p>Enter details from your Purlem account below.  Don't have an account? <a href="http://www.purlem.com">Sign Up</a> </p>
  <form action="options.php" method="post">
  <?php wp_nonce_field('update-options'); ?>
  
  <table class="form-table">
  
  <tr valign="top">
  <th scope="row">Purlem Client ID:</th>
  <td><input name="purlemID" type="text" value="<?php echo get_option('purlemID'); ?>" size="10" /></td>
  </tr>
   
  <tr valign="top">
  <th scope="row">Redirect URL: </th>
  <td><input name="purlemURI" type="text" value="<?php echo get_option('purlemURI'); ?>" size="50" /><br /><i>Full URL of Blog Page to redirect PURL visitors to.</i></td>
  </tr>
  
   <tr valign="top">
  <th scope="row">Show Form in Content Area: </th>
  <td><input <?php if (!(strcmp(get_option('showPurlForm'),"Y"))) {echo "checked=\"checked\"";} ?> name="showPurlForm" type="checkbox" value="Y" /></td>
  </tr>
  
  
  </table>
  
  <input type="hidden" name="action" value="update" />
  <input type="hidden" name="page_options" value="purlemID,purlemURI,showPurlForm" />
  
  <p class="submit">
  <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
  </p>
  
  </form>
  </div>
  
  
	<?php
}

function purl_convert($content) {
	$i = 0;
	$patterns[$i] = '/#firstName/'; $i++;
	$patterns[$i] = '/#lastName/'; $i++;
	$patterns[$i] = '/#organization/'; $i++;
	$patterns[$i] = '/#position/'; $i++;
	$patterns[$i] = '/#email/'; $i++;
	$patterns[$i] = '/#phone/'; $i++;
	$patterns[$i] = '/#address1/'; $i++;
	$patterns[$i] = '/#address/'; $i++;
	$patterns[$i] = '/#city/'; $i++;
	$patterns[$i] = '/#state/'; $i++;
	$patterns[$i] = '/#zip/'; $i++;
	$patterns[$i] = '/#password/'; $i++;
	$i=0;
	$replacements[$i] = $_SESSION['user']->{'firstName'}; $i++;
	$replacements[$i] = $_SESSION['user']->{'lastName'}; $i++;
	$replacements[$i] = $_SESSION['user']->{'contact_organization'}; $i++;
	$replacements[$i] = $_SESSION['user']->{'contact_position'}; $i++;
	$replacements[$i] = $_SESSION['user']->{'contact_email'}; $i++;
	$replacements[$i] = $_SESSION['user']->{'contact_phone'}; $i++;
	$replacements[$i] = $_SESSION['user']->{'contact_address1'}; $i++;
	$replacements[$i] = $_SESSION['user']->{'contact_address1'}; $i++;
	$replacements[$i] = $_SESSION['user']->{'contact_city'}; $i++;
	$replacements[$i] = $_SESSION['user']->{'contact_state'}; $i++;
	$replacements[$i] = $_SESSION['user']->{'contact_zip'}; $i++;
	$replacements[$i] = $_SESSION['user']->{'contact_password'}; $i++;
	$convertedContent = preg_replace($patterns, $replacements, $content);
	return $convertedContent;
}


/**
 * PurlemWidget Class
 */
class PurlemWidget extends WP_Widget {
    /** constructor */
    function PurlemWidget() {
        parent::WP_Widget(false, $name = 'Purlem Form');	
    }

    /** @see WP_Widget::widget */
    function widget($args, $instance) {		
        extract( $args );
        $title = apply_filters('widget_title', $instance['title']);
        ?>
              <?php echo $before_widget; ?>
                  <?php if ( $title )
                        echo $before_title . $title . $after_title;
												echo $_SESSION['user']->{'form'};
               echo $after_widget; ?>
        <?php
    }

    /** @see WP_Widget::update */
    function update($new_instance, $old_instance) {				
        return $new_instance;
    }

    /** @see WP_Widget::form */
    function form($instance) {				
        $title = esc_attr($instance['title']);
        ?>
            <p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Form Title:'); ?> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></label></p>
        <?php 
    }

} // class PurlemWidget


function purlCSS() {
	?>
  <style type="text/css">
	<!--
	.formTitle {
		font-size:20px;
		font-weight:bold;
		margin-bottom:10px;
	}
	.formDescription {
		font-size:16px;
		margin-bottom:15px;
	}
	.formElement {
		margin-bottom:15px;
	}
	.formElement .textbox {
		font-size: 16px;
		width:97%;
		font-weight: bold;
		padding:2px;
	}
	.formElement .title {
		font-weight:bold;
	}
	.checkbox, .radio {
		font-weight:normal;
	}
	.button {
		margin-top:10px;
		font-size: 18px;
	}
	-->
	</style>
  <?php
}
?>
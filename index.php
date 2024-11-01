<?php

    /* 
    Plugin Name: wpNibbler
    Description: Adds nibbler verification code to website
    Author: Stephan Gerlach
    Version: 1.0.2 
    Author URI: http://www.computersniffer.com
    */  
    
    // add admin menu
    add_action('admin_menu', 'wpNibbler_admin_menu');
    function wpNibbler_admin_menu() {
        add_menu_page('wpNibbler', 'wpNibbler', 'administrator', 'wpNibbler_code', 'wpNibbler_code');
    }
    

    // options page    
    function wpNibbler_code() {
        
        global $wpdb;
        
        // check permissions
        if (!current_user_can('manage_options'))  {
    	   	wp_die( __('You do not have sufficient permissions to access this page.') );
    	}
        
        // check if update is sent
        if (isset($_POST['wpnibbler'])) {
            
            // try to get verification key
            $opt = get_option('wpnibbler_authentication_code');
            
            // if verification key exists update key
            if (!(!$opt) || $opt=='') {
                update_option('wpnibbler_authentication_code',$_POST['wpnibbler']);
            }
            // if no key exists (first run) insert key
            else {
                add_option('wpnibbler_authentication_code',$_POST['wpnibbler']);
            }
            
        }
        
        // load verification key
        $opt = get_option('wpnibbler_authentication_code');
        if (!$opt) {
            $opt = '';
        }
        
        // form output
    	echo '<div class="wrap">';
        echo '<h2>Nibbler Verification Code</h2>';
        
        echo '<form action="" method="post">';
        echo '<label for="wpnibbler">Nibbler Verification Code</label>: 
                <input type="text" name="wpnibbler" id="wpnibbler" value="'.$opt.'" size="50" />';
        echo '<br /><input type="submit" name="wpnibbler_save" value="Save" />';
        echo '</form>';
        
        // if key exists display nibbler result in iframe
        if ($opt!='') {
            
            // get site url
            $site = get_option('siteurl');
            
            // remove http:// from the beginning
            if(substr($site,0,7)=='http://') {
                $site = substr($site,7);
            }
            // remove https:// from the beginning
            if(substr($site,0,7)=='https://') {
                $site = substr($site,8);
            }
            
            // load nibbler result in ifframe
            echo '<iframe width="100%" height="400px" src="http://nibbler.silktide.com/reports/'.$site.'"></iframe>';
            
        }
    }
    
    // add action to load javascript for custom menu icon
    add_action('admin_head','wpNibbler_custom_menu');
    
    function wpNibbler_custom_menu() {
        echo '<script type="text/javascript">
            jQuery(document).ready(function(){ jQuery(".toplevel_page_wpNibbler_code .wp-menu-image a img").attr("src","'.plugin_dir_url(__FILE__ ).'/wpNibbler_menu_icon.png");});
            </script>';
    }
    
    
    // add action for inserting code in <head>
    add_action('wp_head', 'wpNibbler_add_code');
    
    function wpNibbler_add_code() {
        
        global $wpdb;
        $opt = get_option('wpnibbler_authentication_code');
        if (!(!$opt)) {
            echo '<meta name="nibbler-site-verification" content="'.$opt.'" />'."\n";
        }
        
    }
    
    
    
    
?>
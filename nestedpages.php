<?php
/*
Plugin Name: Nested Pages
Plugin URI: http://nestedpages.com
Description: Provides an intuitive drag and drop interface for managing pages in the Wordpress admin, while enhancing quick edit. Includes an auto-generated menu to match the nested interface, support for all post types and more.
Version: 3.2.2
Author: Kyle Phillips
Author URI: https://github.com/kylephillips
Text Domain: wp-nested-pages
Domain Path: /languages/
License: GPLv2 or later.
Copyright: Kyle Phillips
*/

/*  Copyright 2022 Kyle Phillips  (email : support@nestedpages.com)

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
    
/**
* Check Wordpress and PHP versions before instantiating plugin
*/
register_activation_hook( __FILE__, 'nestedpages_check_versions' );
function nestedpages_check_versions( $wp = '3.9', $php = '5.4.0' ) {
    global $wp_version;
    if ( version_compare( PHP_VERSION, $php, '<' ) ) $flag = 'PHP';
    elseif ( version_compare( $wp_version, $wp, '<' ) ) $flag = 'WordPress';
    else return;
    $version = 'PHP' == $flag ? $php : $wp;
    
    if (function_exists('deactivate_plugins')){
        deactivate_plugins( basename( __FILE__ ) );
    }
    
    wp_die('<p>The <strong>Nested Pages</strong> plugin requires'.$flag.'  version '.$version.' or greater.</p>','Plugin Activation Error',  array( 'response'=>200, 'back_link'=>TRUE ) );
}

if( !class_exists('Bootstrap') ) :
    define('NESTEDPAGES_DIR', __DIR__);
    define('NESTEDPAGES_URI', __FILE__);

    nestedpages_check_versions();
    require_once(NESTEDPAGES_DIR . '/vendor/autoload.php');
    require_once(NESTEDPAGES_DIR . '/app/NestedPages.php');
    NestedPages::init();
endif;
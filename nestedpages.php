<?php
/*
Plugin Name: Nested Pages
Plugin URI: http://nestedpages.com
Description: Adds intuitive drag and drop functionality for page sorting and nesting, while retaining the quick edit features you're used to.
Version: 0.1
Author: Kyle Phillips
Author URI: https://github.com/kylephillips
License: GPLv2 or later.
Copyright: Kyle Phillips
*/
if( !class_exists('NestedPages') ) :
	require_once('includes/class-nestedpages.php');
	$nested_pages = new NestedPages;
endif;
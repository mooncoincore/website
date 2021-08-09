<?php
/*
Plugin Name: Avia Template Builder
Description: The Template Builder helps you create modern and unique page layouts with the help of a drag and drop interface
Version: 4.8
Author: Christian "Kriesi" Budschedl
Author URI: http://kriesi.at
Text Domain: avia_framework
License: 
*/



require_once( dirname( __FILE__ ) . '/php/class-template-builder.php' );


//activates the builder safe mode. this hides the shortcodes that are built with the content builder from the default wordpress content editor. 
//can also be set to "debug", to show shortcode content and extra shortcode container field
Avia_Builder()->setMode( 'safe' );

<?php

$changelly_widget_code = get_post_meta($post_id, 'changelly_widget_sourcecode', true);

if( isset($changelly_widget_code) && !empty( $changelly_widget_code )  ){
	
}else{
	$changelly_widget_code = '<strong>'. __('Changelly Widget source code is missing or incorrect','ccpw') . '</strong>';
}

$output = ccpw_HTMLpluginVersion();
$output .= $changelly_widget_code;


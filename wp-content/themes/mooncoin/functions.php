<?php

/*
* Add your own functions here. You can also copy some of the theme functions into this file. 
* Wordpress will use those functions instead of the original functions then.
*/

add_filter( 'avf_google_content_font', 'avia_add_content_font');
function avia_add_content_font($fonts)
{

$fonts['Roboto'] = 'Roboto:300,400,500';
return $fonts;
}

function cc_mime_types($mimes) {
 $mimes['svg'] = 'image/svg+xml';
 return $mimes;
}
add_filter('upload_mimes', 'cc_mime_types');


function just_add_cors_http_header($headers){

    $headers['Access-Control-Allow-Origin'] = '*';

    return $headers;

}

add_action('wp_headers','just_add_cors_http_header');

<?php
function get_var( &$var, $default = null ) {
	return isset( $var ) ? $var : $default;
}

function redirect( string $url, int $delay_in_milisecs = 0 ) {
	$string = '<script type="text/javascript">';
//	$string .= 'window.location.href = "' . $url . '"';
	$string .= 'setTimeout(function () {
       window.location.href = "' . $url . '"
    }, "' . $delay_in_milisecs . '");';
	$string .= '</script>';
	
	echo $string;
}

function clear_html_contents(){
	$string = '<script type="javascript">';
	$string .= 'document.body.innerHTMl = "";';
	$string .= '</script>';
	
	echo $string;
}

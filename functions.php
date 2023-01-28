<?php
function get_var( &$var, $default = null ) {
	return isset( $var ) ? $var : $default;
}

/**
 * Custom redirect defining url for auto redirects based on JavaScript
 *
 * @param string $url
 * @param int $delay_in_milisecs
 *
 * @return void
 */
function redirect( string $url, int $delay_in_milisecs = 0 ) {
	$string = '<script type="text/javascript">';
	$string .= 'setTimeout(function () {
       window.location.href = "' . $url . '"
    }, "' . $delay_in_milisecs . '");';
	$string .= '</script>';
	
	echo $string;
}

/**
 * Clear html contents (not currently working)
 * @return void
 */
function clear_html_contents() {
	$string = '<script type="javascript">';
	$string .= 'document.body.innerHTMl = "";';
	$string .= '</script>';
	
	echo $string;
}

/**
 * Redirect if db connection is ok and if is homepage or not
 * @return void
 */
function redirect_if_db_exists() {
	if ( dbinstall::get_db_conn() && $_SERVER[ 'REQUEST_URI' ] === '/index.php' ) {
		clear_html_contents();
		echo '<pre>' . print_r( dbinstall::get_db_conn(), true ) . '</pre>';
		redirect( 'app' );
	}
	if ( ! dbinstall::get_db_conn() && $_SERVER[ 'REQUEST_URI' ] !== '/index.php' ) {
		redirect( '/' );
		exit( 0 );
	}
}

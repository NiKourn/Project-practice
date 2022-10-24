<?php
function get_var( &$var, $default = null ) {
	return isset( $var ) ? $var : $default;
}

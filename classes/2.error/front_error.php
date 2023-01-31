<?php

class Custom_errors {
	
	function __construct() {
		// Set user-defined error handler function
		set_error_handler( [__CLASS__, 'myErrorHandler' ], E_ALL );
	}
	
	public static function myErrorHandler( $errno, $errstr, $errfile, $errline ) {
		if ($errno === E_USER_WARNING ){
			echo
			'<div class="alert alert-warning d-flex align-items-center" role="alert">
  <svg class="bi flex-shrink-0 me-2" role="img" aria-label="Warning:"><use xlink:href="#exclamation-triangle-fill"/></svg>
  <div>
    <b>Custom error:</b> ['. $errno .'] '. $errstr .'<br>Error on line' . $errline .' in ' .$errfile . '<br>
  </div>
  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>';
			echo "<b>Custom error:</b> [$errno] $errstr<br>";
			echo " Error on line $errline in $errfile<br></div>";
		}
		echo "<div class='alert alert-warning' role='alert'><b>Custom error:</b> [$errno] $errstr<br>";
		echo " Error on line $errline in $errfile<br></div>";
	}
	
	
}

new Custom_errors;

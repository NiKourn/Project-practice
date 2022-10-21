<?php

class includeLoader {
	
	/**
	 * @var string
	 */
	private static string $folder_path = 'includes/';
	
	
	function __construct() {
	}
	
	/**
	 * @param $file_name
	 * @param $title
	 *
	 * @return void
	 */
	public static function include( $file_name, $title = '' ) {
		include_once self::$folder_path . $file_name . '.php';
	}
	
}


<?php
defined( 'ABSPATH' ) || exit;

class includeLoader {
	
	/**
	 * @var string
	 */
	private static string $folder_path = ABSPATH . 'includes/';
	
	private static string $title;
	
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
		//self::$title = $title;
	}
	
	public static function get_title(){
		return self::$title;
	}
	
}


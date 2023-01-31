<?php

/**
 * Creates database from jSon file
 */
class db_install extends create_db {
	
	/**
	 * Connection return
	 * @var
	 */
	private static $conn;
	
	/**
	 * Store Json file to check if its empty throughout class
	 * @var json_file not needed yet...
	 */
	private $json_file;
	
	/**
	 * @var
	 */
	private $error_msg;
	
	
	function __construct() {
		parent::__construct();
		
		//redirect without extension if extension is entered (stick it somewhere in code & make a templates file to stick there all the templates)
		foreach (glob('*.php') as $page_ext){
			if ( $_SERVER[ 'REQUEST_URI' ] === '/'.$page_ext) {
				foreach (str_replace('.php','', glob('*.php') ) as $page){
					redirect($page);
				}
			}
		}
		
		if ( ! $this->fetch_connection() ) {
			$this->init();
			exit();
		} else {
			if ( $_SERVER[ 'REQUEST_URI' ] !== '/templates/app.php' && ( $_SERVER[ 'REQUEST_URI' ] === '/install' || $_SERVER[ 'REQUEST_URI' ] === '/' ) ) {
				redirect( '/templates/app' );
				exit();
			}
		}
		
	}
	
	/**
	 * Initialize db Installation
	 * @return void
	 */
	private function init() {
		$this->build_db();
	}
	
	/**
	 * Put array into jSon file
	 *
	 * @param $args
	 *
	 * @return array
	 */
	private function put_json_content( array $args, $password ) {
		$json_file = ABSPATH . 'assets/json/db-info.json';
		$json_raw  = file_get_contents( $json_file );
		$json      = json_decode( $json_raw );
		$json      = [
			'host'          => $args[ 'host' ],
			'db_name'       => $args[ 'db_name' ],
			'root_username' => $args[ 'root_username' ],
			'root_password' => $password,
			'db_username'   => $args[ 'db_username' ],
			'db_password'   => $args[ 'db_password' ]
		
		];
		$json_out  = json_encode( $json );
		file_put_contents( $json_file, $json_out );
		
		return $json;
	}
	
	/**
	 * Decode json into an array or an object
	 *
	 * @param $array // false for object, true for array
	 *
	 * @return mixed
	 */
	public static function jSon_decode( $array = false ) {
		$json_file = file_get_contents( ABSPATH . 'assets/json/db-info.json' );
		$json      = json_decode( $json_file, $array );
		
		return $json;
	}
	
	/**
	 * Fetch dB to check if we have a connection/db#
	 *
	 * @return mixed|void
	 */
	private function fetch_connection() {
//		get json and decode
		$json            = self::jSon_decode( true );// decode the JSON into an associative array
		$this->json_file = $json;
		
		try {
			$db    = $this->PDO_connection( $json, false );
			$query = "SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME=?";
			$stmt  = $db->prepare( $query );
			
			$stmt->bindparam( 1, $json[ 'db_name' ] );
			$stmt->execute();
			$stmt_fetch = $stmt->fetch();
			self::$conn = $stmt_fetch;
			
			return $stmt_fetch;
		} catch ( Exception $e ) {
			
			trigger_error($e->getMessage(), E_USER_WARNING);
			//Errors_app::add_error_msg('connection', $e->getMessage());
			return;
		}
		
	}
	
	/**
	 * Build the dB with default options
	 *
	 * @return true|void
	 */
	private function build_db() {
		if ( $_SERVER[ 'PHP_SELF' ] !== '/install.php' ) {
			redirect( 'install' );
			exit();
		}
		
		if ( ! $this->nonce_validation() ) {
			return;
		}
		if ( isset( $_POST ) ) {
			$root_password = htmlspecialchars( $_POST[ 'root_password' ] );
			//add details to Json before creating db/tables to store info, since nonce returns true and $_POST global isset
//			if( self::$conn ){
//				$this->put_json_content( $_POST, $root_password );
//				echo 'check credentials for error file';
//				return;
//			}
			$json_details = $this->put_json_content( $_POST, $root_password );
			
			//Create db and tables if db is not existent only from form!
			$this->create_db( $json_details );
			$this->create_tables( $json_details );
			redirect( 'app' );
			exit();
		}
		
		
		exit();
	}
	
	/**
	 * Validate nonce for db-forms for security reasons, used in build db method
	 * @return array|void
	 */
	private
	function nonce_validation() {
		$json = self::jSon_decode();
		include 'includes/db-form.php';
		
		$testnonce = $nonce->verifyNonce( $token );
		//if verification is false die
		if ( ! $testnonce ) {
			echo $this->error_msg = 'Form validation is incomplete';
			
			return;
		}
		
		//if empty session return false
		if ( ( ! isset( $_SESSION[ 'nonce' ][ 'dbform' ] ) ) ) {
			echo $this->error_msg = 'Form is not submitted, something is wrong with the form token';
			
			return;
		}
		
		if ( ! isset( $_POST[ 'host' ] ) || ! isset( $_POST[ 'db_name' ] ) ) {
			trigger_error('Form is not submitted', E_USER_WARNING);
			return;
		}
		
		return $testnonce;
	}
	
	
	/**
	 * @return mixed
	 */
	public
	static function get_db_conn() {
		//make connection and access db to check if there's already a database created with given name from $dbname
		
		return self::$conn;
		
	}
	
}

new db_install();

<?php

/**
 * Creates database from jSon file
 */
class dbinstall extends create_db {
	
	/**
	 * Connection return
	 * @var
	 */
	private static $conn;
	
	/**
	 * insert create_db object into constructor and load it as a parameter
	 * @var create_db object
	 */
	private $create_db;
	
	/**
	 * Store Json file to check if its empty throughout class
	 * @var json_file not needed yet...
	 */
	private $json_file;
	
	/**
	 * @var
	 */
	private $error_code;
	
	
	function __construct() {
		parent::__construct();
		//Cannot redirect from constructor, it runs from theme header which causes loops (loading in every single page of the theme to test if there's a database connection or not)
		//Check function
		
		if ( $this->fetch_connection() && ($_SERVER[ 'REQUEST_URI' ] === '/index.php' || $_SERVER[ 'REQUEST_URI' ] === '/') ) {
			//clear_html_contents();
			//echo '<pre>' . print_r( dbinstall::get_db_conn(), true ) . '</pre>';
			redirect( 'app.php' );
			//header("Refresh:0; url=app.php");
			exit( 0 );
		}
//		if ( ! $this->fetch_connection() && $_SERVER[ 'REQUEST_URI' ] !== '/index.php' ) {
//			$this->init();
//			redirect( 'index.php' );
//			echo '<pre>' . print_r( dbinstall::get_db_conn() . 'are we here', true ) . '</pre>';
//			exit( 0 );
//
//		}
//
//		if ( ! $this->fetch_connection() && $_SERVER[ 'REQUEST_URI' ] === '/index.php' ) {
//			$this->init();
//			echo '<pre>' . print_r( dbinstall::get_db_conn(), true ) . '</pre>';
//
//		}
//
//		if ( $this->fetch_connection() && $_SERVER[ 'REQUEST_URI' ] === '/app.php' ) {
//			die();
//		}
		if ( ! self::get_db_conn() ) {
			$this->init();
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
	 * @return void
	 */
	private function put_Json_content( array $args ) {
		$json_file = ABSPATH . 'assets/json/db-info.json';
		$json_raw  = file_get_contents( $json_file );
		$json      = json_decode( $json_raw );
		$json      = [
			'host'          => $args[ 'host' ],
			'db_name'       => $args[ 'db_name' ],
			'root_username' => $args[ 'root_username' ],
			'root_password' => $args[ 'root_password' ],
			'db_username'   => $args[ 'db_username' ],
			'db_password'   => $args[ 'db_password' ]
		
		];
		$json_out  = json_encode( $json );
		file_put_contents( $json_file, $json_out );
	}
	
	/**
	 * Decode our Json file and use it to extract the array in form
	 * @return mixed
	 */
	public static function jSon_decode() {
		$json_file = file_get_contents( ABSPATH . 'assets/json/db-info.json' );
		$json      = json_decode( $json_file );
		
		return $json;
	}
	
	/**
	 * Fetch our connection based on access_db method using the jSon saved contents
	 * @return false|string|null
	 */
	private function fetch_connection() {
		$get_json = file_get_contents( ABSPATH . 'assets/jSon/db-info.json' );
		$json     = json_decode( $get_json, true ); // decode the JSON into an associative array
		
		$this->json_file = $json;
		if ( ! empty( $json ) && ! self::get_db_conn() ) {
			$servername = $json[ 'host' ];
			$dbname     = $json[ 'db_name' ];
			$username   = $json[ 'db_username' ];
			$password   = $json[ 'db_password' ];
			
			//make connection and access db to check if there's already a database created with given name from $dbname
			
			if ( ! $this->access_db( $servername, $dbname, $username, $password ) ) {
				//check this functionality cause it creates a database and repopulates stuff
				try {
					$this->createDB( $json );
					$this->createTables( $json );
				} catch ( PDOException $e ) {
					echo $this->error_code = 'No connection! Please check your connection details at file db-info.json or check if database/username exists';
					$e->getMessage();
				}
				
				return false;
				
			}
			
		}//end if $json empty
		else {
//			includeLoader::include( 'db-form', 'Database Configuration' );
//			echo $this->error_code = 'Empty db-info.json file';
			return false;
		}
		
		//unset( $stmt );
		return self::$conn = $this->access_db( $servername, $dbname, $username, $password );
	}
	
	/**
	 * Access our db if successfully return an array of the db name
	 *
	 * @param $servername
	 * @param $dbname
	 * @param $username
	 * @param $password
	 *
	 * @return false|string|void
	 */
	private function access_db( $servername, $dbname, $username, $password ) {
		try {
			$db = $this->PDO_connection( $servername, $username, $password, $dbname, false );
//			if(! $db){
//				echo 'Check your credentials';
//				return;
//			}
			$query = "SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME=?";
			$stmt  = $db->prepare( $query );
			if ( $stmt === false ) {
				return false;
			}
			$stmt->bindparam( 1, $dbname );
			$stmt->execute();
			$stmt_fetch = $stmt->fetch();
			
			if ( self::$conn ) {
				return '<br><h2>Database Already Created, Redirecting...</h2>';
			}
			
			return $stmt_fetch;
		} catch ( PDOException $e ) {
			$e->getMessage();
		}
		
	}
	
	/**
	 * Build our database if passed onwards conditions
	 * @return bool|void
	 */
	private function build_db() {
		
		if ( ! $this->fetch_connection() ) {
			
			if ( ! $this->nonce_validation() ) {
				$this->error_code = 'Nonce validation failed';
				
				return;
			}
			
			$root_password = htmlspecialchars( $_POST[ 'root_password' ] );
			
			
			//if(isset ( $_POST[ 'submit' ] )) {
			//add details to Json before creating db/tables to store info, since nonce return true
			$json_details = $this->form_post_details( $_POST, $root_password );
			
			//Create db and tables if db is not existent
			$this->createDB( $json_details );
			$this->createTables( $json_details );
			redirect( 'app.php' );
			
			return true;
		}
		
		return false;
	}
	
	/**
	 * Validate nonce for db-forms for security reasons, used in build db method
	 * @return array|void
	 */
	private function nonce_validation() {
//		includeLoader::include(  'db-form', 'Database Configuration' );
		include 'includes/db-form.php';
		
		$testnonce = $nonce->verifyNonce( $token );
		//if verification is false die
		if ( ! $testnonce ) {
			echo $this->error_code = 'Form validation is incomplete';
			
			return;
		}
		
		//if empty session return false
		if ( ( ! isset( $_SESSION[ 'nonce' ][ 'dbform' ] ) ) ) {
			echo $this->error_code = 'Form is not submitted, something wrong with the form token';
			
			return;
		}
		
		if ( ! isset( $_POST[ 'host' ] ) || ! isset( $_POST[ 'db_name' ] ) ) {
			return;
		}
		
		return $testnonce;
	}
	
	/**
	 *  The form post details - put into json and return an array with same args
	 *
	 * @param array $args // The array with args
	 * @param $root_password // Password passed with htmlargs
	 *
	 * @return array
	 */
	private function form_post_details( $args, $root_password ) {
		$json[ 'host' ]          = $args[ 'host' ];
		$json[ 'db_name' ]       = $args[ 'db_name' ];
		$json[ 'root_username' ] = $args[ 'root_username' ];
		$json[ 'root_password' ] = $root_password;
		$json[ 'db_username' ]   = $args[ 'db_username' ];
		$json[ 'db_password' ]   = $args[ 'db_password' ];
		$this->put_Json_content( $args );
		
		return $json;
	}
	
	/**
	 * @return mixed
	 */
	public static function get_db_conn() {
		//make connection and access db to check if there's already a database created with given name from $dbname
		
		return self::$conn;
		
	}

//	private function error_code_msg($message){
//		$this->error_code = $message;
//		return $message;
//	}
	
	
}

new dbinstall();

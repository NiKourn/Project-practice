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
		
		if ( ! $this->fetch_connection() ) {
			$this->init();
			exit();
		} else {
			if ( $_SERVER[ 'REQUEST_URI' ] !== '/app.php' ) {
				redirect( '/app.php' );
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
	 * Decode our Json file and use it to extract the array in dbform
	 * @return mixed
	 */
	public static function jSon_decode() {
		$json_file = file_get_contents( ABSPATH . 'assets/json/db-info.json' );
		$json      = json_decode( $json_file );
		
		return $json;
	}
	
	/**
	 * Fetch dB to check if we have a connection/db
	 *
	 * @return false|mixed|string|void
	 */
	private function fetch_connection() {
		$get_json = file_get_contents( ABSPATH . 'assets/jSon/db-info.json' );
		$json     = json_decode( $get_json, true ); // decode the JSON into an associative array
		
		$this->json_file = $json;
		
		try {
			$db    = $this->PDO_connection( $json, false );
			$query = "SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME=?";
			$stmt  = $db->prepare( $query );
			if ( $stmt === false ) {
				return false;
			}
			$stmt->bindparam( 1, $json[ 'db_name' ] );
			$stmt->execute();
			$stmt_fetch = $stmt->fetch();
			self::$conn = $stmt_fetch;
			if ( self::$conn ) {
				return '<br><h2>Database Already Created, Redirecting...</h2>';
			}
			
			return $stmt_fetch;
		} catch ( PDOException $e ) {
			$e->getMessage();
		}
		
	}
	
	/**
	 * @return true|void
	 */
	private
	function build_db() {
		if ( $_SERVER[ 'REQUEST_URI' ] !== '/index.php' ) {
			redirect( 'index.php' );
			exit( 0 );
		}
		
		echo 'Something not right';
		if ( ! $this->nonce_validation() ) {
			$this->error_code = 'Nonce validation failed';
			
			return;
		}
		if ( isset( $_POST ) ) {
			$root_password = htmlspecialchars( $_POST[ 'root_password' ] );
			//add details to Json before creating db/tables to store info, since nonce return true
			$json_details = $this->put_json_content( $_POST, $root_password );
			
			//Create db and tables if db is not existent only from form
			$this->createDB( $json_details );
			$this->createTables( $json_details );
			redirect( 'app.php' );
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
			echo $this->error_code = 'Form validation is incomplete';
			
			return;
		}
		
		//if empty session return false
		if ( ( ! isset( $_SESSION[ 'nonce' ][ 'dbform' ] ) ) ) {
			echo $this->error_code = 'Form is not submitted, something is wrong with the form token';
			
			return;
		}
		
		if ( ! isset( $_POST[ 'host' ] ) || ! isset( $_POST[ 'db_name' ] ) ) {
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
	
	private function error_code_msg( $no, $message ) {
		return $this->error_code = $message . $no;
	}
	
	
}

new dbinstall();

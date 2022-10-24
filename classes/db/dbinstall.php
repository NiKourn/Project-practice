<?php

class dbinstall {
	
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
		if ( ! self::get_db() ) {
			$this->init();
		} else {
			//Header( "Refresh:1;url=app.php" );
		}
		
	}
	
	/**
	 * Initialize db Installation
	 * @return void
	 */
	public function init() {
		include_once 'create_db.php';
		//include_once ABSPATH . 'classes/nonce/nonce.php';
		$this->create_db = new create_db();
		$this->fetch_connection();
		$this->build_db();
	}
	
	/**
	 * @return void
	 */
	private function put_Json_content( $host, $db_name, $root_username, $root_password, $db_username, $db_password ) {
		$json_file = ABSPATH . 'assets/json/db-info.json';
		$json_raw  = file_get_contents( $json_file );
		$json      = json_decode( $json_raw );
		$json      = [
			'host'          => $host,
			'db_name'       => $db_name,
			'root_username' => $root_username,
			'root_password' => $root_password,
			'db_username'   => $db_username,
			'db_password'   => $db_password
		
		];
		$json_out  = json_encode( $json );
		file_put_contents( $json_file, $json_out );
	}
	
	public static function explodeJson() {
		$json_file = file_get_contents( ABSPATH . 'assets/json/db-info.json' );
		$json      = json_decode( $json_file );
		
		return $json;
	}
	
	/**
	 * @param $return
	 *
	 * @return mixed|void|null
	 */
	private function fetch_connection() {
		$get_json = file_get_contents( ABSPATH . 'assets/jSon/db-info.json' );
		$json     = json_decode( $get_json, true ); // decode the JSON into an associative array
		
		$this->json_file = $json;
		if ( ! empty( $json ) && ! self::get_db() ) {
			$servername = $json[ 'host' ];
			$dbname     = $json[ 'db_name' ];
			$username   = $json[ 'db_username' ];
			$password   = $json[ 'db_password' ];
			
			//make connection and access db to check if there's already a database created with given name from $dbname
			
			try {
				return $this->access_db( $servername, $dbname, $username, $password );
				
			}//end try
			catch ( PDOException $e ) {
				includeLoader::include( 'db-form', 'Database Configuration' );
				echo $this->error_code = 'No connection! Please check your connection details at file db-info.json or check if database/username exists';
			}
		}//end if $json empty
		else {
			includeLoader::include( 'db-form', 'Database Configuration' );
			echo $this->error_code = 'Empty db-info.json file';
		}
		//unset( $stmt );
	}
	
	/**
	 * @param $servername
	 * @param $dbname
	 * @param $username
	 * @param $password
	 * @param $return
	 *
	 * @return mixed|void
	 */
	private function access_db( $servername, $dbname, $username, $password ) {
		//$this->storeJsonInfo();
		$db    = $this->create_db->PDO_connection( $servername, $username, $password );
		$query = "SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME=?";
		$stmt  = $db->prepare( $query );
		if ( $stmt === false ) {
			return;
		}
		$stmt->bindparam( 1, $dbname );
		$stmt->execute();
		self::$conn = $stmt->fetch();
		
		if ( self::$conn ) {
//			Header( "Location: app.php" );
//			echo '<br><h2>Database Already Created, Redirecting...</h2>';
//			exit();
		} else {
			includeLoader::include( 'db-form', 'Database Configuration' );
			echo $this->error_code = 'Something wrong with the database';
		}
		unset( $stmt );
	}
	
	/**
	 * @return void
	 */
	private function build_db() {
		$nonce = new Nonce;
		$token = ( isset ( $_POST[ 'dbform-token' ] ) ? $_POST[ 'dbform-token' ] : '' );
		$testnonce = $nonce->verifyNonce( $token );
		//if verification is false die
		
		if ( $testnonce === false ) {
			//throw new \Exception( 'Nonce validation not complete' );
			return;
		}
		
		//if empty or not same session and post supervariables the return false
		if ( ( ! isset( $_SESSION[ 'dbform-token' ] ) || ! isset ( $_POST[ 'dbform-token' ] ) ) || ( $_SESSION[ 'dbform-token' ] !== $_POST[ 'dbform-token' ] ) ) {
			//throw new \Exception('Tokens need to be same for you to proceed with database and tables creation');
			return;
		}
		
		if ( ! isset( $_POST[ 'host' ] ) || ! isset( $_POST[ 'db_name' ] ) ) {
			return;
		}
		//add details to Json before creating db/tables to store info, since nonce return true
		$json_details = $this->form_post_details( $_POST[ 'host' ], $_POST[ 'db_name' ], $_POST[ 'root_username' ], $_POST[ 'root_password' ], $_POST[ 'db_username' ], $_POST[ 'db_password' ] );
		if ( ! self::get_db() ) {
			//Create db and tables if db is not existent
			$this->create_db->createDB( $json_details[ 'host' ], $json_details[ 'db_name' ], $json_details[ 'root_username' ], $json_details[ 'root_password' ], $json_details[ 'db_username' ], $json_details[ 'db_password' ] );
			$this->create_db->createTables( $json_details[ 'host' ], $json_details[ 'db_name' ], $json_details[ 'db_username' ], $json_details[ 'db_password' ] );
		}
		//unset nonce variables
		unset( $_SESSION, $token, $_POST );
	}
	
	/**
	 * @return array
	 */
	public function form_post_details( $host, $db_name, $root_username, $root_password, $db_username, $db_password ) {
		$json[ 'host' ]          = $host;
		$json[ 'db_name' ]       = $db_name;
		$json[ 'root_username' ] = $root_username;
		$json[ 'root_password' ] = $root_password;
		$json[ 'db_username' ]   = $db_username;
		$json[ 'db_password' ]   = $db_password;
		$this->put_Json_content( $host, $db_name, $root_username, $root_password, $db_username, $db_password );
		
		return $json;
	}
	
	/**
	 * @return mixed
	 */
	public static function get_db() {
		//make connection and access db to check if there's already a database created with given name from $dbname
		return self::$conn;
	}
	
//	private function error_code_msg($message){
//		$this->error_code = $message;
//		return $message;
//	}
	
	
}

new dbinstall();

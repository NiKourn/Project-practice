<?php

class dbinstall {
	
	/**
	 * Connection return
	 * @var
	 */
	private $conn;
	
	/**
	 * insert create_db object into constructor and load it as a parameter
	 * @var create_db object
	 */
	private $create_db;
	
	private $json_file;
	
	function __construct() {
		if ( ! $this->get_db() ) {
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
		//include_once 'create_db.php';
		$this->create_db = new create_db();
		$this->fetch_connection();
		$this->build_db();
	}
	
	/**
	 * @return void
	 */
	private function put_Json_content($host, $db_name, $root_username, $root_password, $db_username, $db_password) {
		$json_file = 'assets/json/db-info.json';
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
		$json_file = file_get_contents( 'assets/json/db-info.json' );
		$json      = json_decode( $json_file );
		
		return $json;
	}
	
	/**
	 * @param $return
	 *
	 * @return mixed|void|null
	 */
	private function fetch_connection( ) {
		$get_json        = file_get_contents( 'assets/jSon/db-info.json' );
		$json            = json_decode( $get_json, true ); // decode the JSON into an associative array
		
		$this->json_file = $json;
		if ( ! empty( $json ) && ! $this->get_db() ) {
			$servername = $json[ 'host' ];
			$dbname     = $json[ 'db_name' ];
			$username   = $json[ 'db_username' ];
			$password   = $json[ 'db_password' ];
			
			//make connection and access db to check if there's already a database created with given name from $dbname
			
			try {
				return $this->access_db( $servername, $dbname, $username, $password);
				
			}//end try
			catch ( PDOException $e ) {
				echo 'No connection! Please check your connection details';
				includeLoader::include( 'db-form', 'Database Configuration' );
				'Fetch db error:' . $e->getMessage();
			}
		}//end if $json empty
		else {
			echo 'Configuration file is empty. Enter details here to Build Database';
			includeLoader::include( 'db-form', 'Database Configuration' );
		}
		unset( $stmt );
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
		$this->conn = $stmt->fetch();
		
			if ( $this->conn ) {
				echo '<br><h2>Database Already Created</h2>';
				die();
				Header( "Refresh:1;url=app.php" );
			} else {
				echo 'Something is wrong with your Configuration file';
				includeLoader::include( 'db-form', 'Database Configuration' );
		}
	}
	
	/**
	 * @return void
	 */
	private function build_db() {
		if ( $_SERVER[ 'REQUEST_METHOD' ] == 'POST' ) {
			
			$json_details = $this->form_post_details( $_POST[ 'host' ], $_POST[ 'db_name' ], $_POST[ 'root_username' ], $_POST[ 'root_password' ], $_POST[ 'db_username' ], $_POST[ 'db_password' ] );
			
			if ( ! $this->get_db()) {
				
					$this->create_db->createDB( $json_details[ 'host' ], $json_details[ 'db_name' ], $json_details[ 'root_username' ], $json_details[ 'root_password' ], $json_details[ 'db_username' ], $json_details[ 'db_password' ] );
					$this->create_db->createTables( $json_details[ 'host' ], $json_details[ 'db_name' ], $json_details[ 'db_username' ], $json_details[ 'db_password' ] );
				}
			} else {
				echo 'Error Establishing Database Connection';
			}
		
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
		$this->put_Json_content($host, $db_name, $root_username, $root_password, $db_username, $db_password);
		return $json;
	}
	
	/**
	 * @return mixed
	 */
	public function get_db() {
		//make connection and access db to check if there's already a database created with given name from $dbname
		return $this->conn;
	}
	
	
}

new dbinstall();

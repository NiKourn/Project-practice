<?php

class dbinstall {
	
	/**
	 * @var string
	 */
	private $title;
	
	/**
	 * Connection return
	 * @var
	 */
	private $conn;
	
	/**
	 * insert class into constructor and load it as a parameter
	 * @var create_db object
	 */
	private $create_db;
	
	function __construct() {
		include_once 'create_db.php';
		$this->create_db = new create_db();
		$this->fetch_db( );
		$this->build_db();
	}
	
	public function set_title( $title ) {
		$this->title = $title;
	}
	
	public function get_title() {
		return $this->title;
	}
	
	/**
	 * @return void
	 */
	private function fetch_db( ) {
		$this->json_contents_condition();
	}
	
	/**
	 * @return void
	 */
	private function storeJsonInfo() {
		$json_file = 'assets/json/db-info.json';
		$json_raw  = file_get_contents( $json_file );
		$json      = json_decode( $json_raw );
		$json      = [
			'host'          => $_POST[ 'host' ],
			'db_name'       => $_POST[ 'db_name' ],
			'root_username' => $_POST[ 'root_username' ],
			'root_password' => $_POST[ 'root_password' ],
			'db_username'   => $_POST[ 'db_username' ],
			'db_password'   => $_POST[ 'db_password' ]
		
		];
		$json_out  = json_encode( $json );
		file_put_contents( $json_file, $json_out );
	}
	
	/**
	 * @param $return
	 *
	 * @return mixed|void|null
	 */
	private function json_contents_condition( $return = false ) {
		$get_json = file_get_contents( 'assets/json/db-info.json' );
		$json     = json_decode( $get_json, true ); // decode the JSON into an associative array
		if ( ! empty( $json ) ) {
			$servername = $json[ 'host' ];
			$dbname     = $json[ 'db_name' ];
			$username   = $json[ 'db_username' ];
			$password   = $json[ 'db_password' ];
			//make connection and access db to check if there's already a database created with given name from $dbname
			
				try {
					$this->access_db( $servername, $dbname, $username, $password,  $return );
					
				}//end try
				catch ( PDOException $e ) {
					$title = $this->get_title();
					include 'db/db-form.php';
					'Fetch db error:' . $e->getMessage();
				}
		}//end if $json empty
		else {
			$title = $this->get_title();
			include 'db/db-form.php';
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
	private function access_db( $servername, $dbname, $username, $password, $return = false ) {
		$db    = $this->create_db->PDO_connection( $servername, $username, $password );
		$query = "SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME=?";
		$stmt  = $db->prepare( $query );
		if ( $stmt === false ) {
			return;
		}
		$stmt->bindparam( 1, $dbname );
		$stmt->execute();
		$this->conn = $stmt->fetch();
		
		if ( $return === true ) {
			return $this->conn;
			
		} else {
			if ( $this->conn ) {
				echo '<br><h2>Database Already Created</h2>';
				//Header( "Refresh:1;url=homepage.php" );
			} else {
				$title = $this->get_title();
				include 'db/db-form.php';
			}
		}
	}
	
	/**
	 * @return void
	 */
	private function build_db() {
		if ( $_SERVER[ 'REQUEST_METHOD' ] == 'POST' ) {
			$host          = $_POST[ 'host' ];
			$db            = $_POST[ 'db_name' ];
			$root_username = $_POST[ 'root_username' ];
			$root_password = $_POST[ 'root_password' ];
			$db_username   = $_POST[ 'db_username' ];
			$db_password   = $_POST[ 'db_password' ];
			$charset       = 'utf8mb4';
			
			$this->create_db->createDB( $host, $db, $root_username, $root_password, $db_username, $db_password );
			$this->create_db->createTables( $host, $db, $db_username, $db_password );
			$this->storeJsonInfo();
		}
	}
	
	/**
	 * @return void
	 */
	public function get_db() {
		//make connection and access db to check if there's already a database created with given name from $dbname
		if ( isset ( $this->conn ) ) {
			return $this->conn;
		}
	}
	
	
}

$db = new dbinstall();

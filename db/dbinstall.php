<?php

class dbinstall {
	
	/**
	 * @var string
	 */
	private $title = '';
	
	/**
	 * @var
	 */
	public $conn;
	
	function __construct( $title ) {
		$this->title = $title;
		$this->fetch_db( $title );
		$this->build_db();
	}
	
	/**
	 * @param $servername
	 * @param $root_username
	 * @param $root_password
	 *
	 * @return PDO
	 */
	private function connection( $servername, $root_username, $root_password ) {
		$conn = new PDO( "mysql:host=$servername;", $root_username, $root_password );
		$conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
		
		
		return $conn;
	}
	
	/**
	 * @param $servername
	 * @param $username
	 * @param $password
	 *
	 * @return void
	 */
	private function createDB( $servername, $dbname, $root_username, $root_password, $db_username, $db_password ) {
		
		try {
			$conn = $this->connection( $servername, $root_username, $root_password );
			$sql  = "CREATE DATABASE `$dbname`;
                CREATE USER '$db_username'@'$servername' IDENTIFIED BY '$db_password';
                GRANT ALL ON `$dbname`.* TO '$db_username'@'$servername';
                FLUSH PRIVILEGES;";
			// use exec() because no results are returned
			$conn->exec( $sql );
			echo "Database created successfully<br>";
			
		} catch ( PDOException $e ) {
			$e->getMessage();
		}
		
		$conn = null;
	}
	
	/**
	 * @param $servername
	 * @param $dbname
	 * @param $username
	 * @param $password
	 *
	 * @return void
	 */
	private function createTables( $servername, $dbname, $username, $password ) {
		
		try {
			
			$conn = new PDO( "mysql:host=$servername;dbname=$dbname", $username, $password );
			// set the PDO error mode to exception
			$conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
			
			$sql1 = "CREATE TABLE attendee (
                attendee_id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                firstname VARCHAR(50) NOT NULL,
                lastname VARCHAR(50) NOT NULL,
                dateofbirth DATE,
                contactnumber VARCHAR(15),
                emailaddress VARCHAR(50),
                specialty_id INT(11),
                avatar_path VARCHAR(200),
                reg_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                )";
			
			$sql2 = "CREATE TABLE specialties (
                specialty_id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(50),
                reg_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                )";
			
			$sql3 = "CREATE TABLE users (
                id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                username VARCHAR(50),
                password VARCHAR(50),
                reg_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                )";
			
			$sql4 = "INSERT INTO `specialties`(`name`) VALUES ('Database Admin'), ('Software Devs'), ('Server Admins'), ('Other')";
			
			$sqlqs = [ $sql1, $sql2, $sql3, $sql4 ];
			
			// use foreach to browse through arrays and only PDOexec() because no results are returned
			foreach ( $sqlqs as $sql ) {
				$conn->exec( $sql );
				
				echo "Tables created successfully <br>";
				Header( "Refresh:3;url=homepage.php" );
				
			}
		} catch ( PDOException $e ) {
			echo "<br>" . $e->getMessage();
			//Header("Refresh:1;url=homepage.php");
		}
		
		$conn = null;
	}
	
	
	/**
	 * @param $title
	 *
	 * @return mixed|void
	 */
	private function fetch_db( $title = '' ) {
		$this->json_contents_condition( false, $title );
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
	 * @param $title
	 *
	 * @return mixed|void|null
	 */
	private function json_contents_condition( $return = false, $title = '' ) {
		$get_json = file_get_contents( 'assets/json/db-info.json' );
		$json     = json_decode( $get_json, true ); // decode the JSON into an associative array
		
		if ( ! empty( $json ) ) {
			$servername = $json[ 'host' ];
			$dbname     = $json[ 'db_name' ];
			$username   = $json[ 'db_username' ];
			$password   = $json[ 'db_password' ];
			//make connection and access db to check if there's already a database created with given name from $dbname
			
			if ( $return === true ) {
				return $this->access_db( $servername, $dbname, $username, $password, $return );
			} else {
				try {
					$this->access_db( $servername, $dbname, $username, $password, $return, $title );
					
				}//end try
				catch ( PDOException $e ) {
					include 'db/db-form.php';
					'Fetch db error:' . $e->getMessage();
				}
			}
		}//end if $json empty
		elseif ( $return === false ) {
			include 'db/db-form.php';
		}
		unset( $stmt );
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
			$this->createDB( $host, $db, $root_username, $root_password, $db_username, $db_password );
			$this->createTables( $host, $db, $db_username, $db_password );
			$this->storeJsonInfo();
		}
	}
	
	/**
	 * @return mixed|null
	 */
	public function get_db() {
		//make connection and access db to check if there's already a database created with given name from $dbname
		if ( isset ( $this->conn ) ) {
			return $this->json_contents_condition( true );
		}
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
	private function access_db( $servername, $dbname, $username, $password, $return = false, $title = '' ) {
		$db    = $this->connection( $servername, $username, $password );
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
				include 'db/db-form.php';
			}
		}
	}
	
	
}

$db = new dbinstall( $title );

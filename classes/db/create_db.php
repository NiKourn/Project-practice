<?php

class create_db {
	
	/**
	 * @param $servername
	 * @param $username
	 * @param $password
	 * @param $dbname
	 * @param $before_creating_db
	 *
	 * @return PDO
	 */
	public function PDO_connection( $servername, $username, $password, $dbname = '', $before_creating_db = true ) {
		
		if ( $before_creating_db === true ) {
			$conn = new PDO( "mysql:host=$servername;", $username, $password );
			// set the PDO error mode to exception
			$conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
		} else {
			$conn = new PDO( "mysql:host=$servername;dbname=$dbname", $username, $password );
			// set the PDO error mode to exception
			//PDO::ATTR_PERSISTENT => true;
			$conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
		}
		
		return $conn;
	}
	
	/**
	 * @param $servername
	 * @param $dbname
	 * @param $root_username
	 * @param $root_password
	 * @param $db_username
	 * @param $db_password
	 *
	 * @return void
	 */
	public function createDB( $servername, $dbname, $root_username, $root_password, $db_username, $db_password ) {
		
		try {
			$conn = $this->PDO_connection( $servername, $root_username, $root_password );
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
	 * @return string
	 */
	private function create_table_attendee() {
		
		$query = "CREATE TABLE attendee (
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
		
		return $query;
	}
	
	/**
	 * @return string
	 */
	private function create_table_specialties() {
		$query = "CREATE TABLE specialties (
                specialty_id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(50),
                reg_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                )";
		
		return $query;
	}
	
	/**
	 * @return string
	 */
	private function create_table_users() {
		$query = "CREATE TABLE users (
                id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                username VARCHAR(50),
                password VARCHAR(50),
                reg_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                )";
		
		return $query;
	}
	
	private function create_table_options() {
		$query = "CREATE TABLE options (
                id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                option_name VARCHAR(50),
                option_value VARCHAR(50)
                )";
		
		return $query;
	}
	
	/**
	 * @return string
	 */
	private function create_table_specialties_values() {
		$query = "INSERT INTO `specialties`(`name`) VALUES ('Database Admin'), ('Software Devs'), ('Server Admins'), ('Other')";
		
		return $query;
	}
	
	/**
	 * @param $servername
	 * @param $dbname
	 * @param $username
	 * @param $password
	 *
	 * @return void
	 */
	public function createTables( $servername, $dbname, $username, $password ) {
		
		try {
			$conn                     = $this->PDO_connection( $servername, $username, $password, $dbname, false );
			$create_tables_into_array = [
				$this->create_table_attendee(),
				$this->create_table_specialties(),
				$this->create_table_users(),
				$this->create_table_options(),
				$this->create_table_specialties_values()
			];
			
			// use foreach to browse through arrays and only PDOexec() because no results are returned
			foreach ( $create_tables_into_array as $query ) {
				$conn->exec( $query );
				echo "Tables created successfully <br>";
				Header( "Refresh:3;url=app.php" );
				
			}
		} catch ( PDOException $e ) {
			echo "<br>" . $e->getMessage();
			//Header("Refresh:1;url=index.php");
		}
		
		$conn = null;
	}
}
<?php

abstract class create_db {
	
	function __construct() {
	
	}
	
	/**
	 * @param $servername
	 * @param $root_username
	 * @param $root_password
	 * @param $dbname
	 * @param $before_creating_db
	 *
	 * @return PDO|void
	 */
	public function PDO_connection( $args, $before_creating_db = true ) {
		if ( $before_creating_db === true ) {
			$conn = new PDO( "mysql:host={$args[ 'host' ]};", $args[ 'db_username' ], $args[ 'db_password' ] );
			// set the PDO error mode to exception
			$conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
			
			return $conn;
			
		} else {
			
			$conn = new PDO( "mysql:host={$args[ 'host' ]};dbname={$args[ 'db_name' ]}", $args[ 'db_username' ], $args[ 'db_password' ] );
			// set the PDO error mode to exception
			//PDO::ATTR_PERSISTENT => true;
			$conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
			
			return $conn;
		}
		
	}
	
	/**
	 * @param array $args
	 *
	 * @return void
	 */
	protected function createDB( array $args ) {
		try {
			$conn = $this->PDO_connection( $args );
			$conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
			$sql = "CREATE DATABASE IF NOT EXISTS `{$args['db_name']}`;
                    CREATE USER IF NOT EXISTS `{$args['db_username']}`@'%' IDENTIFIED BY '{$args['db_password']}';
                    GRANT ALL ON `{$args['db_name']}`.* TO '{$args['db_username']}'@'%';
                    FLUSH PRIVILEGES;";
			// use exec() because no results are returned
			$conn->exec( $sql );
			echo "Database created successfully<br>";
			
		} catch ( PDOException $e ) {
			$e->getMessage();
		}
		
	}
	
	/**
	 * @return string
	 */
	private function create_table_attendee() {
		
		$query = "CREATE TABLE IF NOT EXISTS attendee (
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
		$query = "CREATE TABLE IF NOT EXISTS specialties (
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
		$query = "CREATE TABLE IF NOT EXISTS users (
                id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                username VARCHAR(50),
                password VARCHAR(50),
                reg_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                )";
		
		return $query;
	}
	
	/**
	 * @return string
	 */
	private function create_table_options() {
		$query = "CREATE TABLE IF NOT EXISTS options (
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
		$query = "INSERT INTO IF NOT EXISTS `specialties`(`name`) VALUES ('Database Admin'), ('Software Devs'), ('Server Admins'), ('Other')";
		
		return $query;
	}
	
	/**
	 * @param $args
	 *
	 * @return void
	 */
	protected function createTables( array $args ) {
		
		try {
			$conn                     = $this->PDO_connection( $args, false );
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
			}
		} catch ( PDOException $e ) {
			echo "<br>" . $e->getMessage();
		}
		
		$conn = null;
	}
}
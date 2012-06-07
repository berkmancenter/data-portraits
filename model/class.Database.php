<?php

class Database {
	
    private $connection;
    public $last_query;
    private $magic_quotes_active;
    private $real_escape_string_exists;
    
    function __construct() {
	$this->openConnection();
	$this->magic_quotes_active = get_magic_quotes_gpc();
	$this->real_escape_string_exists = function_exists( "mysql_real_escape_string" );
    }
    
    public function openConnection() {
	$this->connection = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
	if (!$this->connection) {
	    die("Database connection failed: " . mysql_error());
	} else {
	    $db_select = mysql_select_db(DB_NAME, $this->connection);
	    if (!$db_select) {
		die("Database selection failed: " . mysql_error());
	    }
	}
    }
    
    public function closeConnection() {
	if(isset($this->connection)) {
	    mysql_close($this->connection);
	    unset($this->connection);
	}
    }
    
    public function query($sql) {
	$this->last_query = $sql;
	$result = mysql_query($sql, $this->connection);
	$this->confirmQuery($result);
	return $result;
    }
    
    // "database-neutral" methods
    public function fetchArray($result_set) {
	return mysql_fetch_array($result_set);
    }
    
    public function numRows($result_set) {
	return mysql_num_rows($result_set);
    }
  
    public function insertID() {
	// get the last id inserted over the current db connection
	return mysql_insert_id($this->connection);
    }
    
    public function affectedRows() {
	return mysql_affected_rows($this->connection);
    }
    
    private function confirmQuery($result) {
	if (!$result) {
	    $output = "Database query failed: ".mysql_error()."<br/><br/>";
	    die($output);
	}
    }
}
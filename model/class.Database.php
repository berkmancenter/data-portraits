<?php
/**
 *
 * Data-Portraits/model/class.Database.php
 * Class for Database object
 *
 * Copyright (c) 2012 Berkman Center for Internet and Society, Harvard Univesity
 *
 * LICENSE:
 *
 * This file is part of Data Portraits Project (http://cyber.law.harvard.edu/dataportraits/Main_Page).
 *
 * Data Portraits is a free software: you can redistribute it and/or modify it under the terms of the GNU General Public
 * License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * Data Portraits is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more
 * details.
 *
 * You should have received a copy of the GNU General Public License along with Data Portraits.  If not, see
 * <http://www.gnu.org/licenses/>.
 *
 *
 * @author Ekansh Preet Singh <ekanshpreet[at]gmail[dot]com>
 * @author Judith Donath <jdonath[at]cyber[dot]law[dot]harvard[dot]edu>
 * @license http://www.gnu.org/licenses/gpl.html
 * @copyright 2012 Berkman Center for Internet and Society, Harvard University
 * 
 */

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
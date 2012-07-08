<?php
/**
 *
 * Data-Portraits/model/class.UserDetailsTable.php
 * Model for retrieving / inserting data into the User Details table.
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

class UserDetailsTable {
    
    public static function addData($username, $type, $data) {
        global $db;
        $data = addslashes(json_encode($data));
        $sql = "INSERT INTO userdetails(username, type, userdata) VALUES
                ('$username','$type','$data');";
        $query = $db->query($sql);
        $result = $db->affectedRows($query);
        return $result;
    }
    
    public static function retrieveData($username) {
        global $db;
        $sql = "SELECT * FROM userdetails WHERE username='$username'";
        $query = $db->query($sql);
        $result = array();
        while ($row = $db->fetchArray($query)) {
            $row['userdata'] = json_decode($row['userdata']);
            array_push($result, $row);
        }
        return $result;
    }
    
    public static function deleteUserData($username) {
        global $db;
        $sql = "DELETE FROM userdetails WHERE username='$username'";
        $query = $db->query($sql);
        $result = $db->affectedRows($query);
        return $result;
    }
    
}
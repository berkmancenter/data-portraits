<?php
/**
 *
 * Data-Portraits/model/class.UsersTable.php
 * Class for retrieving/inserting data into Users table
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
class UsersTable {
    
    public static function findByID($id) {
        global $db;
        $sql = "SELECT * FROM users WHERE oauth_provider = 'twitter' AND
                oauth_uid = ". $id;
        $query = $db->query($sql);
        $result = $db->fetchArray($query);
        return $result;
    }
    
    public static function addUser($vals) {
        global $db;
        $sql = "INSERT INTO users (oauth_provider, oauth_uid, username,
                oauth_token, oauth_secret) VALUES ('twitter', {$vals['id']},
                '{$vals['screen_name']}', '{$vals['oauth_token']}',
                '{$vals['oauth_token_secret']}')";
        $query = $db->query($sql);
        $sql = "SELECT * FROM users WHERE id = ".$db->insertID();
        $query = $db->query($sql);
        $result = $db->fetchArray($query);
        return $result;
    }
    
    public static function updateUser($vals) {
        global $db;
        $sql = "UPDATE users SET oauth_token = '{$vals['oauth_token']}',
                oauth_secret = '{$vals['oauth_token_secret']}' WHERE
                oauth_provider = 'twitter' AND oauth_uid = {$vals['id']}";
        $db->query($sql);
    }
    
}
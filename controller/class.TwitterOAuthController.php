<?php
/**
 *
 * Data-Portraits/controller/class.TwitterOAuthController.php
 * Class for Signing in using Twitter
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

require_once("init.php");
require_once(ROOT_PATH."/model/class.UsersTable.php");

class TwitterOAuthController {
    
    public function go() {
        if (!empty($_GET['oauth_verifier']) && !empty($_SESSION['oauth_token']) &&
            !empty($_SESSION['oauth_token_secret'])) {
            
            // TwitterOAuth instance, with two new parameters we got in twitter_login.php
            $twitteroauth = new TwitterOAuth(CONSUMER_KEY, CONSUMER_KEY_SECRET,
                                             $_SESSION['oauth_token'],
                                             $_SESSION['oauth_token_secret']);
            
            // request the access token
            $access_token = $twitteroauth->getAccessToken($_GET['oauth_verifier']);
            
            // Saving it in a session var
            $_SESSION['access_token'] = $access_token;
            
            $user_info = $twitteroauth->get('account/verify_credentials');
            
            if(isset($user_info->error)) {
                // Error
                header('Location: '.LOGIN_LINK);
            } else {
                // find the user by its ID
                $result = UsersTable::findByID($user_info->id);
                
                $vals = array(
                    'oauth_token' => $access_token['oauth_token'],
                    'oauth_token_secret' => $access_token['oauth_token_secret'],
                    'id' => $user_info->id,
                    'screen_name' => $user_info->screen_name
                );
                
                // If not, add it to the database
                if(empty($result)){
                    $result = UsersTable::addUser($vals);
                } else {
                    // Update the tokens
                    $query = UsersTable::updateUser($vals);
                }
            
                $_SESSION['id'] = $result['id'];
                $_SESSION['dp_username'] = $result['username'];
                $_SESSION['oauth_uid'] = $result['oauth_uid'];
                $_SESSION['oauth_provider'] = $result['oauth_provider'];
                $_SESSION['oauth_token'] = $result['oauth_token'];
                $_SESSION['oauth_secret'] = $result['oauth_secret'];
            
                if(!empty($_SESSION['dp_username'])){
                    // User is logged in, redirect
                    $url = SITE_ROOT_PATH."pages/home.php";
                    header('Location: '. $url);
                }
            }
            
        } else {
            // error
            header('Location: '.LOGIN_LINK);
        }
    }
}



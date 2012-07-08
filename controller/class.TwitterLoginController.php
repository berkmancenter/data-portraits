<?php
/**
 *
 * Data-Portraits/controller/class.TwitterLoginController.php
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
class TwitterLoginController {
    
    public function go() {        
        // The TwitterOAuth instance
        $twitteroauth = new TwitterOAuth(CONSUMER_KEY, CONSUMER_KEY_SECRET);

        // Requesting authentication tokens, the parameter is the URL we will be redirected to
        $request_token = $twitteroauth->getRequestToken(REDIRECT_LINK);
        
        // Saving them into the session
        $_SESSION['oauth_token'] = $request_token['oauth_token'];
        $_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];
        
        // If everything goes well..
        if($twitteroauth->http_code==200) {
            // Generate the URL and redirect
            $url = $twitteroauth->getAuthorizeURL($_SESSION['oauth_token']);
            header('Location: '. $url);
        } else {
            die('Something wrong happened.');
        }
    }
}
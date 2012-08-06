<?php
/**
 *
 * Data-Portraits/controller/class.TopicModellingController.php
 * Class for creating the Topic Modelling page
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
require_once(ROOT_PATH."/controller/class.DPController.php");

class TopicModellingController extends DPController {
    
    public function go() {
        
        if (get_magic_quotes_gpc()) {
            $statuses = "var statuses = ".stripcslashes($_POST['statuses']).";";
        } else {
	    $statuses = "var statuses = ".$_POST['statuses'].";";
        }
        
        $this->addToView('statuses', $statuses);
        $this->setViewTemplate('topics.tpl');
        return $this->generateView();
    }
    
}
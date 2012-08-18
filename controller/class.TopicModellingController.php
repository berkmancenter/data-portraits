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
require_once(ROOT_PATH."/model/class.TopicModel.php");

class TopicModellingController extends DPController {
    
    public function go() {
        if (get_magic_quotes_gpc()) {
            $statuses = json_decode(stripcslashes($_POST['statuses']));
        } else {
	    $statuses = json_decode($_POST['statuses']);
        }
        $model = new TopicModel();
	$result = $model->analyse($statuses);
	$num = "var num = ".json_encode($result['num']).";";
	$tweets = "var tweets = ".json_encode($result['tweets']).";";
	$topic_text = "var topic_text = ".json_encode($result['topic_text']).";";
	$topic_text_values = "var topic_text_values = ".json_encode($result['topic_text_values']).";";
	$this->addToView('num', $num);
	$this->addToView('tweets', $tweets);
	$this->addToView('topic_text', $topic_text);
	$this->addToView('topic_text_values', $topic_text_values);
        //$this->addToView('statuses', $statuses);
        //$this->setViewTemplate('topics.tpl');
        //return $this->generateView();
	$this->setViewTemplate('topics_new.tpl');
	return $this->generateView();
    }
    
}
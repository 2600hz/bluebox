<?php defined('SYSPATH') or die('No direct access allowed.');
/*
 * Bluebox Modular Telephony Software Library / Application
 *
 * Module:
 * 
 * The contents of this file are subject to the Mozilla Public License
 * Version 1.1 (the "License"); you may not use this file except in
 * compliance with the License. You may obtain a copy of the License at
 * http://www.mozilla.org/MPL/
 * 
 * Software distributed under the License is distributed on an "AS IS"
 * basis, WITHOUT WARRANTY OF ANY KIND, either express or implied. See the
 * License for the specific language governing rights and limitations
 * under the License.
 * 
 * The Initial Developer of the Original Code is Michael Phillips <michael.j.phillips@gmail.com>.
 * 
 * Portions created by the Initial Developer are Copyright (C)
 * the Initial Developer. All Rights Reserved.
 *  
 * Contributor(s): 
 *  
 *
 */
/**
 * esl.php - Sofia Viewer Module
 *
 * @author Michael Phillips <michael.j.phillips@gmail.com>
 * @author Jon Blanton <jon@2600hz.com>
 * @author Rockwood Cataldo <rocco@2600hz.com>
 * @license MPL
 * @package Esl
 */

class switchboard_Controller extends Bluebox_Controller
{
    public function index()
    {
        stylesheet::add('switchboard', 50);
        javascript::add('pubsub.js', 50);

        $this->view->sofia_status = '';
        $sipInterface = Doctrine::getTable('SipInterface')->findAll();
        foreach($sipInterface as $interface)
        {
            $this->view->sofia_status .= form::button(array(
                'id' => 'sofia_profile',
                'class' => 'eslEvent',
                'value' => 'SIP Interface ' . $interface->sipinterface_id .' Status',
                'param' => 'sipinterface_' . $interface->sipinterface_id
            ));
        }

        $this->view->trunk_status = '';
        $trunks = Doctrine::getTable('Trunk')->findAll();
        foreach($trunks as $trunk)
        {
            $this->view->trunk_status .= form::button(array(
                'id' => 'gateway_profile',
                'class' => 'eslEvent',
                'value' => 'Trunk ' . $trunk->trunk_id .' Status',
                'param' => 'trunk_' . $trunk->trunk_id
            ));
        }

        //CLEAR THE ESL SESSION
        $_SESSION['esl'] = array();

    }
   

    public function eslresponse() {
        $this->auto_render = FALSE;
        $event = $_POST['event'];
        
        $eslManager = new EslManager();
        
        switch ($event) {
            case 'switchboard/manual_entry':
                if($eslManager->isConnected()) {
                    $result = $eslManager->sendRecv("api " . $_POST['param']);
                    $response = $eslManager->getResponse($result);
                }
                // Can't connect to Freeswitch
                else {
                    $response = "Cannot connect to Freeswitch";
                }
                break;
        }


        echo $response;
        flush();
        die();
    }


    public function fluxresponse() {
        //Turn off the view
        $this->auto_render = FALSE;
        //Set the timeout for the http request (in seconds)
        //Until flux handles firefox correctly, set this to 5
        $TIMEOUT = 5;

        $response = array();

        $eslManager = new EslManager();

        $subscribers = $_POST['subscribers'];

        $starttime = time();
        $exectime = 0;
        while(sizeof($response) == 0 && $exectime < $TIMEOUT) {
            foreach ($subscribers as $subscriber) {
                switch($subscriber) {
                    case "switchboard/numactivecalls":
                        if($eslManager->isConnected()) {
                            $result = $eslManager->calls();
                            $text = $eslManager->getResponse($result);
                            preg_match("/[0-9]+(?=\stotal\.)/", $text, $output);
                            $event = array("name" => $subscriber, "data" => array($output[0]));
                        }
                        // Can't connect to Freeswitch
                        else {
                            $event = array("name" => $subscriber, "data" => array("N\\A"));
                        }
                        
                        break;

                    case "switchboard/numactivemodules":
                        if($eslManager->isConnected()) {
                            $result = $eslManager->show('modules');
                            $text = $eslManager->getResponse($result);
                            preg_match("/[0-9]+(?=\stotal\.)/", $text, $output);
                            $event = array("name" => $subscriber, "data" => array($output[0]));
                        }
                        // Can't connect to Freeswitch
                        else {
                            $event = array("name" => $subscriber, "data" => array("N\\A"));
                        }

                        break;

                    case "switchboard/numactivechannels":
                        if($eslManager->isConnected()) {
                            $result = $eslManager->channels();
                            $text = $eslManager->getResponse($result);
                            preg_match("/[0-9]+(?=\stotal\.)/", $text, $output);
                            $event = array("name" => $subscriber, "data" => array($output[0]));
                        }
                        // Can't connect to Freeswitch
                        else {
                            $event = array("name" => $subscriber, "data" => array("N\\A"));
                        }

                        break;
                    case "switchboard/channels":
                        if($eslManager->isConnected()) {
                            $result = $eslManager->channels();
                            $text = $eslManager->getResponse($result);
                            $event = array("name" => $subscriber, "data" => array($text));
                        }
                        // Can't connect to Freeswitch
                        else {
                            $event = array("name" => $subscriber, "data" => array("N\\A"));
                        }

                        break;

                    case "switchboard/numactivecodecs":
                        if($eslManager->isConnected()) {
                            $result = $eslManager->show('codecs');
                            $text = $eslManager->getResponse($result);
                            preg_match("/[0-9]+(?=\stotal\.)/", $text, $output);
                            $event = array("name" => $subscriber, "data" => array($output[0]));
                        }
                        // Can't connect to Freeswitch
                        else {
                            $event = array("name" => $subscriber, "data" => array("N\\A"));
                        }

                        break;

                    case "switchboard/modules":
                        if($eslManager->isConnected()){
                            $result = $eslManager->show('modules');
                            $text = $eslManager->getResponse($result);
                            preg_match_all("/(?<=\,)mod_[A-Za-z_0-9]+(?=,)/", $text, $output, PREG_PATTERN_ORDER);

                            if(isset($output)) {

                                // A dirty hack to eliminate duplicates
                                $matches = array();
                                foreach($output[0] as $value) {
                                    $matches[$value] = 1337;
                                }

                                $text = "";
                                foreach($matches as $key => $value) {
                                    $text .= $key . ',';
                                }
                            }
                            else {
                                $text = "No modules found... I think something is broken.";
                            }

                            $event = array("name" => $subscriber, "data" => array($text));
                        }
                        else{
                            $event=array("name" => $subscriber, "data" => array("Freeswitch not loaded.."));
                        }

                        break;

                    case "switchboard/sipinterfaces":
                        if($eslManager->isConnected()) {
                            $result = $eslManager->sofia('status');
                            $text = $eslManager->getResponse($result);
                            $event = array("name" => $subscriber, "data" => array($text));
                        }
                        // Can't connect to Freeswitch
                        else {
                            $event = array("name" => $subscriber, "data" => array("N\\A"));
                        }

                        break;

                    case "switchboard/calls":
                        if($eslManager->isConnected()) {
                            $result = $eslManager->calls();
                            $text = $eslManager->getResponse($result);
                            $event = array("name" => $subscriber, "data" => array($text));
                        }
                        // Can't connect to Freeswitch
                        else {
                            $event = array("name" => $subscriber, "data" => array("N\\A"));
                        }

                        break;

                    case "switchboard/activecalls":
                        if($eslManager->isConnected()) {
                            $result = $eslManager->calls();
                            $text = $eslManager->getResponse($result);

                            $output = explode("\n", $text);



                            $event = array("name" => $subscriber, "data" => array($text));
                        }
                        // Can't connect to Freeswitch
                        else {
                            $event = array("name" => $subscriber, "data" => array("N\\A"));
                        }

                        break;

                    case "switchboard/uptime":
                        if($eslManager->isConnected()) {
                            $result = $eslManager->status();
                            $text = $eslManager->getResponse($result);
                            preg_match("/[0-9]+(?=\syear)/", $text, $output);
                            $years = $output[0];
                            preg_match("/[0-9]+(?=\sday)/", $text, $output);
                            $days = $output[0];
                            preg_match("/[0-9]+(?=\shour)/", $text, $output);
                            $hours = $output[0];
                            preg_match("/[0-9]+(?=\sminute)/", $text, $output);
                            $mins = $output[0];
                            preg_match("/[0-9]+(?=\ssecond)/", $text, $output);
                            $secs = $output[0];

                            $text = "";

                            if($years > 0) {
                                $text .= $years . " year";

                                //Plurar check
                                if($years > 1) {
                                    $text .= "s";
                                }

                                //check if there will be another field
                                if($days + $hours + $mins > 0) {
                                    $text .= ", ";
                                }
                            }
                            if($days > 0) {
                                $text .= $days . " day";

                                //Plurar check
                                if($days > 1) {
                                    $text .= "s";
                                }

                                //check if there will be another field
                                if($hours + $mins > 0) {
                                    $text .= ", ";
                                }
                            }
                            if($hours > 0) {
                                $text .= $hours . " hour";

                                //Plurar check
                                if($hours > 1) {
                                    $text .= "s";
                                }

                                //check if there will be another field
                                if($mins > 0) {
                                    $text .= ", ";
                                }
                            }
                            if($mins > 0) {
                                $text .= $mins . " minute";

                                //Plurar check
                                if($mins > 1) {
                                    $text .= "s";
                                }
                            }
                            if($mins + $hours + $days + $years == 0 && $secs > 0) {
                                $text .= "<0 minutes";
                            }

                            $event = array("name" => $subscriber, "data" => array($text));
                            
                        }
                        // Can't connect to Freeswitch
                        else {
                            $event = array("name" => $subscriber, "data" => array("(Server is down)"));
                        }

                        break;

                    case "switchboard/logviewer":
                        $logfile = "/usr/local/freeswitch/log/freeswitch.log";
                        if(!isset($_SESSION["esl"]["logviewer_pos"])) {
                            $logviewer_pos = filesize($logfile) - 1200;
                            if($logviewer_pos < 0) {
                                $logviewer_pos = 0;
                            }
                        }
                        else {
                            $logviewer_pos = $_SESSION["esl"]["logviewer_pos"];
                        }

                        $text = "";

                        $log_pointer = fopen($logfile, "r");
                        fseek($log_pointer, $logviewer_pos);

                        //Skip to the first complete line....
                        while(($char = fgetc($log_pointer)))
                            if ($char == "\n") break;

                        while(!feof($log_pointer)) {
                            $line = fgets($log_pointer);
                            if(trim($line) != "")
                                $text .= $line;
                        }
                        
                        $_SESSION["esl"]["logviewer_pos"] = ftell($log_pointer);
                        fclose($log_pointer);
                        $event = array("name" => $subscriber, "data" => array($text));
                        break;

                    default:
                        $event = NULL;
                        break;
                }

                if(isset($event)) {
                   if(isset($_SESSION["esl"][$subscriber])) {
                        if($_SESSION["esl"][$subscriber] == $event["data"]) {
                            continue;
                        }
                   }

                   $_SESSION["esl"][$subscriber] = $event["data"];
                   $response[] = $event;
                }
            }

            $exectime = time() - $starttime;
       }
       
       echo json_encode($response);
       flush();
       die();
    }
}


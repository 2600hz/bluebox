<?php defined('SYSPATH') or die('No direct access allowed.');
/*
 * FreePBX Modular Telephony Software Library / Application
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
 * @license MPL
 * @package Esl
 */

class Esl_Controller extends FreePbx_Controller
{
    //protected $noAuth = array('index', 'eslRepsonse');
    public function index()
    {
        stylesheet::add('esl', 50);

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

        $eslManager = new EslManager();
        $this->view->isConnected = $eslManager->isConnected();
        $this->view->isExtension = $eslManager->isExtension();
    }
    
    public function eslreponse()
    {
        $eslManager = new EslManager();

        $errors = message::render(NULL, array(
            'html' => TRUE,
            'htmlTemplate' => '{text}' ."\n",
            'growl' => FALSE,
            'inline' => FALSE
        ));

        if (!empty($errors['html'])) {
            echo implode('', $errors['html']);
        }

        if(!$eslManager->isConnected())
        {
            if (empty($errors['html']))
                echo __("Failed to load ESL. Check logs");
            flush();
            die();
        }

        switch($_POST['type'])
        {
            case 'status':
                $result = $eslManager->status();
                break;

            case 'version':
                $result = $eslManager->version();
                break;

            case 'reloadacl':
                $result = $eslManager->reloadacl();
                break;

            case 'reloadxml':
                $result = $eslManager->reloadxml();
                break;

            case 'channels':
                $result = $eslManager->channels();
                break;

            case 'calls':
                $result = $eslManager->calls();
                break;

            case 'show_codec':
                $result = $eslManager->show('codec');
                break;

            case 'show_modules':
                $result = $eslManager->show('modules');
                break;

            case 'nat_status':
                $result = $eslManager->nat('status');
                break;

            case 'nat_reinit':
                $result = $eslManager->nat('reinit');
                break;

            case 'nat_republish':
                $result = $eslManager->nat('republish');
                break;

            case 'reload_sofia':
                $result = $eslManager->reload('mod_sofia');
                break;

            case 'sofia_status':
                $result = $eslManager->sofia('status');
                break;

            case 'sofia_profile':
                $result = $eslManager->sofia('status', 'profile', $_POST['param']);
                break;

            case 'gateway_profile':
                $result = $eslManager->sofia('status', 'gateway', $_POST['param']);
                break;

            case 'recvEvent':
                if (empty($_POST['param'])) {
                    $result = '== HELP ==' ."\n";
                    $result .= __('Please use the text input to enable an event listener that you are interested in.') ."\n";
                    $result .= 'Example: events plain all' ."\n";
                    break;
                }
                $result = $eslManager->sendRecv($_POST['param']);
                $reply = $result->getHeader('Reply-Text');
                if (!strstr($reply, '+OK event listener enabled')) {
                    $result = '== HELP ==' ."\n";
                    $result .= __('The supplied command failed to start an event listener, please ensure you are using the events command.') ."\n";
                    break;
                }
                $result = $eslManager->recvEvent();
                break;

            case 'sendRecv':
                if (empty($_POST['param'])) {
                    $result = '== HELP ==' ."\n";
                    $result .= 'Please use the text input to provide a command to execute.' ."\n";
                    $result .= 'Example: api version' ."\n";
                    break;
                }
                $result = $eslManager->sendRecv($_POST['param']);
                break;

            default:
                $result =  $_POST['type'] .__(' command not supported by FreePBX ESL plugin.');
                break;
        }

        $text = $eslManager->getResponse($result);
        echo htmlentities($text);
        flush();
        die();
    }
}

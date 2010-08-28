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
 * Michael Phillips
 *
 *
*/

/**
 * @author Your Name <your@email.org>
 * @license Your License
 * @package _Skeleton
 */
class Xmlcdr_Controller extends Bluebox_Controller {
   protected $authBypass = array('service');
   protected $baseModel = 'Xmlcdr';

    public function  index() {

        $this->template->content = new View('generic/grid');

        // Setup the base grid object
        $grid = jgrid::grid($this->baseModel, array(
                'caption' => 'Caller Detail Records',
                'sortname' => 'x.start_stamp',
                'sortorder' => 'desc'
                )
        );

        // Add the base model columns to the grid
        $grid->add('xml_cdr_id', 'ID', array(
                'hidden' => true,
                'key' => true
                )
        );
        $grid->add('direction', 'Direction', array(
                'width' => '120'
                )
        );
        $grid->add('caller_id_name', 'Caller Name', array(
                'width' => '250'
                )
        );
        $grid->add('caller_id_number', 'Caller Number', array(
                'width' => '250',
                'callback' => array($this, 'formatNumber')
                )
        );
        $grid->add('destination_number', 'Destination', array(
                'width' => '250',
                'callback' => array($this, 'formatNumber')
                )
        );

        $grid->add('start_stamp', 'Start', array('width' => 250, 'callback' => array($this, 'formatDate')));
        $grid->add('duration', 'Duration', array('callback' => array($this, 'formatDuration')));
        //$grid->add('uuid', 'Recording', array('callback' => array($this, 'playLink')));


        // Add the actions to the grid
        $grid->addAction('xmlcdr/details', 'Details', array(
                'arguments' => 'xml_cdr_id'
                )
        );


        // Let plugins populate the grid as well
        $this->grid = $grid;
        plugins::views($this);

        // Produce a grid in the view
        $this->view->grid = $this->grid->produce();

//             $this->template->content = new View('xmlcdr/index');

    }

    public function details($xml_cdr_id) {


        $xmlcdr = Doctrine::getTable('Xmlcdr')->findOneBy('xml_cdr_id', $xml_cdr_id);

        $idx = array(
                'Caller Name' => 'caller_id_name',
                'Caller Number' => 'caller_id_number',
                'Direction' => 'direction',
                'Desintation Number' => 'destination_number',
                'User Name' => 'user_name',
                'Context' => 'context',
                'Start' => 'start_stamp',
                'Answer' => 'answer_stamp',
                'End' => 'end_stamp',
                'Duration' => 'duration',
                'Billable Seconds' => 'billsec',
                'Hangup Cause' => 'hangup_cause',
                'UUID' => 'uuid',
                'B-Leg UUID' =>  'bleg_uuid',
                'Account Code' => 'accountcode',
                'Domain Name' => 'domain_name',
                'User Context' => 'user_context',
                'Read Codec' => 'read_codec',
                'Write Codec' => 'write_codec',
                'Dialed Domain' => 'dialed_domain',
                'Dialed User' => 'dialed_user'
        );

        $details = '<h3>CDR</h3>';
        $details .= '<table>';
        foreach($idx as $k => $p) {
            $details .=  "<tr><td width=\"300px\">{$k}</td><td>{$xmlcdr->$p}</td></tr>\n";
        }

        $details .= '</table>';

        $details .= '<h3>Listen</h3>';

        if(file_exists($this->getFile($xmlcdr->uuid))) {
            $details .= $this->playLink($xmlcdr->uuid);
        } else {
            $details .= 'No file found';
        }

        $this->template->content = new View('xmlcdr/details');
        $this->template->content->details = $details;
    }


    public function playLink($uuid) {

        return sprintf('<audio src="%s" controls="controls">No audio tag suport</audio>',  url::site('xmlcdr/listen/' . $uuid));

    }

    public function listen( $uuid) {
        $this->auto_render = FALSE;

        $file = $this->getFile($uuid);
        if(!file_exists($file)) {
            Kohana::log('error', 'Can\'t access file: '  . $file);
            return;
        }

        header("Content-type: audio/wav");
        header('Content-Length: '.filesize($file));
        readfile($file);
        die();
    }

    public function service($key = NULL) {
        $this->auto_render = FALSE;


        if($this->input->post()) {
            $xml = $this->input->post('cdr');
            XmlcdrManager::addXMLCDR($xml);
        } else {
            $error =  "NO CDR RECORD FOUND IN POST HEADER";
            echo $error;
            Kohana::log('error', $error);

        }
    }
    private function getBasePath() {
        return '/usr/local/freeswitch/recordings/';
    }

    private function getRecordingExtension() {
        return '.wav';
    }

    private function getFile($uuid) {
        return $this->getBasePath() . $uuid . $this->getRecordingExtension();
    }

    public function formatDuration ($sec, $padHours = false) {

        $hms = "";

        // there are 3600 seconds in an hour, so if we
        // divide total seconds by 3600 and throw away
        // the remainder, we've got the number of hours
        $hours = intval(intval($sec) / 3600);

        // add to $hms, with a leading 0 if asked for
        $hms .= ($padHours)
                ? str_pad($hours, 2, "0", STR_PAD_LEFT). ':'
                : $hours. ':';

        // dividing the total seconds by 60 will give us
        // the number of minutes, but we're interested in
        // minutes past the hour: to get that, we need to
        // divide by 60 again and keep the remainder
        $minutes = intval(($sec / 60) % 60);

        // then add to $hms (with a leading 0 if needed)
        $hms .= str_pad($minutes, 2, "0", STR_PAD_LEFT). ':';

        // seconds are simple - just divide the total
        // seconds by 60 and keep the remainder
        $seconds = intval($sec % 60);

        // add to $hms, again with a leading 0 if needed
        $hms .= str_pad($seconds, 2, "0", STR_PAD_LEFT);

        return $hms;
    }

    public function formatNumber($number)
    {
        return numbermanager::formatNumber($number);
    }

}

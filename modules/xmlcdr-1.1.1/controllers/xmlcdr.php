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
                'caption' => 'Caller Detail Records'
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
                'width' => '250'
            )
        );
        $grid->add('destination_number', 'Destination', array(
                'width' => '250'
            )
        );

        $grid->add('start_stamp', 'Start', array(
                'width' => '250'
            )
        );
        $grid->add('duration', 'Duration', array(
                'width' => '250'
            )
        );



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

    public function details($xml_cdr_id)
    {


        $xmlcdr = Doctrine::getTable('Xmlcdr')->findOneBy('xml_cdr_id', $xml_cdr_id);

        $idx = array('caller_id_name');


        $details = "
            <table>
            <tr><td>Caller Name: </td><td>{$xmlcdr->caller_id_name}</td></tr>
            <tr><td>Caller Name: </td><td>{$xmlcdr->caller_id_number}</td></tr>
            <tr><td>Direction: </td><td>{$xmlcdr->direction}</td></tr>
            </table>
            ";



        $this->template->content = new View('xmlcdr/details');
        $this->template->content->details = $details;
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

}

<?php
defined('SYSPATH') or die('No direct access allowed.');
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
 * sofia.php - Sofia Viewer Module
 *
 * @author Michael Phillips <michael.j.phillips@gmail.com>
 * @license MPL
 * @package FreePBX3
 * @subpackage Sofia
 */
class Sofia_Controller extends FreePbx_Controller
{
    public function index()
    {
        $this->template->content = new View('generic/grid');
        // Buidl a grid with a hidden device_id, class_type, and add an option for the user to select the display columns
        $this->grid = jgrid::grid('SipRegistrations', array(
            'caption' => 'Sip Registrations'
        ))->add('call_id', 'Call ID', array(
            'hidden' => true,
            'key' => true
        ))->add('sip_user', 'User', array(
            'width' => '20',
            'search' => true
        ))->add('sip_host', 'Host', array(
            'width' => '20',
            'search' => true
        ))->add('contact', 'Contact', array(
            'width' => '50',
            'search' => true
        ))->add('status', 'Status', array(
            'width' => '20',
            'search' => true
        ))->add('user_agent', 'User Agent', array(
            'width' => '50',
            'search' => true
        ))->addAction('sofia/details/registration', 'Details', array(
            'arguments' => 'call_id',
            'width' => '20'
        ))->navButtonAdd('Columns', array(
            'onClickButton' => 'function () {  $(\'#{table_id}\').setColumns(); }',
            'buttonimg' => url::base() . 'assets/css/jqGrid/table_insert_column.png',
            'title' => 'Show/Hide Columns',
            'noCaption' => true,
            'position' => 'first'
        ));
        // dont foget to let the plugins add to the grid!
        plugins::views($this);
        // Produces the grid markup or JSON
        $this->view->grid = $this->grid->produce();
        $this->view->title = 'Sofia Registrations';
        //Doctrine::generateModelsFromDb('/tmp/models');
        
    }
    public function details($type = NULL, $id = NULL)
    {
        switch ($type) {
            case 'registration':
                $baseModel = Doctrine::getTable('SipRegistrations')->find($id);
                if ($baseModel) {
                    $this->view->modelContents = $this->displayModelContents($baseModel);
                    break;
                } 
                
            default:
                message::set('Unable to find the details for that ' .$type, array(
                    'type' => 'alert',
                    'redirect' => 'sofia'
                ));
        }        
    }
    private function displayModelContents($model)
    {
        $html = "<table class=\"fancy\" width=\"100%\">";
        foreach($model->toArray() as $key => $value) {
            if ($key == 'network_ip') {
                $html .= "<tr><td> " . __($key) . "</td><td>";
                $html .= '<a href="http://' .$value .'" target="_blank">' .htmlspecialchars($value) .'</a></td></tr>';
            } else {
                $html .= "<tr><td> " . __($key) . "</td><td>" . htmlspecialchars($value) . "</td></tr>\n";
            }
            
        }
        $html.= "</table>";
        return $html;
    }
}

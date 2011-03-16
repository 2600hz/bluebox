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
class Mongocdr_Controller extends Bluebox_Controller {
   protected $authBypass = array('service');

    public function  index() {


             $this->template->content = new View('mongocdr/index');

    }

    public function service($key = NULL) {
        $this->auto_render = FALSE;


        if($this->input->post()) {
            $mongo = new MongoCdr();
            
            $mongo->connect();

            $xml = $this->input->post('cdr');

            Kohana::log('info', $xml);

            $mongo->addXMLCDR($xml);
        } else {
            $error =  "NO CDR RECORD FOUND IN POST HEADER";
            echo $error;
            Kohana::log('error', $error);

        }
    }

}

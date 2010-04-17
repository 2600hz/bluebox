<?php defined('SYSPATH') or die('No direct access allowed.');
/*
 * FreePBX Modular Telephony Software Library / Application
 * Copyright (C) 2005-2009, Darren Schreiber <d@d-man.org>
 *
 * Version: FPL 1.0 (a modified version of MPL 1.1)
 *
 * The contents of this file are subject to the FreePBX Public License Version
 * 1.0 (the "License"); you may not use this file except in compliance with
 * the License. You may obtain a copy of the License at
 * http://www.freepbx.org/FPL/
 *
 * Software distributed under the License is distributed on an "AS IS" basis,
 * WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License
 * for the specific language governing rights and limitations under the
 * License.
 *
 * The Original Code is FreePBX Modular Telephony Software Library / Application
 *
 * The Initial Developer of the Original Code is
 * Darren Schreiber <d@d-man.org>
 * Portions created by the Initial Developer are Copyright (C)
 * the Initial Developer. All Rights Reserved.
 *
 * Contributor(s):
 *
 * Raymond Chandler <intralanman@gmail.com>
 *
 */

/**
 * lcr.php - Description of the plug-in
 *
 * @author Raymond Chandler <intralanman@gmail.com>
 * @license BSD
 * @package FreePBX
 * @subpackage LCR
 */

class Lcr_Plugin extends FreePbx_Plugin {

    /**
     * Catch changes to trunks and add/remove them for lcr
     */
    public function trunkmanager_save() {
        $carrierRecord = array();
        $postArray = $this->input->post();
        Kohana::log('debug', print_r($postArray, true));
        $carriers = Doctrine::getTable('Carriers');
        $carrierResult = $carriers->findOneBy('carrier_name', $postArray['trunk']['provider']);
        if ($carrierResult) {
            $carrierRecord = $carrierResult->toArray();
        }
        if(!array_key_exists('id', $carrierRecord)) {
            Kohana::log('debug', 'ID Does not exist in the array');
            $newCarrier = new Carriers();
            $newCarrier['carrier_name'] = $postArray['trunk']['provider'];
            $newCarrier->save();
        } else {
            Kohana::log('debug', 'ID Does exist in the array');
        }
    }
}

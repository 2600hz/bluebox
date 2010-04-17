<?php defined('SYSPATH') or die('No direct access allowed.');
/*
 * FreePBX Modular Telephony Software Library / Application
 *
 * The contents of this file are subject to the Mozilla Public License Version 1.1 (the 'License');
 * you may not use this file except in compliance with the License. You may obtain a copy of the License at
 * http://www.mozilla.org/MPL/.
 *
 * Software distributed under the License is distributed on an 'AS IS' basis, WITHOUT WARRANTY OF ANY KIND, either
 * express or implied. See the License for the specific language governing rights and limitations under the License.
 *
 * The Original Code is FreePBX Telephony Configuration API and GUI Framework.
 * The Original Developer is the Initial Developer.
 * The Initial Developer of the Original Code is Darren Schreiber
 * All portions of the code written by the Initial Developer and Bandwidth, Inc. are Copyright © 2008-2009. All Rights Reserved.
 *
 * Contributor(s):
 *
 *
 */

/**
 * device.php - FreeSWITCH Device driver
 *
 * Allows for a number to terminate at a device directly
 *
 * @author Darren Schreiber <d@d-man.org>
 * @license MPL
 * @package TCAPI
 * @subpackage FreeSWITCH_Driver
 */
class FreeSwitch_Device_Driver extends FreeSwitch_Base_Driver {
    public static function set($obj)
    {
        
    }

    public static function delete($obj)
    {
        
    }

    public static function dialplan($obj)
    {
        $xml = Telephony::getDriver()->xml;

        $domain = '$${location_' . $obj->Device->User->location_id . '}';

        Kohana::log('debug', $obj->Device->class_type);

        if ($obj->Device->class_type == 'SipDevice') {
            Kohana::log('debug', 'Got Here');
            Doctrine::initializeModels('SipDevice');
            $xml->update('/action[@application="bridge"]{@data="user\/' . $obj->Device->Sip->username . '@' . $domain . '"}');
        }
    }
}

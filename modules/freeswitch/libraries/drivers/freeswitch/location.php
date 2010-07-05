<?php defined('SYSPATH') or die('No direct access allowed.');
/*
 * Bluebox Modular Telephony Software Library / Application
 *
 * The contents of this file are subject to the Mozilla Public License Version 1.1 (the 'License');
 * you may not use this file except in compliance with the License. You may obtain a copy of the License at
 * http://www.mozilla.org/MPL/.
 *
 * Software distributed under the License is distributed on an 'AS IS' basis, WITHOUT WARRANTY OF ANY KIND, either
 * express or implied. See the License for the specific language governing rights and limitations under the License.
 *
 * The Original Code is Bluebox Telephony Configuration API and GUI Framework.
 * The Original Developer is the Initial Developer.
 * The Initial Developer of the Original Code is Darren Schreiber
 * All portions of the code written by the Initial Developer are Copyright © 2008-2009. All Rights Reserved.
 *
 * Contributor(s):
 *
 *
 */

/**
 * location.php - FreeSWITCH Locations driver
 *
 * Allows for locations to exist as global variables when compiling the FS XML and to be used in various places as $${location_ID}
 *
 * @author Darren Schreiber <d@d-man.org>
 * @license MPL
 * @package TCAPI
 * @subpackage FreeSWITCH_Driver
 */
class FreeSwitch_Location_Driver extends FreeSwitch_Base_Driver
{
    public static function set($base)
    {
        $xml = FreeSwitch::setSection('location');

        $xml->update('/X-PRE-PROCESS[@cmd="set"][@bluebox="location_' .$base['location_id'] . '"]{@data="location_' .$base['location_id'] .'=' .$base['location_id'] .'"}');
    }

    public static function delete($base)
    {
        $xml = FreeSwitch::setSection('location');

        $xml->deleteNode('/X-PRE-PROCSS[@cmd="set"][@bluebox="location_' .$base['location_id'] .'"]');
    }
}

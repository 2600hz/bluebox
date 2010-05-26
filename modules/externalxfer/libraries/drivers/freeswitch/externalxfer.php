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
 * externalxfer.php - FreeSwitch ExternalXfer driver
 * Adds ExternalXfer variables to FreeSWITCH XML configuration files.
 *
 * @author Darren Schreiber <d@d-man.org>
 * @license MPL
 * @package FreePBX3
 * @subpackage ExternalXfer
 */
class FreeSwitch_ExternalXfer_Driver extends FreeSwitch_Base_Driver {
    public static function dialplan()
    {
        Kohana::log('debug', 'Adding external routing info');
        
        // Reference to our XML document, positioned at the right extension for us :-)
        $xml = FreeSWITCH::createExtension('externalxfer');
        $condition = '/condition[@field="${internal_caller_id_number}"][@expression="^.+$"]';
        $xml->update($condition . '/action[@application="set"][@data="effective_caller_id_name=${internal_caller_id_name}"]');
        $xml->update($condition .'/action[@application="set"][@data="effective_caller_id_number=${internal_caller_id_number}"]');

    }
}

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
 * externalxfer.php - FreeSwitch ExternalXfer driver
 * Adds ExternalXfer variables to FreeSWITCH XML configuration files.
 *
 * @author Darren Schreiber <d@d-man.org>
 * @license MPL
 * @package Bluebox
 * @subpackage ExternalXfer
 */
class FreeSwitch_ExternalXfer_Driver extends FreeSwitch_Base_Driver
{
    public static function set($obj)
    {

    }

    public static function delete($obj)
    {
        
    }

    public static function dialplan($number)
    {
        $xml = Telephony::getDriver()->xml;

        $destination = $number['Destination'];

        if ($destination instanceof ExternalXfer)
        {
            $numberOptions = fs::getNumberOptions($number);

            switch($destination['route_type'])
            {
                case ExternalXfer::TYPE_SIP:

                    if (empty($destination['route_details']['interface']) OR empty($destination['route_details']['sipuri']))
                    {
                        
                    }

                    $dialstring = 'sofia/sipinterface_' .$destination['route_details']['interface'] .'/' .$destination['route_details']['sipuri'];

                    break;
                    
                case ExternalXfer::TYPE_TRUNK:
                    
                    if (empty($destination['route_details']['trunk']) OR empty($destination['route_details']['number']))
                    {

                    }

                    $dialstring = 'sofia/gateway/trunk_' .$destination['route_details']['trunk'] .'/' .$destination['route_details']['number'];
                    
                    break;
            }

            $dialstring = str_replace(array('/', '@'), array('\/', '\@'), $dialstring);

            if (empty($dialstring))
            {
                return;
            }

            $xml->update('/action[@application="set"][@bluebox="settingTimeout"]{@data="call_timeout=' .$numberOptions['timeout'] .'"}');

            $xml->update('/action[@application="set"][@bluebox="settingRing"]{@data="ringback=' . $numberOptions['ringtype'] . '"}');

            $xml->update('/action[@application="set"][@bluebox="settingRingXfer"]{@data="transfer_ringback=' . $numberOptions['ringtype'] . '"}');

            if (!empty($destination['plugins']['callerid']['internal_name']))
            {
                $xml->update('/action[@application="export"][@bluebox="sipCalleeIdName"]{@data="sip_callee_id_name=' .$destination['plugins']['callerid']['internal_name'] .'"}');
            }
            else
            {
                $xml->update('/action[@application="export"][@bluebox="sipCalleeIdName"]{@data="sip_callee_id_name=' .$destination['name'] .'"}');
            }

            $xml->update('/action[@application="export"][@bluebox="sipCalleeIdNumber"]{@data="sip_callee_id_number=' .$number['number'] .'"}');


            $options = array();

            if (arr::get($destination, 'registry', 'ignore_early_media'))
            {
                $options[] = 'ignore_early_media=true';

            }

            if (arr::get($destination, 'registry', 'require_confirmation'))
            {
                $options[] = 'group_confirm_file=ivr\/accept_reject_voicemail.wav,group_confirm_key=1,call_timeout=60';

            }

            if (count($options) > 0) {
                $options = '{' . implode(',', $options) . '}';
            } else {
                $options = '';
            }

            $xml->update('/action[@application="bridge"]{@data="' .$options.$dialstring .'"}');
        }
    }
}

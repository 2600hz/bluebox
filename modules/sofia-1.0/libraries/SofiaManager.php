<?php
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



class SofiaManager {
    /*
     * Check to see if a device is registered on the switch
    */
    public static function isDeviceActive($user, $domain)
    {
        $registraions = self::getRegistrationGlob();

        foreach($registraions as $registration) 
        {
            if (empty($registration['user']))
            {
                continue;
            }
            
            $device = explode('@', $registration['user']);

            $deviceUser = $device[0];

            $deviceDomain = $device[1];

            if($deviceUser == $user && $deviceDomain == $domain)
            {
                return '<b>Registered</b>';
            }
        }

        return 'Unknown';
    }

    /*
     * Get registrations for a switch
     * @param string $SIPInterface Name of SIP interface
    */
    public static function getRegistrations($SIPInterface)
    {
        $cache = Cache::instance();

        $sipRegCache = $cache->get('cache_' . $SIPInterface);

        if(!$sipRegCache)
        {
            Kohana::log('info', 'Using cached registration');

            $eslManager = new EslManager();
            
            $cmd = 'sofia xmlstatus profile ' . $SIPInterface;

            $result = $eslManager->api($cmd);

            $xml = $eslManager->getResponse($result);

            $xml = simplexml_load_string($xml);

            $registrations = (array)$xml->registrations;

            $result = array();

            foreach($registrations['registration'] as $r) // cast to array from stl
            {
                $r = (array)$r;

                $r['interface'] = $SIPInterface;

                $result[] = $r;
            }

            return $result;
        } 
        else
        {
            return $sipRegCache;
        }
    }

    public static function getSIPInterfaces()
    {
        $interfaces = Doctrine::getTable('SipInterface')->findAll();

        $interfaceArr = array();

        if($interfaces)
        {
            foreach($interfaces as $interface) 
            {
                // all sip interfaces start with sipinterface_
                $interfaceArr[$interface->sipinterface_id] = 'sipinterface_' . $interface->sipinterface_id; 
            }

            return $interfaceArr;

        } 
        else
        {
            throw new Exception('Failed to find SIP interfaces');
        }
    }

    /*
     * Scan for all interfaces and put all registrations into a datastructure we can use in Bluebox
    */

    public static function getRegistrationGlob()
    {
        $interfaces = self::getSIPInterfaces();

        $registrationGlobArr = array();

        foreach($interfaces as $interface)
        {
            $registrationGlobArr = array_merge($registrationGlobArr, self::getRegistrations($interface));
        }

        return $registrationGlobArr;
    }
}
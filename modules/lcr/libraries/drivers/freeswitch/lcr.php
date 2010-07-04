<?php defined('SYSPATH') or url::redirect('/');
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
 * All portions of the code written by the Initial Developer and Bandwidth, Inc. are Copyright © 2008-2009. All Rights Reserved.
 *
 * Contributor(s):
 * Raymond Chandler <intralanman@gmail.com>
 *
 */

/**
 * lcr.php - FreeSwitch lcr Number driver
 *
 * Allows for a number to have a lcr bridge as a final destination in the dialplan. Links it to the
 * lcr FreeSWITCH XML configuration files.
 *
 * @author Raymond Chandler <intralanman@gmail.com>
 * @license BSD
 * @package Bluebox
 * @subpackage LCR
 */
class FreeSwitch_Lcr_Driver extends FreeSwitch_Base_Driver {
/**
 * Indicate we support FreeSWITCH
 */
    public static function set($obj) {
        Kohana::log('debug', get_class($obj));
        Kohana::log('debug',
            sprintf('Called from %s in %s on line %d', __METHOD__, __FILE__, __LINE__)
        );

        // Reference to our XML document
        $xml = Telephony::getDriver()->xml;
        $xml->setXmlRoot('//document/section[@name="configuration"]/configuration[@name="lcr.conf"]/');
        $xml->update('/settings/param[@name="odbc-dsn"]{@value="freeswitch-odbc:root:root"}');
        
    }

    public static function dialplan($obj) {
        Kohana::log('debug', get_class($obj));
        Kohana::log('debug',
            sprintf('Called from %s in %s on line %d', __METHOD__, __FILE__, __LINE__)
        );

    }

    public static function delete($obj) {
        Kohana::log('debug', get_class($obj));
        Kohana::log('debug',
            sprintf('Called from %s in %s on line %d', __METHOD__, __FILE__, __LINE__)
        );
    }

    public static function get($obj) {
        $xml = Telephony::getDriver()->xml;
        $xml->setXmlRoot('//document/section[@name="configuration"]/configuration[@name="lcr.conf"]/');
        Kohana::log('debug', $xml->getAttributeValue('/param{@name="odbc-dsn"]'));
        
    }

}


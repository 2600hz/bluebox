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
 * odbc.php - FreeSwitch Odbc driver
 *
 * Odbc XML generation
 *
 * @author Darren Schreiber <d@d-man.org>
 * @license MPL
 * @package TCAPI
 * @subpackage Odbc
 */
class FreeSwitch_Odbc_Driver extends FreeSwitch_Base_Driver {
    public static function set($obj)
    {
        if (!$obj)
            return;
		
        $xml = Telephony::getDriver()->xml;
        //$xml->setXmlRoot($obj->xpath);
        //$xml->update('/param[@name="odbc-dsn"][@value="' . $obj->dsn_name . ':' . $obj->user . ':' . $obj->pass . '"]');
        FreeSwitch::setSection('odbc');
        
        $dsn = $obj->dsn_name . ':' . $obj->user . ':' .$obj->pass;
        $xml->update('/X-PRE-PROCESS[@cmd="set"][@bluebox="odbc_' . $obj->odbc_id . '"]{@data="odbc_' . $obj->odbc_id . '=' . $dsn . '"}');
    }

    public static function delete($obj)
    {
        if (!$obj)
            return;

		$xml = Telephony::getDriver()->xml;
        $xml->setXmlRoot('//document/section[@name="odbc"]/X-PRE-PROCESS[@cmd="set"][@bluebox="odbc_' . $obj->odbc_id . '"]');
        $xml->deleteNode();
    }
}

<?php defined('SYSPATH') or die('No direct access allowed.');
/*
 * Bluebox Modular Telephony Software Library / Application
 * Copyright (C) 2005-2009, Darren Schreiber <d@d-man.org>
 *
 * Version: FPL 1.0 (a modified version of MPL 1.1)
 *
 * The contents of this file are subject to the Bluebox Public License Version
 * 1.0 (the "License"); you may not use this file except in compliance with
 * the License. You may obtain a copy of the License at
 * http://www.bluebox.org/FPL/
 *
 * Software distributed under the License is distributed on an "AS IS" basis,
 * WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License
 * for the specific language governing rights and limitations under the
 * License.
 *
 * The Original Code is Bluebox Modular Telephony Software Library / Application
 *
 * The Initial Developer of the Original Code is
 * Darren Schreiber <d@d-man.org>
 * Portions created by the Initial Developer are Copyright (C)
 * the Initial Developer. All Rights Reserved.
 *
 * Contributor(s):
 *
 * Darren Schreiber <d@d-man.org>
 *
 */

/**
 * myplugin.php - FreeSWITCH XML configuration driver for MyPlugin
 *
 * @author Your Name <your@email.org>
 * @license Your License
 * @package _Skeleton
 */
class FreeSwitch_MyPlugin_Driver {
    /**
     * Indicate we support FreeSWITCH with this SIP Device and provide code to save SIP device specific settings
     */
    public static function set($obj)
    {
        // Reference to our XML document
        $xml = Telephony::getDriver()->xml;
        $xml->setXmlRoot('//document/section[@name="myplugin"]');

        // The section we are working with is <document><section name="myplugin"><feature name="XXX">
        $prefix = sprintf('/feature[@name="%s"]', $obj->mydatafield1);

        // Create the base user record (using the defined prefix).
        $xml->set($prefix);

        // These vars are made up by this library. They are used consistently throughout.
        $xml->update($prefix . '/variables/variable[@name="mydatafield2"]{@value="' . $obj->mydatafield2 . '"}');
    }
}

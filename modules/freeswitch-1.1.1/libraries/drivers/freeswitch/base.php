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
 * Base FreeSWITCH Module Driver. Extend this class when creating modules that spit out configuration information.
 *
 * @author dschreiber
 * @package TCAPI
 * @subpackage FreeSWITCH_Driver
 */
interface FreeSwitch_Base_Driver_Interface
{
    public static function set($obj);
        // Must be defined by implementor!

    public static function delete($obj);
        // Must be defined by implementor!

}

abstract class FreeSwitch_Base_Driver implements FreeSwitch_Base_Driver_Interface
{
    public static function dialplan($obj)
    {
        return FALSE;   // Not supported if not overridden
    }
}

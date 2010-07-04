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
 * All portions of the code written by the Initial Developer and Bandwidth, Inc. are Copyright © 2008-2009. All Rights Reserved.
 *
 * Contributor(s):
 *
 *
 */

/**
 * number.php - FreeSwitch Number driver
 * 
 * Creates the basic context and dialplan extensions for all phone numbers in the system, and calls the correct methods to add
 * their number-related tasks to the specific number being generated.
 *
 * @author Darren Schreiber <d@d-man.org>
 * @license MPL
 * @package TCAPI
 * @subpackage FreeSWITCH_Driver
 */
class FreeSwitch_Number_Driver extends FreeSwitch_Base_Driver {
    /**
     * When a number is saved, we need to update any contexts that the number lives in
     */
    public static function set($obj)
    {
        // Go create the number related stuff for each context
        if ($obj->NumberContext) foreach ($obj->NumberContext as $context) {
            FreeSwitch_NumberContext_Driver::set($context);
        }
    }

    public static function delete($obj)
    {
        if ($obj->NumberContext) foreach ($obj->NumberContext as $context) {
            FreeSwitch_NumberContext_Driver::delete($context);
        }
    }
}

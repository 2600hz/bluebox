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
 * TimezoneDevice.php - Timezone support for Devices
 * Created on Jul 26, 2009
 *
 * @author Darren Schreiber <d@d-man.org>
 * @license MPL
 * @package Bluebox
 * @subpackage Timezone
 */
class DeviceTimezone extends Timezone
{
    public function setUp() {
        $this->setAttribute(Doctrine::ATTR_EXPORT, Doctrine::EXPORT_ALL ^ Doctrine::EXPORT_CONSTRAINTS);
        
        parent::setUp();
    }
}

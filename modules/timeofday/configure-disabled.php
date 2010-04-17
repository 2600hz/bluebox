<?php defined('SYSPATH') or die('No direct access allowed.');
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
*
*
*/
/**
 * configure.php - Voicemail configure hook
 *
 * @author Michael Phillips <michael.j.phillips@gmail.com>
 * @license MPL
 * @package FreePBX3
 * @subpackage TimeOfDay
 */
class TimeOfDay_Configure extends FreePbx_Configure
{
    public static $version = 0.1;
    public static $packageName = 'TimeOfDay';
    public static $author = 'Michael Phillips';
    public static $vendor = 'FreePbx';
    public static $license = 'MPL';
    public static $summary = 'Route calls based on time of day (Afterhours)';
    public static $default = FALSE;
    public static $type = FreePbx_Installer::TYPE_PLUGIN;
    public static $required = array(
        'core' => 0.1
    );

    public static function _checkExp() {
        return array('warnings' => 'This module is experimental and not ready for production use!');
    }
}

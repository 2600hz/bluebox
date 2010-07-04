<?php defined('SYSPATH') or die('No direct access allowed.');
/*
* Bluebox Modular Telephony Software Library / Application
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
 * configure.php - Sofia Configure Hook
 *
 * @author Michael Phillips <michael.j.phillips@gmail.com>
 * @license MPL
 * @package Bluebox
 * @subpackage Sofia_Configured
 */
class Sofia_Configure extends Bluebox_Configure
{
    public static $version = 0.1;
    public static $packageName = 'sofia';
    public static $displayName = 'SIP Registration Viewer';
    public static $author = 'Michael Phillips';
    public static $vendor = 'Bluebox';
    public static $license = 'MPL';
    public static $summary = 'Allow end-users to view registrations and much more.';
    public static $default = true;
    public static $type = Bluebox_Installer::TYPE_MODULE;
    public static $required = array(
        'core' => 0.1,
        'freeswitch' => 0.1
    );
    public static $navIcon = 'assets/img/icons/mainSettingsX.png';
    public static $navBranch = '/Status/';
    public static $navURL = 'sofia/index';    
    public static $navSubmenu = array(
        'Sip Registrations' => '/sofia/index',
        'Details' => array(
            'url' => '/sofia/details',
            'disabled' => true
        )
    );
}

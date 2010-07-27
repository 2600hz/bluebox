<?php defined('SYSPATH') or die('No direct access allowed.');
/*
* Bluebox Modular Telephony Software Library / Application
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
* The Initial Developer of the Original Code is Darren Schreiber <d@d-man.org> .
*
* Portions created by the Initial Developer are Copyright (C)
* the Initial Developer. All Rights Reserved.
*
* Contributor(s):
*
*
*/
/**
 * configure.php - Global Media Management System
 *
 * @author Darren Schreiber <d@d-man.org>
 * @license MPL
 * @package Bluebox
 * @subpackage MediaManager
 */
class Moh_Configure extends Bluebox_Configure
{
    public static $version = 0.1;
    public static $packageName = 'mediastream';
    public static $displayName = 'Media Stream Manager';
    public static $author = 'Darren Schreiber';
    public static $vendor = 'blue.box';
    public static $license = 'MPL';
    public static $summary = 'Provides support for media streaming and music-on-hold within FreeSWITCH.';
    public static $default = TRUE;
    public static $type = Package_Manager::TYPE_PLUGIN;
    public static $required = array(
        'core' => 0.1,
        'freeswitch' => 0.1
    );

}

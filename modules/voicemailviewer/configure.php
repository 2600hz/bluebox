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
 * configure.php - Voicemail configure hook
 *
 * @author Michael Phillips <michael.j.phillips@gmail.com>
 * @license MPL
 * @package Bluebox
 * @subpackage Voicemail
 */
class VoicemailViewer_Configure extends Bluebox_Configure
{
    public static $version = 0.1;
    public static $packageName = 'voicemailviewer';
    public static $displayName = 'Voicemail Viewer';
    public static $author = 'Michael Phillips';
    public static $vendor = 'Bluebox';
    public static $license = 'MPL';
    public static $summary = 'Provides voicemail mailbox content viewing and management.';
    public static $default = FALSE;
    public static $type = Bluebox_Installer::TYPE_MODULE;
    public static $required = array(
        'core' => 0.1,
        'esl' => 0.1
    );
    public static $navIcon = 'assets/img/icons/mainVoicemailsX.png';
    public static $navLabel = 'My Voicemail';
    public static $navBranch = '/My Phone/';
    public static $navURL = 'voicemailviewer/index';
    public static $navSubmenu = array(
        'Messages' => '/voicemailviewer',
        'Blast' => '/voicemailviewer/blast'
    );
}

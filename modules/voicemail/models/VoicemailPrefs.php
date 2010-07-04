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
 * VoicemailPrefs - Interface with FS directly via a model for voicemail information
 *
 * In addition to storing our password in voicemail, we need to save it here for fs to access via odbc
 *
 * @author Michael Phillips
 * @package Bluebox
 * @subpackage Voicemail
 */
class VoicemailPrefs extends Bluebox_Record {
	public function setTableDefinition()
	{
		$this->hasColumn('username', 'string', 255);
		$this->hasColumn('domain', 'string', 255);
		$this->hasColumn('name_path', 'string', 255);
		$this->hasColumn('greeting_path', 'string', 255);
		$this->hasColumn('password', 'string', 255);
		
	}
	
	public function setUp()
	{
        $this->actAs('DeviceRelation');
        $this->actAs('TelephonyEnabled');
 	}

}

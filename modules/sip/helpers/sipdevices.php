<?php defined('SYSPATH') or die('No direct access allowed.');
/*
 * FreePBX Modular Telephony Software Library / Application
 *
 * The contents of this file are subject to the Mozilla Public License Version 1.1 (the 'License');
 * you may not use this file except in compliance with the License. You may obtain a copy of the License at
 * http://www.mozilla.org/MPL/.
 *
 * Software distributed under the License is distributed on an 'AS IS' basis, WITHOUT WARRANTY OF ANY KIND, either
 * express or implied. See the License for the specific language governing rights and limitations under the License.
 *
 * The Original Code is FreePBX Telephony Configuration API and GUI Framework.
 * The Original Developer is the Initial Developer.
 * The Initial Developer of the Original Code is Darren Schreiber
 * All portions of the code written by the Initial Developer and Bandwidth, Inc. are Copyright © 2008-2009. All Rights Reserved.
 *
 * Contributor(s):
 *
 *
 */

/**
 * sipdevices.php - sipdevices class
 * Created on Jul 30, 2009
 *
 * @author Darren Schreiber <d@d-man.org>
 * @license MPL
 * @package FreePBX3
 * @subpackage Core
 */
class sipdevices {
	/**
	 * Gets valid sip devices.
	 * This is useful for make a dropdown of sip device numbers.  Used in voicemail blasting
	 * @todo put some column names in once we realize how it will be interacted with
	 *
	 */

	public function getSipNumbers()
	{
	 $q = Doctrine_Query::create()->select('s.username, d.device_id, u.user_id, l.domain')
				->from('SipDevice d, d.Sip s, d.User u, u.Location l');
		$result = $q->execute(array(), Doctrine::HYDRATE_ARRAY);
		return $result;
	}

	public static function getSipEndpoints()
	{
		$numbers = array();
		$q = Doctrine_Query::create()->select('s.username, d.device_id, u.user_id, l.domain')
				->from('SipDevice d, d.Sip s, d.User u, u.Location l');
		$result = $q->execute(array(), Doctrine::HYDRATE_ARRAY);
				$numbers = array();

		foreach($result as $number)
		{
			$numbers[$number['device_id']]=  $number['Sip']['username'] . '@' . $number['User']['Location']['domain'];
		}
		return $numbers;
	}

	/**
	 * Resolve the domain part of the sip url based on the device_id
	 * @access public
	 * @static
	 * @param string username
	 * @return mixed bool|string
	 */

	public static function getDomain($device_id)
	{
		$sipDevice = Doctrine::getTable('SipDevice')->find($device_id);

		if($sipDevice)
		{
			return $sipDevice->User->Location->domain;
		} else {
			return false;
		}
	}

	/**
	 * Resolve the username part of the sip url based on the device_id
	 * @access public
	 * @static
	 * @param string username
	 * @return mixed bool|string
	 */

	public static function getUserName($device_id)
	{
		$sipDevice = Doctrine::getTable('SipDevice')->find($device_id);

		if($sipDevice)
		{
			return $sipDevice->Sip->username;
		} else {
			return false;
		}
	}

        public static function randomPwdLink($bindTo, $title = NULL, $attributes = array(), $javascript = TRUE) {
            if (empty($bindTo)) return FALSE;

            // standardize the $data as an array, strings default to the class_type
            if ( ! is_array($attributes) )
            {
                $attributes = array('id' => $attributes);
            }

            // add in all the defaults if they are not provided
            $attributes += array(
                'id' => 'random_' .$bindTo .'_link',
                'translate' => TRUE,
                'jgrowl' => TRUE
            );

            // ensure we have our distint class
            arr::update($attributes, 'class', ' random_pwd_link');

            // if there is no title then use the default
            if (empty($title)) {
                $title = 'Create Random Password';
            }

            // unless instructed otherwise translate this title
            if($attributes['translate']) {
                $title = __($title);
            }

            if ($javascript) {

                if ($attributes['jgrowl'])
                    jquery::addPlugin('growl');

                $script = '$("#' .$attributes['id'] .'").bind("click", function(e) {
                    e.preventDefault();
                    chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"
                    numbers = "1234567890";
                    pass = "";
                    i = Math.floor(Math.random() * 52);
                    pass += chars.charAt(i);
                    for(x=0;x<6;x++)
                    {
                        i = Math.floor(Math.random() * 62);
                        if (i <= 52) {
                            pass += chars.charAt(i);
                        } else {
                            pass += numbers.charAt(i-53);
                        }
                    }
                    i = Math.floor(Math.random() * 10);
                    pass += numbers.charAt(i);
                    $("#' .$bindTo .'").val(pass);';
                if ($attributes['jgrowl'])
                    $script .= '$.jGrowl("' .__('Assigned password') .' " + pass, { theme: "success", life: 5000 });
                            ';
                $script .= '});';

                javascript::codeBlock($script);
            }

            // dont inlcude the tranlaste in the html attributes
            unset($attributes['translate']);
            unset($attributes['jgrowl']);

            // Parsed URL
            return '<a href="#" ' .html::attributes($attributes) .'><span>' .$title .'</span></a>';
        }
}

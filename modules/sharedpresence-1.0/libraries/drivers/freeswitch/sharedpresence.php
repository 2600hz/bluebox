<?php defined('SYSPATH') or die('No direct access allowed.');

class FreeSwitch_SharedPresence_Driver extends FreeSwitch_Base_Driver
{
    public static function set($base)
    {
        if ($base instanceof SipInterface)
        {
            $xml = FreeSwitch::setSection('sofia', 'sipinterface_' . $base['sipinterface_id']);
            $xml->update('/settings/param[@name="manage-presence"]{@value="' . (isset($base['plugins']['sharedpresence']['mode']) ? $base['plugins']['sharedpresence']['mode'] : 'true') . '"}');
            $xml->update('/settings/param[@name="manage-shared-appearance"]{@value="' . (isset($base['plugins']['sharedpresence']['mode']) && $base['plugins']['sharedpresence']['mode'] != 'false' ? 'true' : 'false') . '"}');
            if (isset($base['plugins']['sharedpresence']['spd_id']))
            {
            	$dbobj = Doctrine::getTable('SharedPresenceDB')->findOneByspd_id($base['plugins']['sharedpresence']['spd_id']);
            	$xml->update('/settings/param[@name="dbname"]{@value="' . (is_object($dbobj) ? $dbobj->spd_name : "") . '"}');
	        } else {
            	$xml->update('/settings/param[@name="dbname"]{@value=""}');
	        }
            $xml->update('/settings/param[@name="send-presence-on-register"]{@value="' . (isset($base['plugins']['sharedpresence']['send_info']) ? $base['plugins']['sharedpresence']['send_info'] : 'false') . '"}');
        }
    }

    public static function delete($base)
    {
    }
}

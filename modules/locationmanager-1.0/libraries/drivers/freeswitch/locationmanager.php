<?php defined('SYSPATH') or die('No direct access allowed.');

class FreeSwitch_LocationManager_Driver extends FreeSwitch_Base_Driver
{
    public static function set($base)
    {
        if($base instanceof Location)
        {
            foreach($base['User'] as $user)
            {
                foreach($user['Device'] as $device)
                {
                    LocationManager::setAreacode($device);
                }
            }
        }
        else if($base instanceof Device)
        {
            LocationManager::setAreacode($base);
        }
    }

    public static function delete($base)
    {

    }
}

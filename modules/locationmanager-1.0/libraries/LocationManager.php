<?php

class LocationManager {
    public function setAreacode($base)
    {
        if($base instanceof Device)
        {
            $domain = '$${location_' . $base['User']['location_id'] .'}';

            $xml = Telephony::getDriver()->xml;

            FreeSwitch::setSection('user', $domain, $base['device_id']);

            if(isset($base['User']['Location']['registry']['areacode']))
            {
                $xml->update('/variables/variable[@name="areacode"]{@value="' . $base['User']['Location']['registry']['areacode'] .'"}');
            }
            else {
                $xml->update('/variables/variable[@name="areacode"]{@value=""}');
            }
        }
    }


    public function updateAreacode()
    {
        $base = Event::$data;

        if(isset($base))
        {
            if(isset($base['device']))
            {
                LocationManager::setAreacode($base['device']);
            }
        }
    }
}

?>

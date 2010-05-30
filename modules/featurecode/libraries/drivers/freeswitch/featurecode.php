<?php
class FreeSwitch_FeatureCode_Driver extends FreeSwitch_Base_Driver {
    public static function set($obj) {

    }

    public static function delete($obj) {

    }
    
    public static function dialplan($obj) {
        $xml = Telephony::getDriver()->xml;

        $xml->replaceWithXml($obj->FeatureCode->xml);
    }
}

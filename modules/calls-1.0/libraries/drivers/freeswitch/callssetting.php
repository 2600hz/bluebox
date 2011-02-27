<?php defined('SYSPATH') or die('No direct access allowed.');

class FreeSwitch_CallsSetting_Driver extends FreeSwitch_Base_Driver {

    public static function set($base)
    {
        $xml = Telephony::getDriver()->xml;

        FreeSwitch::setSection('cdr_csv');

        $xml->update('/settings/param[@name="default-template"]{@value="bluebox"}');
        $xml->update('/settings/param[@name="legs"]{@value="a"}');
        $xml->update('/settings/param[@name="rotate-on-hup"]{@value="true"}');
        $xml->replaceWithXml(Kohana::config('calls.defaulttemplate'),'/templates/template[@name="bluebox"]');
  
    }

    public static function delete($base)
    {
        $xml = Telephony::getDriver()->xml;

        FreeSwitch::setSection('cdr_csv');

        //not sure what to do here

//        $xml->deleteNode();
    }
}

<?php defined('SYSPATH') or die('No direct access allowed.');

class FreeSwitch_CallsTemplate_Driver extends FreeSwitch_Base_Driver {

    public static function set($base)
    {
        $xml = Telephony::getDriver()->xml;

        FreeSwitch::setSection('cdr_csv');

        $xml->update('/settings/template[@name="account_' . $base->account_id . '"]{@value="' . $template_config . '"}');
    }

    public static function delete($base)
    {
        $xml = Telephony::getDriver()->xml;

        FreeSwitch::setSection('cdr_csv');

        $xml->deleteNode('/settings/template[@name="account_' . $base->account_id . '"]');
    }
}

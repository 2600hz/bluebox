<?php defined('SYSPATH') or die('No direct access allowed.');

class FreeSwitch_FeatureCode_Driver extends FreeSwitch_Base_Driver {
    public static function set($obj) {
    }

    public static function delete($obj) {
    }

    public static function dialplan($number) {
        $xml = Telephony::getDriver()->xml;

        $destination = $number['Destination'];

        $xml->replaceWithXml($destination['registry']['main']);
    }

    public static function conditioning()
    {
        self::generateXml('conditioning');
    }

    protected static function generateXml($section) {
        $features = Doctrine::getTable('FeatureCode')->findAll(Doctrine::HYDRATE_ARRAY);
        if ($features) foreach ($features as $feature) if (isset($feature['registry'][$section])) {
            Kohana::log('debug', 'Generating section ' . $section . ' for feature code ' . $feature[feature_code_id]);
            $xml = FreeSWITCH::createExtension('feature_code_' . $feature['feature_code_id']);

            $xml->replaceWithXml($feature['registry'][$section]);
        }
    }
}

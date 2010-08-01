<?php defined('SYSPATH') or die('No direct access allowed.');

class FreeSwitch_FeatureCode_Driver extends FreeSwitch_Base_Driver
{
    public static function set($obj) 
    {

    }

    public static function delete($obj) 
    {

    }

    public static function dialplan($number) {
        $xml = Telephony::getDriver()->xml;

        $destination = $number['Destination'];

        //$xml->replaceWithXml($destination['registry']['main']);
    }

    public static function preNumber($number) {
        $xml = Telephony::getDriver()->xml;

        $destination = $number['Destination'];

       // $xml->replaceWithXml($destination['registry']['prenumber']);
    }

    public static function postNumber($number)
    {
        
    }

    public static function conditioning()
    {
        self::generateXml('conditioning');
    }

    public static function network()
    {
        self::generateXml('network');
    }

    public static function preroute()
    {
        self::generateXml('preroute');
    }

    public static function postroute()
    {
        self::generateXml('postroute');
    }

    public static function preanswer()
    {
        self::generateXml('preanswer');
    }

    public static function postanswer()
    {
        self::generateXml('postanswer');
    }

    public static function catchall()
    {
        self::generateXml('catchall');
    }

    public static function postexecute()
    {
        self::generateXml('postexecute');
    }

    protected static function generateXml($section)
    {
        $features = Doctrine::getTable('FeatureCode')->findAll(Doctrine::HYDRATE_ARRAY);

        if ($features)
        {
            foreach ($features as $feature)
            {
                if (isset($feature['registry'][$section]))
                {
                    Kohana::log('debug', 'Generating section ' . $section . ' for feature code ' . $feature['feature_code_id']);

                    $xml = FreeSWITCH::createExtension('feature_code_' . $feature['feature_code_id']);

                    //$xml->replaceWithXml($feature['registry'][$section]);
                }
            }
        }
    }
}
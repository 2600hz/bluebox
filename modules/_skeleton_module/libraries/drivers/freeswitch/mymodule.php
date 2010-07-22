<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * @author Your Name <your@email.org>
 * @license Your License
 * @package _Skeleton
 */
class FreeSwitch_MyModule_Driver
{
    /**
     * Indicate we support FreeSWITCH with this SIP Device and provide code to save SIP device specific settings
     */
    public static function set($myModuleData)
    {
        // Reference to our XML document
        $xml = Telephony::getDriver()->xml;

        $xml->setXmlRoot('//document/section[@name="mymodule"]');

        // The section we are working with is <document><section name="mymodule"><feature name="XXX">
        $prefix = sprintf('/feature[@name="%s"]', $myModuleData['mydatafield1']);

        // Create the base user record (using the defined prefix).
        $xml->set($prefix);

        // These vars are made up by this library. They are used consistently throughout.
        $xml->update($prefix . '/variables/variable[@name="mydatafield2"]{@value="' .$mymoduleData['mydatafield2'] . '"}');
    }

    public static function delete($myModuleData)
    {
        // Reference to our XML document
        $xml = Telephony::getDriver()->xml;

        $xml->setXmlRoot('//document/section[@name="mymodule"]');

        // Delete everything at this node
        $xml->deleteNode();
    }
}

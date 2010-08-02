<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * @package    Asterisk
 * @author     K Anderson <bitbashing@gmail.com>
 * @license    Mozilla Public License (MPL)
 */
class Asterisk_Device_Driver extends Asterisk_Base_Driver
{
    public static function set($number)
    {

    }

    public static function delete($number)
    {

    }

    public static function dialplan($number)
    {
        $doc = Telephony::getDriver()->doc;

        $destination = $number['Destination'];

        $numberOptions = astrsk::getNumberOptions($number);

        if ($destination instanceof Device)
        {
            if (!empty($destination['plugins']['sip']['username']))
            {
                $doc->add('Dial(SIP/' .$destination['plugins']['sip']['username'] .',' .$numberOptions['timeout'] .')');
            }
        }
    }
}
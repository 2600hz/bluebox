<?php defined('SYSPATH') or die('No direct access allowed.');

class SipLib
{
    public static function initializeDevice()
    {
        extract(Event::$data);

        $plugin = array('sip' => array(
            'username' => html::token($device['name']),
            'password' => self::generatePassword()
        ));

        $device['plugins'] = arr::merge($device['plugins'], $plugin);
    }

    public static function generatePassword($length = 8)
    {
        $consonants = 'bdghjmnpqrstvz';
        $consonants .= 'BDGHJLMNPQRSTVWXZ';
        $consonants .= '1234567890';

        $vowels = 'aeuy';
        $vowels .= "AEUY";

        $password = '';
        $alt = time() % 2;
        for ($i = 0; $i < $length; $i++) {
                if ($alt == 1) {
                        $password .= $consonants[(rand() % strlen($consonants))];
                        $alt = 0;
                } else {
                        $password .= $vowels[(rand() % strlen($vowels))];
                        $alt = 1;
                }
        }
        // at least one digit
        $password = substr_replace($password, rand(0, 9), rand(1, $length), 1);

        return $password;
    }
}
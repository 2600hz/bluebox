<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * conferencenumber.php - Asterisk Conference Number driver
 *
 * Allows for a number to have a conference bridge as a final destination in the dialplan. Links it to the
 * Asterisk extensions configuration files.
 *
 * @author K Anderson
 * @license MPL
 * @package Bluebox
 * @subpackage Conference
 */
class Asterisk_Conference_Driver extends Asterisk_Base_Driver {
    /**
     * Indicate we support Asterisk
     */
    public static function set($obj)
    {

    }

    public static function delete($obj) {

    }

    public static function dialplan($obj)
    {
        $doc = Telephony::getDriver()->doc;

        $doc->add('MeetMeCount(${CONTEXT},CONF_COUNT)');
        $doc->add('GotoIf($[${CONF_COUNT} = 0]?new:existing)');
        $doc->add('MeetMe(${CONTEXT},acDMxs)', 'new');
        $doc->add('Return');
        $doc->add('MeetMe(${CONTEXT},cDMxs)', 'existing');
    }
}

<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * number.php - Asterisk Number driver
 *
 * Creates the basic context and dialplan extensions for all phone numbers in the system, and calls the correct methods to add
 * their number-related tasks to the specific number being generated.
 *
 * @author K Anderson
 * @license LGPL
 * @package Asterisk
 * @subpackage Asterisk_Driver
 */
class Asterisk_NumberContext_Driver extends Asterisk_Base_Driver
{
    /**
     * Indicate we support Asterisk with this SIP Device and provide code to save SIP device specific settings
     */
    public static function set($obj)
    {
        kohana::log('debug', 'Create a context dialplan for id ' .$obj->context_id);

        $doc = Telephony::getDriver()->doc;

        // Create the context we're going to start routing at. This just does pre-call setup routines
        // In Asterisk land this ensures a [context_X] exists and has, at minimum, a NoOp() at the top
        // and a GoSub to our actual list of available numbers
        $doc->createRoutableContext($obj->context_id);

        // THE ABOVE TWO LINES SHOULD RESULT IN:
        // [context_1]
        // exten => _X,NoOp()        ; added by dialplanStart
        // exten => _X,n,network-stuff
        // exten => _X,n,conditioning-stuff
        // exten => _X,n,blah
        // exten => _X,n,Gosub(destinations_1) ; added by diaplanEnd
        // exten => _X,n,FinishUpCallHooks   ; diaplanEnd hooks
        // exten => _X,n,Hangup()
        //
        // [context_1_destinations]
        //
        // THAT'S IT. It will delete and recreate the context_1 section but not destinations_1. This LOGIC belongs elsewhere, NOT HERE.

        // Does this number go anywhere?
        if ($obj->Number->class_type) {
            // Prepare to update or create this context  <-- This really should go in a lumped-together function
            //Asterisk::createContext('context_' .$obj->Number->location_id .'_' .$obj->context_id, $obj->Number->number);

            // Add this numbers dialplan into the appropriate context
            //Asterisk::add('Goto(main_number_' . $obj->Number->number_id . ',${EXTEN},1)');

            // Create the exten => 3000,Goto(number_X)  or whatever in the [destinations] list so we can actually reach this guy via the current context
            // This also sets some internal variable that tracks our current number and context (for use by the next few items) and
            // also creates a dummy [number_X] section
            // NOTE: This also sets a pointer in memory for the preNumber, actual dialplan and postNumber routines to use
            // too add their "stuff" to this dialplan entry
            //Asterisk::createExtension('context_' . $obj->Number->location_id . '_' . $obj->context_id, $obj->Number->number_id, 'Goto(main_number_' . $obj->Number->number_id . ',${EXTEN},1)');
            $doc->createDestination($obj->context_id, $obj->Number->number_id, $obj->Number->number);

            // Add an extension-specific prenumber items
            // Note that unlike other dialplan adds, this one assumes you're already in the right spot in the number_X section
            dialplan::preNumber($obj->Number);

            // Add related final destination XML
            $destinationDriverName = Telephony::getDriverName() . '_' . substr($obj->Number->class_type, 0, strlen($obj->Number->class_type) - 6) . '_Driver';

            Kohana::log('debug', 'Looking for destination driver ' . $destinationDriverName);

            // Is there a driver?
            if (class_exists($destinationDriverName, TRUE)) {
                // Logging
                Kohana::log('debug', 'Adding information for destination ' . $obj->Number->number_id . ' from model "' . get_class($obj->Number) . '" to our telephony configuration...');

                // Drivers are always singletons, and are responsible for persistenting data for their own config generation via static vars
                // TODO: Change this for PHP 5.3, which doesn't require eval(). Don't change this until all the cool kids are on PHP 5.3*/
                $success = eval('return ' . $destinationDriverName . '::dialplan($obj->Number);');
            }

            // Add a failure route for this dialplan
            // Note that unlike other dialplan adds, this one assumes you're already in the right spot in the dialplan
            dialplan::postNumber($obj->Number);

            $doc->add('Return');

            return TRUE;
        } else {
            // Number doesn't go anywhere - delete it altogether.
            $doc->deleteDialplanExtension($obj->context_id, $obj->Number->number);
        }
        return FALSE;
        // Add any "global" hooks that come after the processing of any numbers (this is per context)
        // In Asterisk land, this ensures a Gosub(destinations_X) followed by any hangup/post-call event hook stuff
        //  NOTE: NO LONGER NEEDED - DONE in Asterisk::createRoutableContext
        //  dialplan::end('context_' . $obj->context_id);

    }

    public static function delete($obj)
    {
        // Create a dummy/empty extension. This effectively will delete the extension.
        $doc = Telephony::getDriver()->doc;

        $doc->deleteDialplanExtension($obj->context_id, $obj->Number->number);
    }
}

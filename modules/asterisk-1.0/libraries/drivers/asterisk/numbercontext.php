<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * @package    Asterisk
 * @author     K Anderson <bitbashing@gmail.com>
 * @license    Mozilla Public License (MPL)
 */
class Asterisk_NumberContext_Driver extends Asterisk_Base_Driver
{
    /**
     * Indicate we support Asterisk with this SIP Device and provide code to save SIP device specific settings
     */
    public static function set($base)
    {
        kohana::log('debug', 'Asterisk -> Create a context dialplan for id ' .$base['context_id']);

        $doc = Telephony::getDriver()->doc;

        // Create the context we're going to start routing at. This just does pre-call setup routines
        // In Asterisk land this ensures a [context_X] exists and has, at minimum, a NoOp() at the top
        // and a GoSub to our actual list of available numbers
        $doc->createRoutableContext($base['context_id']);

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
        if ($base['Number']['class_type'])
        {
            $dialplan = $base['Number']['dialplan'];
            
            // Create the exten => 3000,Goto(number_X)  or whatever in the [destinations] list so we can actually reach this guy via the current context
            // This also sets some internal variable that tracks our current number and context (for use by the next few items) and
            // also creates a dummy [number_X] section
            // NOTE: This also sets a pointer in memory for the preNumber, actual dialplan and postNumber routines to use
            // too add their "stuff" to this dialplan entry
            $doc->createDestination($base['context_id'], $base['Number']['number_id'], $base['Number']['number']);

            // Add an extension-specific prenumber items
            // Note that unlike other dialplan adds, this one assumes you're already in the right spot in the number_X section
            dialplan::preNumber($base['Number']);

            // Add related final destination XML
            $destinationDriverName = Telephony::getDriverName() .'_' .substr($base['Number']['class_type'], 0, strlen($base['Number']['class_type']) - 6) .'_Driver';

              Kohana::log('debug', 'Asterisk -> Looking for destination driver ' .$destinationDriverName);

            // Is there a driver?
            if (class_exists($destinationDriverName, TRUE)) 
            {
                // Logging
                Kohana::log('debug', 'Asterisk -> Adding information for destination ' .$base['Number']['number_id'] .' from model "' .get_class($base['Number']) .'" to our telephony configuration...');

                // Drivers are always singletons, and are responsible for persistenting data for their own config generation via static vars
                // TODO: Change this for PHP 5.3, which doesn't require eval(). Don't change this until all the cool kids are on PHP 5.3*/
                kohana::log('debug', 'Asterisk -> EVAL ' .$destinationDriverName .'::dialplan($base->Number);');
                
                // Drivers are always singletons, and are responsible for persistenting data for their own config generation via static vars
                // TODO: Change this for PHP 5.3, which doesn't require eval(). Don't change this until all the cool kids are on PHP 5.3*/
                $success = eval('return ' . $destinationDriverName . '::dialplan($base->Number);');
            }

            // Add a failure route for this dialplan
            // Note that unlike other dialplan adds, this one assumes you're already in the right spot in the dialplan
            dialplan::postNumber($base['Number']);

            if (!empty($dialplan['terminate']['action']))
            {
                switch($dialplan['terminate']['action'])
                {
                    case 'transfer':
                        if($transfer = astrsk::getTransferToNumber($dialplan['terminate']['transfer']))
                        {
                            $doc->add('Goto(' .$transfer .')');
                        }
                        else
                        {
                            $doc->add('Hangup');
                        }

                        break;

                    case 'continue':
                        $doc->add('Return');
                    
                        break;

                    case 'hangup':
                    default:
                        $doc->add('Hangup');

                        break;
                }
            }
            else
            {
                $doc->add('Hangup');
            }


            return TRUE;
        } 
        else
        {
            kohana::log('debug', 'Asterisk -> REMOVING NUMBER ID ' .$base['Number']['number_id'] .' FROM CONTEXT ' .$base['context_id']);

            // Number doesn't go anywhere - delete it altogether.
            $doc->deleteDialplanExtension($base['context_id'], $base['Number']['number']);
        }

        return FALSE;
    }

    public static function delete($base)
    {
        $identifier = $base->identifier();

        $context_id = $base['context_id'];

        if (!empty($identifier['context_id']))
        {
            $context_id = $identifier['context_id'];
        }

        $number_id = $base['number_id'];

        if (!empty($identifier['number_id']))
        {
            $number_id = $identifier['number_id'];
        }

        kohana::log('debug', 'Asterisk -> REMOVING NUMBER ID ' .$number_id .' FROM CONTEXT ' .$context_id);

        $doc = Telephony::getDriver()->doc;

        $doc->deleteDialplanExtension($context_id, $base['Number']['number']);
    }
}

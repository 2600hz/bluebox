<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * number.php - Asterisk Number driver
 *
 * Creates the basic context and dialplan extensions for all phone numbers in the system, and calls the correct methods to add
 * their number-related tasks to the specific number being generated.
 *
 * @author Karl Anderson
 * @license LGPL
 * @package Asterisk_Driver
 */
class Asterisk_Number_Driver extends Asterisk_Base_Driver {
    public static function set($obj)
    {
        // Create a context if one doesnt exist that will contain the dialplan for this number
        //Asterisk::createRoutableContext('context_' . $obj->context_id);

        // Does this number go anywhere?
        /*if ($obj->class_type) {
            // Add an extension-specific prenumber priorities
            //dialplan::preNumber($obj);
            // Add related final destination priorities
            $destinationDriverName = Telephony::getDriverName() . '_' . substr($obj->class_type, 0, strlen($obj->class_type) - 6) . '_Driver';
            Kohana::log('debug', 'Looking for destination driver ' . $destinationDriverName);
            // Is there a driver?
            if (class_exists($destinationDriverName, TRUE)) {
                // Logging
                Kohana::log('debug', 'Adding information for destination ' . $obj->number_id . ' from model "' . get_class($obj) . '" to our telephony configuration...');
                // Drivers are always singletons, and are responsible for persistenting data for their own config generation via static vars
                // TODO: Change this for PHP 5.3, which doesn't require eval(). Don't change this until all the cool kids are on PHP 5.3
                $success = eval('return ' . $destinationDriverName . '::dialplan($obj);');
            }
            // Add en extension-specific postnumber priorities
            //dialplan::postNumber($obj);
        }
        Asterisk::flushDialplan();*/
        
        $doc = Telephony::getDriver()->doc;

        // Go create the number related stuff for each context
        $numberInUse = FALSE;
        if ($obj->NumberContext) {
            foreach ($obj->NumberContext as $context) {
                $return = Asterisk_NumberContext_Driver::set($context);
                $numberInUse = $numberInUse | $return;
                //$doc->createDialplanExtension($context->context_id, $obj->number_id, $obj->number);
            }
        }

        if (!$numberInUse) {
            // Remove the destination itself, fully. Despite this being called create, an empty context gets deleted automagically at save time
            kohana::log('debug', 'Number id ' .$obj->number_id .' is no longer in use in any context, deleteing!');
            $doc->deleteContext('extensions.conf', 'number_' . $obj->number_id);
        }
    }

    public static function delete($obj)
    {
        $doc = Telephony::getDriver()->doc;

        // Go remove any references to this number
        if ($obj->NumberContext) foreach ($obj->NumberContext as $context) {
            Asterisk_NumberContext_Driver::delete($context);
            //$doc->deleteDialplanExtension($context->context_id, $obj->number);
        }

        // Remove the destination itself, fully. Despite this being called create, an empty context gets deleted automagically at save time
        $doc->deleteContext('extensions.conf', 'number_' . $obj->number_id);
    }
}

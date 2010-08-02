<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * @package    Asterisk
 * @author     K Anderson <bitbashing@gmail.com>
 * @license    Mozilla Public License (MPL)
 */
class Asterisk_Number_Driver extends Asterisk_Base_Driver
{
    public static function set($obj)
    {
        $numberInUse = FALSE;

        if ($obj->NumberContext)
        {
            foreach ($obj->NumberContext as $context)
            {
                // Add any "global" hooks that come before the processing of any numbers (this is per context)
                dialplan::start('context_' .$context['context_id']);

                $numberInUse = $numberInUse | Asterisk_NumberContext_Driver::set($context);

                // Add any "global" hooks that come after the processing of any numbers (this is per context)
                dialplan::end('context_' .$context['context_id']);
            }
        }

        if (!$numberInUse)
        {
            // Remove the destination itself, fully. Despite this being called create, an empty context gets deleted automagically at save time
            kohana::log('debug', 'Number id ' .$obj['number_id'] .' is no longer in use in any context, deleteing!');
            
            $doc = Telephony::getDriver()->doc;

            $doc->deleteContext('extensions.conf', 'number_' . $obj['number_id']);
        }
    }

    public static function delete($obj)
    {
        $doc = Telephony::getDriver()->doc;

        // Go remove any references to this number
        if ($obj->NumberContext)
        {
            foreach ($obj->NumberContext as $context)
            {
                Asterisk_NumberContext_Driver::delete($context);
                //$doc->deleteDialplanExtension($context->context_id, $obj->number);
            }
        }

        // Remove the destination itself, fully. Despite this being called create, an empty context gets deleted automagically at save time
        $doc->deleteContext('extensions.conf', 'number_' . $obj['number_id']);
    }
}
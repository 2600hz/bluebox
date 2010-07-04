<?php defined('SYSPATH') or die('No direct access allowed.');
/*
 * Bluebox Modular Telephony Software Library / Application
 *
 * The contents of this file are subject to the Mozilla Public License Version 1.1 (the 'License');
 * you may not use this file except in compliance with the License. You may obtain a copy of the License at
 * http://www.mozilla.org/MPL/.
 *
 * Software distributed under the License is distributed on an 'AS IS' basis, WITHOUT WARRANTY OF ANY KIND, either
 * express or implied. See the License for the specific language governing rights and limitations under the License.
 *
 * The Original Code is Bluebox Telephony Configuration API and GUI Framework.
 * The Original Developer is the Initial Developer.
 * The Initial Developer of the Original Code is Darren Schreiber
 * All portions of the code written by the Initial Developer and Bandwidth, Inc. are Copyright © 2008-2009. All Rights Reserved.
 *
 * Contributor(s):
 *
 *
 */

/**
 * number.php - FreeSwitch Number driver
 * 
 * Creates the basic context and dialplan extensions for all phone numbers in the system, and calls the correct methods to add
 * their number-related tasks to the specific number being generated.
 *
 * @author Darren Schreiber <d@d-man.org>
 * @license MPL
 * @package TCAPI
 * @subpackage FreeSWITCH_Driver
 */
class FreeSwitch_Number_Driver extends FreeSwitch_Base_Driver
{
    /**
     * When a number is saved, we need to update any contexts that the number lives in
     */
    public static function set($obj)
    {
        // Go create the number related stuff for each context it is assigned to
        if ($obj->NumberContext) foreach ($obj->NumberContext as $context)
        {

            // If an account is disabled wipe out the context and add a catch all
            // to play back a message
            if ($obj->Account->status == Account::STATUS_DISABLED)
            {
                kohana::log('debug', 'FreeSWITCH -> Account ' .$obj->Account->name .' is disabled, overriding context creation');

                $xml = Telephony::getDriver()->xml;

                // Position ourself at the root of the context and delete everything in it
                $xml->setXmlRoot(sprintf('//document/section[@name="dialplan"]/context[@name="context_' . $context->context_id . '"]'));

                $xml->deleteChildren();

                // Create a new main_disabled_account extension within this context
                $xml = FreeSwitch::createExtension('disabled_account', 'main', 'context_' . $context->context_id);

                // Write the dialplan to playback a message witin a catch all condition
                $condition = '/condition';

                $xml->update($condition .'/action[@application="set"][@data="tts_engine=cepstral"]');

                $xml->update($condition .'/action[@application="set"][@data="tts_voice=Allison-8kHz"]');

                $xml->update($condition .'/action[@application="answer"]');

                $xml->update($condition .'/action[@application="sleep"][@data="1000"]');

                $xml->update($condition .'/action[@application="speak"][@data="This account is currently inactive, please try your call again later."]');

                $xml->update($condition .'/action[@application="hangup"]');
            } 
            else
            {
                FreeSwitch_NumberContext_Driver::set($context);
            }
        }

        // If the number is an inbound number (NPA NXX) then add it to a global incoming context that carriers enter through
        if (preg_match('/^1?[2-9][0-8][0-9][2-9][0-9]{6}$/', $obj->number))
        {
            $xml = FreeSwitch::createExtension('number_' . $obj->number_id, 'main', 'global_incoming');

            if ($obj->class_type)
            {
                $xml->deleteChildren();

                $xml->update('/condition[@field="destination_number"]{@expression="^\+?1?(' . $obj->number . ')$"}');

                $xml->setXmlRoot($xml->getXmlRoot() . '/condition[@field="destination_number"][@expression="^\+?1?(' . $obj->number . ')$"]');

                $xml->update('/action[@bluebox="inbound-xfer"][@application="transfer"]{@data="$1 XML context_' .$obj->NumberContext[0]->context_id .'"}');
            } 
            else
            {
                $xml->deleteNode();
            }
        }
    }

    public static function delete($obj)
    {
        // Remove the number from all contexts that is belongs to
        if ($obj->NumberContext) foreach ($obj->NumberContext as $context)
        {
            FreeSwitch_NumberContext_Driver::delete($context);
        }

        // If the number was a NPA NXX then it also exists in the global incoming context so we must remove it
        if (preg_match('/^1?[2-9][0-8][0-9][2-9][0-9]{6}$/', $obj->number))
        {
            $xml = Telephony::getDriver()->xml;

            // Reference to our XML document & context
            $xml->setXmlRoot(sprintf('//document/section[@name="dialplan"]/context[@name="global_incoming"]/extension[@name="%s"]', 'main_number_' . $obj->number_id));

            $xml->deleteNode();
        }
    }
}

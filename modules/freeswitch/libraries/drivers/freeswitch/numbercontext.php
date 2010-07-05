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
 * All portions of the code written by the Initial Developer are Copyright © 2008-2009. All Rights Reserved.
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
class FreeSwitch_NumberContext_Driver extends FreeSwitch_Base_Driver {
    /**
     * Indicate we support FreeSWITCH with this SIP Device and provide code to save SIP device specific settings
     */
    public static function set($base)
    {
        $xml = Telephony::getDriver()->xml;

        // When an account is disabled we add this catch call extension but there isn't really a good place to remove it
        // so we will simply do it everytime to ensure it is gone
        $xml->setXmlRoot('//document/section[@name="dialplan"]/context[@name="context_' .$base['context_id'] . '"]/extension[@name="main_disabled_account"]');

        $xml->deleteNode();
        
        // Add any "global" hooks that come before the processing of any numbers (this is per context)
        dialplan::start('context_' .$base['context_id']);

        // Reference to our XML document, context and extension. Creates the extension & context if does not already exist
        $xml = FreeSwitch::createExtension('number_' .$base['Number']['number_id'], 'main', 'context_' .$base['context_id']);

        // Does this number go anywhere?
        if ($base['Number']['class_type'])
        {
            // Dialplans are a bit different - we don't want to keep anything that is currently in an extension, in the event it's totally changed
            $xml->deleteChildren();

            // Check what number they dialed
            $xml->update('/condition[@field="destination_number"]{@expression="^' .$base['Number']['number'] .'$"}');

            // Now that the extension and condition fields are created for this number, set our root to inside the condition
            $xml->setXmlRoot($xml->getXmlRoot() . '/condition[@field="destination_number"][@expression="^' .$base['Number']['number'] . '$"]');

            // Add an extension-specific prenumber items
            // Note that unlike other dialplan adds, this one assumes you're already in the right spot in the XML document for the add
            dialplan::preNumber($base['Number']);

            // Add related final destination XML
            $destinationDriverName = Telephony::getDriverName() .'_' .substr($base['Number']['class_type'], 0, strlen($base['Number']['class_type']) - 6) .'_Driver';

            Kohana::log('debug', 'FreeSWITCH -> Looking for destination driver ' .$destinationDriverName);

            // Is there a driver?
            if (class_exists($destinationDriverName, TRUE))
            {
                // Logging
                Kohana::log('debug', 'FreeSWITCH -> Adding information for destination ' .$base['Number']['number_id'] .' from model "' .get_class($base['Number']) .'" to our telephony configuration...');

                // Drivers are always singletons, and are responsible for persistenting data for their own config generation via static vars
                // TODO: Change this for PHP 5.3, which doesn't require eval(). Don't change this until all the cool kids are on PHP 5.3*/
                kohana::log('debug', 'FreeSWITCH -> EVAL ' .$destinationDriverName .'::dialplan($base->Number);');

                $success = eval('return ' .$destinationDriverName .'::dialplan($base->Number);');
            }

            // Add an anti-action / failure route for this dialplan
            // Note that unlike other dialplan adds, this one assumes you're already in the right spot in the XML document for the add
            dialplan::postNumber($base['Number']);
        } 
        else
        {
            $xml->deleteNode();
        }

        // Add any "global" hooks that come after the processing of any numbers (this is per context)
        dialplan::end('context_' .$base['context_id']);
    }

    public static function delete($base)
    {
        $xml = Telephony::getDriver()->xml;

        // Reference to our XML document & context
        $xml->setXmlRoot(sprintf('//document/section[@name="dialplan"]/context[@name="context_' .$base['context_id'] . '"]/extension[@name="%s"]', 'main_number_' .$base['Number']['number_id']));

        $xml->deleteNode();
    }
}

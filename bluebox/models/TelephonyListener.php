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
 * TelephonyListener.php - Listens for Doctrine events to feed to the Telephony configuration library
 *
 * @author Darren Schreiber <d@d-man.org>
 * @license MPL
 * @package Bluebox
 * @subpackage Core
 */

 /*
  * TODO:
  * This code is not my best work, but apparently it is a necessary evil at this time. It provides a way to determine what records are pending save/delete.
  * This should ultimately be replaced with a modification of Doctrine that allows for us to access the pendingSaves and
  * pendingDeletes from a unit of work. That information is currently private and inaccessible to external entities.
  *
  * The Doctrine event system is not sufficient for avoiding this issue, as events are called prematurely, before newly inserted records
  * have their auto-increment ids populated (i.e. we won't have access to the primary keys when generating XML).
  *
  * We either need a change in Doctrine's source to fix this, or we need to link to it and change it ourselves (not preferred)
  * In the meantime, I will keep my own list of pending saves/deletes. It's more expensive but I'm using references, so not too bad.
  * This needs to be revised in the future, but would be good enough for 1.0
  */

class TelephonyListener extends Doctrine_EventListener {
    //public static $relatedModels = array();

    public static $queuedSets = array();
    public static $queuedDeletes = array();

    public static $changedModels = array();

    public function preTransactionBegin(Doctrine_Event $event)
    {
        // We get here when a DB transaction is about to occur. Assume a telephony config file might need to be updated and
        // prepare any related telephony drivers. If no driver is specified, we don't do anything here.
        // THIS IS WHERE WE INSTANTIATE THE SWITCH-SPECIFIC DRIVER!
        if (Kohana::config('telephony.driver') && Kohana::config('telephony.diskoutput')) {

            // Only work on actions that give us a base model
            $base = Bluebox_Record::getBaseTransactionObject();

            if ($base) {
                Kohana::log('debug', 'Instantiated our telephony driver before updating the model data.');
                Telephony::setDriver(Kohana::config('telephony.driver'));
            }
        }
    }

    public function postTransactionCommit(Doctrine_Event $event)
    {
        // A transaction just ended - we write out any configuration information set by the telephony driver now.
        // THIS IS WHERE WE UPDATE VIA THE SWITCH-SPECIFIC DRIVER!
        if (Kohana::config('telephony.driver') && Kohana::config('telephony.diskoutput')) {

            if (!empty(self::$changedModels)) {
                Kohana::log('debug', 'Creating config from saved models in memory.');

                // Figure out what models were touched and either set or delete based on the action that was done to them
                // NOTE: Make sure this occurs in the same order it occurred via Doctrine's transaction
                foreach (self::$changedModels as $change) {

                    if (!empty($change['baseModel'])) {
                        Bluebox_Record::setBaseSaveObject($change['baseModel']);
                    } else {
                        Kohana::log('alert', 'The record ' . get_class($change['record']) . ' did not have the baseModel set!');
                        continue;
                    }
                    
                    switch ($change['action']) {
                        case 'update':
                        case 'insert':
                            Telephony::set($change['record']);
                            break;

                            // As a safety net, re-generate all items with a direct relation to the base object
                            $base = Bluebox_Record::getBaseTransactionObject();
                            foreach ($base->getReferences() as $reference) {
                                Telephony::set($reference);
                            }

                            Bluebox_Record::setBaseSaveObject(NULL);
                            
                        case 'delete':
                            Telephony::delete($change['record']);
                            break;

                        default:
                            Kohana::log('debug', 'An unknown action (' . $change['action'] . ') was performed on model ' . get_class($change['record']));
                            break;
                    }

                }

                Telephony::save();

                // If configured, tell the telephony engine to reload it's configs immediately
                Telephony::commit();

                // Clear the telephony info in memory. This is important, because if someone is doing a bulk add or otherwise, things get crazy
                Telephony::reset();

                self::$changedModels = array();
            }
        }
    }
}

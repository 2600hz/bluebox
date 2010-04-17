<?php defined('SYSPATH') or die('No direct access allowed.');
/*
 * FreePBX Modular Telephony Software Library / Application
 *
 * The contents of this file are subject to the Mozilla Public License Version 1.1 (the 'License');
 * you may not use this file except in compliance with the License. You may obtain a copy of the License at
 * http://www.mozilla.org/MPL/.
 *
 * Software distributed under the License is distributed on an 'AS IS' basis, WITHOUT WARRANTY OF ANY KIND, either
 * express or implied. See the License for the specific language governing rights and limitations under the License.
 *
 * The Original Code is FreePBX Telephony Configuration API and GUI Framework.
 * The Original Developer is the Initial Developer.
 * The Initial Developer of the Original Code is Darren Schreiber
 * All portions of the code written by the Initial Developer and Bandwidth, Inc. are Copyright © 2008-2009. All Rights Reserved.
 *
 * Contributor(s):
 *
 *
 */

/**
 * TelephonyRecordListener.php - Telephony individual record listener. Catches pre-save events and loads the relevant switch
 * configs from disk (if the driver supports it), and on post-save it updates those configs in memory and saves them back to disk
 * (if the driver supports it)
 *
 * @author Darren Schreiber <d@d-man.org>
 * @license MPL
 * @package FreePBX3
 * @subpackage Core
 */

class TelephonyRecordListener extends Doctrine_Record_Listener
{
    /**
     * Upon a successful record being saved, update the relevant configuration in memory (for preparation to write to disk at the end of
     * a successful transaction)
     * @param Doctrine_Event $event
     */
    /*public function postSave(Doctrine_Event $event)
    {
        $invoker =& $event->getInvoker();
        if ((get_parent_class($invoker) == 'FreePbx_Record') or (get_parent_class($invoker) == 'Doctrine_Record')) {
            $modelName = get_class($invoker);
        } else {
            $modelName = get_parent_class($invoker);
        }
        Kohana::log('debug', 'Queuing saving of model data stored in ' . $modelName . ' for generation in memory after disk commit');
        TelephonyListener::$queuedSets[$modelName][] =& $invoker; // We do this because we can't set configs until we have saved all related models that may have an auto increment!
    }*/

    /**
     * Upon a successful record being updated, update the relevant configuration in memory (for preparation to write to disk at the end of
     * a successful transaction)
     * @param Doctrine_Event $event
     */
    public function postUpdate(Doctrine_Event $event)
    {
        $invoker =& $event->getInvoker();
        if ((get_parent_class($invoker) == 'FreePbx_Record') or (get_parent_class($invoker) == 'Doctrine_Record')) {
            $modelName = get_class($invoker);
        } else {
            $modelName = get_parent_class($invoker);
        }
        Kohana::log('debug', 'Queuing update of model data stored in ' . $modelName);
        // We do this because we can't set configs until we have saved all related models that may have an auto increment!
        TelephonyListener::$changedModels[] = array('action' => 'update', 'record' => &$invoker, 'baseModel' => FreePbx_Record::getBaseTransactionObject());
    }

    /**
     * Upon a successful record being inserted, update the relevant configuration in memory (for preparation to write to disk at the end of
     * a successful transaction)
     * @param Doctrine_Event $event
     */
    public function postInsert(Doctrine_Event $event)
    {
        $invoker =& $event->getInvoker();
        if ((get_parent_class($invoker) == 'FreePbx_Record') or (get_parent_class($invoker) == 'Doctrine_Record')) {
            $modelName = get_class($invoker);
        } else {
            $modelName = get_parent_class($invoker);
        }

        Kohana::log('debug', 'Queuing insert of model data stored in ' . $modelName);
        // We do this because we can't set configs until we have saved all related models that may have an auto increment!
        TelephonyListener::$changedModels[] = array('action' => 'insert', 'record' => &$invoker, 'baseModel' => FreePbx_Record::getBaseTransactionObject());
    }

    /**
     * Prior to a record being deleted from the DB we delete it's config on disk. Note that the item's information is still in memory
     * while the delete transaction and records are running.
     * @param Doctrine_Event $event
     */
    public function postDelete(Doctrine_Event $event)
    {
        //Kohana::log('debug', 'Deleting FS config in memory for model data related to ' . get_class($event->getInvoker()));
        $invoker =& $event->getInvoker();
        if ((get_parent_class($invoker) == 'FreePbx_Record') or (get_parent_class($invoker) == 'Doctrine_Record')) {
            $modelName = get_class($invoker);
        } else {
            $modelName = get_parent_class($invoker);
        }
        Kohana::log('debug', 'Queuing delete of model data from ' . $modelName);
        // We do this because we can't set configs until we have saved all related models that may have an auto increment!
        TelephonyListener::$changedModels[] = array('action' => 'delete', 'record' => &$invoker, 'baseModel' => FreePbx_Record::getBaseTransactionObject());
    }

}

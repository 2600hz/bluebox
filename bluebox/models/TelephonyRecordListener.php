<?php defined('SYSPATH') or die('No direct access allowed.');

class TelephonyRecordListener extends Doctrine_Record_Listener
{
    /**
     * Upon a successful record being updated, update the relevant configuration in memory (for preparation to write to disk at the end of
     * a successful transaction)
     * @param Doctrine_Event $event
     */
    public function postUpdate(Doctrine_Event $event)
    {
        $invoker =& $event->getInvoker();

        $baseModel = Bluebox_Record::getBaseTransactionObject();

        $identifier = $invoker->identifier();

        // We do this because we can't set configs until we have saved all related models that may have an auto increment!
        TelephonyListener::$changedModels[$invoker->getOid()] = array('action' => 'update', 'record' => &$invoker, 'baseModel' => $baseModel, 'identifier' => $identifier);

        $checkFor = get_class($invoker) .'Number';

        if (!class_exists($checkFor))
        {
            return TRUE;
        }

        if (isset($invoker['Number'][0]))
        {
            foreach($invoker['Number'] as &$number)
            {
                if (! ($number instanceof Bluebox_Record))
                {
                    continue;
                }

                $identifier = $number->identifier();

                $OID = $number->getOid();

                if (empty(TelephonyListener::$changedModels[$OID]))
                {
                    kohana::log('debug', 'Marking associated Number ' .implode(', ', $identifier) .' (' .$number['number'] .')  as dirty on updated model ' .get_class($invoker));
                }

                // We do this because we can't set configs until we have saved all related models that may have an auto increment!
                TelephonyListener::$changedModels[$OID] = array('action' => 'update', 'record' => &$number, 'baseModel' => $baseModel, 'identifier' => $identifier);
            }
        }
        else if ($invoker['Number'] instanceof Bluebox_Record)
        {
            $identifier = $invoker['Number']->identifier();

            $OID = $invoker['Number']->getOid();

            if (empty(TelephonyListener::$changedModels[$OID]))
            {
                kohana::log('debug', 'Marking associated Number ' .implode(', ', $identifier) .' (' .$invoker['Number']['number'] .') as dirty on updated model ' .get_class($invoker));
            }

            // We do this because we can't set configs until we have saved all related models that may have an auto increment!
            TelephonyListener::$changedModels[$OID] = array('action' => 'update', 'record' => &$invoker['Number'], 'baseModel' => $baseModel, 'identifier' => $identifier);
        }
    }

    /**
     * Upon a successful record being inserted, update the relevant configuration in memory (for preparation to write to disk at the end of
     * a successful transaction)
     * @param Doctrine_Event $event
     */
    public function postInsert(Doctrine_Event $event)
    {
        $invoker =& $event->getInvoker();

        $baseModel = Bluebox_Record::getBaseTransactionObject();

        $identifier = $invoker->identifier();

        // We do this because we can't set configs until we have saved all related models that may have an auto increment!
        TelephonyListener::$changedModels[$invoker->getOid()] = array('action' => 'insert', 'record' => &$invoker, 'baseModel' => $baseModel, 'identifier' => $identifier);
    }

    /**
     * Prior to a record being deleted from the DB we delete it's config on disk. Note that the item's information is still in memory
     * while the delete transaction and records are running.
     * @param Doctrine_Event $event
     */
    public function postDelete(Doctrine_Event $event)
    {
        $invoker =& $event->getInvoker();

        $baseModel = Bluebox_Record::getBaseTransactionObject();

        $identifier = $invoker->identifier();

        // We do this because we can't set configs until we have saved all related models that may have an auto increment!
        TelephonyListener::$changedModels[$invoker->getOid()] = array('action' => 'delete', 'record' => &$invoker, 'baseModel' => $baseModel, 'identifier' => $identifier);
    }
}
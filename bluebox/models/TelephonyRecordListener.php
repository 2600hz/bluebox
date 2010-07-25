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

        Kohana::log('debug', 'Telephony -> Queuing update of ' .get_class($invoker) .' ' .implode(', ', $identifier) .' with OID ' .$invoker->getOid() .' on base model ' .get_class($baseModel));

        // We do this because we can't set configs until we have saved all related models that may have an auto increment!
        TelephonyListener::$changedModels[$invoker->getOid()] = array('action' => 'update', 'record' => &$invoker, 'baseModel' => $baseModel, 'identifier' => $identifier);

        $checkFor = get_class($invoker) .'Number';

        if (!class_exists($checkFor))
        {
            kohana::log('debug', 'Base model ' .$baseModel .' has no need to dirty child numbers');

            return TRUE;
        }

        foreach ($invoker['Number'] as &$number)
        {
            $identifier = $number->identifier();

            Kohana::log('debug', 'Telephony -> Queuing update of ' .get_class($number) .' ' .implode(', ', $identifier) .' with OID ' .$number->getOid() .' on base model ' .get_class($baseModel));

            // We do this because we can't set configs until we have saved all related models that may have an auto increment!
            TelephonyListener::$changedModels[$number->getOid()] = array('action' => 'update', 'record' => &$number, 'baseModel' => $baseModel, 'identifier' => $identifier);
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

        Kohana::log('debug', 'Telephony -> Queuing insert of ' .get_class($invoker) .' ' .implode(', ', $identifier) .' with OID ' .$invoker->getOid() .' on base model ' .get_class($baseModel));

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

        Kohana::log('debug', 'Telephony -> Queuing delete of ' .get_class($invoker) .' ' .implode(', ', $identifier) .' with OID ' .$invoker->getOid() .' on base model ' .get_class($baseModel));

        // We do this because we can't set configs until we have saved all related models that may have an auto increment!
        TelephonyListener::$changedModels[$invoker->getOid()] = array('action' => 'delete', 'record' => &$invoker, 'baseModel' => $baseModel, 'identifier' => $identifier);
    }
}
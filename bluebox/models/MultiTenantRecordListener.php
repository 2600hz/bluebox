<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * MultiTenantListener.php - Listen for save/update/insert/delete events and tack on account_ids
 *
 * Classes that use the MultiTenant behavior have this record listener attached to them automatically.
 * You do not need to manually do anything with this class.
 *
 * This class does the heavy lifting for figuring out how to add account_id checks to Doctrine
 * record changes AND Doctrine DQL queries.
 *
 * @author Darren Schreiber <d@d-man.org>
 * @license Closed/Copyright Darren Schreiber
 */

class MultiTenantRecordListener extends Doctrine_Record_Listener
{
    /** Handle object references **/
    public function preSave(Doctrine_Event $event)
    {
        $record = &$event->getInvoker();

        if (!isset($record['account_id'])) {
            Kohana::log('debug', 'Throwing exception due to unset account_id - preSave');
            throw new Exception('Unable to assign this record to your account', 1);
        }

        if (!empty(users::$user['account_id'])) {
            $record['account_id'] = users::$user['account_id'];
        } else {
            Kohana::log('debug', 'Throwing exception due to empty user account_id - preSave');
            throw new Exception('Unable to determine your authorization to add this record', 1);
        }
    }

    public function preUpdate(Doctrine_Event $event)
    {
        $record = &$event->getInvoker();

        if (!isset($record['account_id'])) {
            Kohana::log('debug', 'Throwing exception due to unset account_id - preUpdate');
            throw new Exception('Unable to assign this record to your account', 1);
        }

        if (empty(users::$user['account_id'])) {
            Kohana::log('debug', 'Throwing exception due to empty user account_id - preUpdate');
            throw new Exception('Unable to determine your authorization to update this record', 1);
        } else if ($record['account_id'] != users::$user['account_id']) {
            Kohana::log('debug', 'Throwing exception account_id mismatch - preUpdate');
            throw new Exception('You do not have authorization to update this record', 1);
        }
    }

    public function preInsert(Doctrine_Event $event)
    {
        $record = &$event->getInvoker();

        if (!isset($record['account_id'])) {
            Kohana::log('debug', 'Throwing exception due to unset account_id - preInsert');
            throw new Exception('Unable to assign this record to your account', 1);
        }

        if (!empty(users::$user['account_id'])) {
            $record['account_id'] = users::$user['account_id'];
        } else {
            Kohana::log('debug', 'Throwing exception due to empty user account_id - preInsert');
            throw new Exception('Unable to determine your authorization to add this record', 1);
        }
    }

    public function preDelete(Doctrine_Event $event)
    {
        $record = &$event->getInvoker();

        // This is dangerous - should we be more strict?
        if (empty($record['account_id'])) {
            Kohana::log('debug', 'Throwing exception due to unset account_id - preInsert');
            throw new Exception('Unable to determine the account owner of this record', 1);
        }

        if (empty(users::$user['account_id'])) {
            Kohana::log('debug', 'Throwing exception due to empty user account_id - preDelete');
            throw new Exception('Unable to determine your authorization to delete this record', 1);
        }

        if ($record['account_id'] != users::$user['account_id']) {
            Kohana::log('debug', 'Throwing exception account_id mismatch - preDelete');
            throw new Exception('You do not have authorization to delete this record', 1);
        }
    }

    /** Handle DQL **/
    public function preDqlUpdate($event)
    {
        $q = $event->getQuery();
        $q->andWhere('account_id = ' .users::$user['account_id']);
    }

    public function preDqlSelect($event)
    {
        // HACK! TODO: Fix this
        // For now, if there's no authenticated user, don't filter on any user for selects
        if ((!users::$user) or (!users::$user['account_id'])) {
            return;
        }

        $q = &$event->getQuery();
        $q->andWhere('account_id = ' .users::$user['account_id']);
    }

    public function preDqlDelete($event)
    {
        $q = &$event->getQuery();
        $q->andWhere('account_id = ' .users::$user['account_id']);
    }

    public function preValidate($event)
    {

    }
}

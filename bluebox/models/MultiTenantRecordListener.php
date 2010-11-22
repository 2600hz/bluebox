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
    private static $account_id = 0;

    public function getUserId()
    {
        $prev_account_id = self::$account_id;

	// The user may change for example force_login
	if (!empty(users::$user['account_id']))
	{
	    self::$account_id = users::$user['account_id'];
	}

	$session = Session::instance();

	if (($multitenant_account_id = $session->get('multitenant_account_id', FALSE)))
	{
	    self::$account_id = $multitenant_account_id;
	}

	if (!self::$account_id)
	{
	    Kohana::log('debug', 'Throwing exception due to empty user account_id');

	    //throw new Exception('Unable to determine your authorization to manipulate this record');
	}

	if (self::$account_id != $prev_account_id)
	{
	    kohana::log('debug', 'MultiTenantRecordListener is using account id ' .self::$account_id);
	}

        return self::$account_id;
    }

    public function preSave(Doctrine_Event $event)
    {
        $record = &$event->getInvoker();

        if (!isset($record['account_id']))
        {
            Kohana::log('debug', 'Throwing exception due to unset account_id');

            throw new Exception('Unable to assign this record to your account');
        }

        $record['account_id'] = $this->getUserId();
    }

    public function preUpdate(Doctrine_Event $event)
    {   
        $record = &$event->getInvoker();

        if (!isset($record['account_id']))
        {
            Kohana::log('debug', 'Throwing exception due to unset account_id');

            throw new Exception('Unable to assign this record to your account');
        }

        if ($record['account_id'] != $this->getUserId())
        {
            Kohana::log('debug', 'Throwing exception account_id mismatch, ' .$record['account_id'] .' != ' .$this->getUserId());

            throw new Exception('You do not have authorization to update this record', 1);
        }
    }

    public function preInsert(Doctrine_Event $event)
    {
        $record = &$event->getInvoker();

        if (!isset($record['account_id']))
        {
            Kohana::log('debug', 'Throwing exception due to unset account_id');

            throw new Exception('Unable to assign this record to your account');
        }

        $record['account_id'] = $this->getUserId();
    }

    public function preDelete(Doctrine_Event $event)
    {
        $record = &$event->getInvoker();

        // This is dangerous - should we be more strict?
        if (empty($record['account_id']))
        {
            Kohana::log('debug', 'Throwing exception due to unset account_id');

            throw new Exception('Unable to determine the account owner of this record');
        }

        if ($record['account_id'] != $this->getUserId())
        {
            Kohana::log('debug', 'Throwing exception account_id mismatch, ' .$record['account_id'] .' != ' .$this->getUserId());

            throw new Exception('You do not have authorization to update this record', 1);
        }
    }

    public function preDqlUpdate($event)
    {   
        $q = $event->getQuery();

        $q->andWhere('account_id = ' .$this->getUserId());
    }

    public function preDqlSelect($event)
    {
        $query = $event->getQuery();

        $q = &$event->getQuery();

        $q->andWhere('account_id = ' .$this->getUserId());
    }

    public function preDqlDelete($event)
    {
        $q = &$event->getQuery();

        $q->andWhere('account_id = ' .$this->getUserId());
    }
}

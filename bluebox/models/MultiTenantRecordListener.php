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
    public function getUserId()
    {
        return users::getAttr('Account', 'account_id');
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
        $q = &$event->getQuery();

        if (!isset($q->_hasRunMultiTenantPre) || (isset($q->_hasRunMultiTenantPre) && $q->_hasRunMultiTenantPre === False))
			$q->andWhere('account_id = ' .$this->getUserId() . ' or account_id = -1');
        $q->_hasRunMultiTenantPre = True;
    }

    public function preDqlSelect($event)
    {
        $q = &$event->getQuery();

	$uid=$this->getUserId();

	if (!is_null($uid)) {

	        if (!isset($q->_hasRunMultiTenantPre) || (isset($q->_hasRunMultiTenantPre) && $q->_hasRunMultiTenantPre === False))
				$q->andWhere('account_id = ' .$uid . ' or account_id = -1');
	        $q->_hasRunMultiTenantPre = True;
	}
    }

    public function preDqlDelete($event)
    {
        $q = &$event->getQuery();

        if (!isset($q->_hasRunMultiTenantPre) || (isset($q->_hasRunMultiTenantPre) && $q->_hasRunMultiTenantPre === False))
			$q->andWhere('account_id = ' .$this->getUserId() . ' or account_id = -1');
        $q->_hasRunMultiTenantPre = True;
    }
}

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
/*    protected $_options;

    public function __construct(array $options)
    {
        $this->_options = $options;
    }*/

    /** Handle object references **/
    public function preSave(Doctrine_Event $event)
    {
        $record =& $event->getInvoker();
        if (!$record->account_id) {         // This is dangerous - should we be more strict?
            if ((users::$user) and (users::$user->account_id)) {
                $record->account_id = users::$user->account_id;
            } else {
                Kohana::log('debug', 'Throwing exception - preSave');
                throw new Exception('Invalid user - can\'t save', 1);
            }
        }
        Kohana::log('debug', 'Got here - would have done stuff to save.');
    }

    public function preUpdate(Doctrine_Event $event)
    {
        $record =& $event->getInvoker();
        if (!$record->account_id) {         // This is dangerous - should we be more strict?
            Kohana::log('debug', 'Throwing exception - preUpdate');
            throw new Exception('Invalid user - can\'t save', 1);
        }
        
        if ($record->account_id != users::$user->account_id) {
            Kohana::log('debug', 'Throwing exception - preUpdate');
            throw new Exception('Can\'t update other peoples records', 1);
        }
        Kohana::log('debug', 'Got here - would have done stuff to update.');
    }

    public function preInsert(Doctrine_Event $event)
    {
        $record = $event->getInvoker();
        if (!$record->account_id) {         // This is dangerous - should we be more strict?
            if ((users::$user) and (users::$user->account_id)) {
                $record->account_id = users::$user->account_id;
            } else {
                Kohana::log('debug', 'Throwing exception - preInsert');
                throw new Exception('Invalid user - can\'t save', 1);
            }
        }
        Kohana::log('debug', 'Got here - would have done stuff to insert.');

    /*foreach ($this->_options['relations'] as $relation => $options)
    {
      $table = Doctrine::getTable($options['className']);
      $relation = $table->getRelation($options['foreignAlias']);

      $table
        ->createQuery()
        ->update()
        ->set($options['columnName'], $options['columnName'].' + 1')
        ->where($relation['local'].' = ?', $invoker->$relation['foreign'])
        ->execute();
    }*/

    }

    public function preDelete(Doctrine_Event $event)
    {
        $record =& $event->getInvoker();
        if ((users::$user) and (users::$user->account_id)) {
            if ($record->account_id != users::$user->account_id)
                throw new Exception('Can\'t delete other peoples records', 1);
        } else {
            throw new Exception('Invalid user - can\'t save', 1);
        }
        Kohana::log('debug', 'Got here - would have done stuff to delete.');
    }


    

    /** Handle DQL **/
    public function preDqlUpdate($event)
    {
        $q = $event->getQuery();
        $q->andWhere('account_id = ?', users::$user->account_id);
        Kohana::log('debug', 'Got here - would have done stuff to DQL update.');
    }

    public function preDqlSelect($event)
    {
        $q = $event->getQuery();
        // HACK! TODO: Fix this
        // For now, if there's no authenticated user, don't filter on any user for selects
        if ((!users::$user) or (!users::$user->account_id)) {
            return;
        }
        //Kohana::log('debug', 'Current user info' . print_r(users::$user->account_id, TRUE));
        $q->andWhere('account_id = ?', users::$user->account_id);
        Kohana::log('debug', 'Got here - would have done stuff to DQL select.' . get_class($q));
    }

    public function preDqlDelete($event)
    {
        $q = $event->getQuery();
        $q->andWhere('account_id = ?', users::$user->account_id);
        Kohana::log('debug', 'Got here - would have done stuff to DQL delete.');
    }

    public function preValidate($event)
    {
        
        Kohana::log('debug', 'Got here - would have done stuff to validate.');
    }
}

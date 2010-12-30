<?php defined('SYSPATH') or die('No direct access allowed.');

class Context extends Bluebox_Record
{
    public static $errors = array (
        'name' => array (
            'required' => 'Context name is required'
        )
    );
    
    /**
     * Sets the table name, and defines the table columns.
     */
    public function setTableDefinition()
    {
        // COLUMN DEFINITIONS
        $this->hasColumn('context_id', 'integer', 11, array('unsigned' => true, 'notnull' => true, 'primary' => true, 'autoincrement' => true));
        $this->hasColumn('name', 'string', 40, array('notnull' => true, 'notblank' => true));
        $this->hasColumn('locked', 'boolean', NULL, array('default' => false));
        $this->hasColumn('account_id', 'integer', 11, array('unsigned' => true));
    }

    /**
     * Sets up relationships, behaviors, etc.
     */
    public function setUp()
    {
        // RELATIONSHIPS
        $this->hasOne('Account', array('local' => 'account_id', 'foreign' => 'account_id'));
        $this->hasMany('NumberContext', array('local' => 'context_id', 'foreign' => 'context_id', 'cascade' => array('delete')));

        // BEHAVIORS
        $this->actAs('GenericStructure');
        $this->actAs('Timestampable');
        $this->actAs('MultiTenant');
    }

    public function preDelete(Doctrine_Event $event)
    {
        $unlimit = Session::instance()->get('bluebox.delete.unlimit', FALSE);

        if (!$unlimit AND count($this->getTable()->findAll()) <= 1)
        {
            throw new Exception('You can not delete the only context for this account!');
        }
    }

    public static function getContextByType($type, $account_id = NULL)
    {
        if ($account_id)
        {
            Doctrine::getTable('Context')->getRecordListener()->get('MultiTenant')->setOption('disabled', TRUE);
            
            $contexts = Doctrine_Query::create()
                ->from('Context c')
                ->where('account_id = ?', array($account_id))
                ->execute();

            Doctrine::getTable('Context')->getRecordListener()->get('MultiTenant')->setOption('disabled', FALSE);
        }
        else
        {
            $contexts = Doctrine::getTable('Context')->findAll();
        }

        $context_id = NULL;

        foreach ($contexts as $context)
        {
            if (empty($context['registry']['type']))
            {
                continue;
            }
            
            if ($context['registry']['type'] == $type)
            {
                $context_id = $context['context_id'];

                break;
            }
        }

        return $context_id;
    }
}

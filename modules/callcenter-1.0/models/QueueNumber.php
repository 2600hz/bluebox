<?php defined('SYSPATH') or die('No direct access allowed.');

class QueueNumber extends Number
{
    public static $description = 'Queue';

    public function setUp()
    {
        parent::setUp();

        $this->setAttribute(Doctrine::ATTR_EXPORT, Doctrine::EXPORT_NONE);

        $this->hasOne('Queue as Destination', array('local'   => 'foreign_id',
                                           	    'foreign' => 'queue_id',
                                      		    'owningSide' => FALSE));

        foreach (get_declared_classes() as $class)
        {
            if (is_subclass_of($class, 'Number') or ($class == 'Number'))
            {
                $deviceTable = Doctrine::getTable($class);
                
                $deviceTable->bind(array('Queue', array('local' => 'foreign_id', 'foreign' => 'queue_id')), Doctrine_Relation::ONE);
            }
        }

        $this->actAs('TelephonyEnabled');
    }
}

<?php defined('SYSPATH') or die('No direct access allowed.');

class RingGroupNumber extends Number
{
    public static $description = 'RingGroup';

    public function setUp()
    {
        parent::setUp();

        $this->setAttribute(Doctrine::ATTR_EXPORT, Doctrine::EXPORT_NONE);

        /*
         * Relationships
         */
        // Relate the ring group (ringroup_id) with a generic number identifier (foreign_id) in the Number class.
        // Note carefully that this only works because this model is related to Number
        // The Number class has some "magic" that auto relates the class
        $this->hasOne('RingGroup as Destination', array('local'   => 'foreign_id',
                                          'foreign' => 'ring_group_id',
                                          'owningSide' => FALSE));
        
        foreach (get_declared_classes() as $class)
        {
            if (is_subclass_of($class, 'Number') or ($class == 'Number'))
            {
                $numberTable = Doctrine::getTable($class);

                $numberTable->bind(array('RingGroup', array('local' => 'foreign_id', 'foreign' => 'ring_group_id')), Doctrine_Relation::ONE);
            }
        }

        $this->actAs('TelephonyEnabled');
    }
}

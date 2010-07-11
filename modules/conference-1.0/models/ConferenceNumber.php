<?php defined('SYSPATH') or die('No direct access allowed.');

class ConferenceNumber extends Number
{
    public static $description = 'Conference';

    public function setUp()
    {
        parent::setUp();

        $this->setAttribute(Doctrine::ATTR_EXPORT, Doctrine::EXPORT_NONE);

//        $this->hasOne('Conference', array('local'   => 'foreign_id',
//                                          'foreign' => 'conference_id',
//                                          'owningSide' => FALSE));

        $this->hasOne('Conference as Destination', array('local'   => 'foreign_id',
                                          'foreign' => 'conference_id',
                                          'owningSide' => FALSE));

        foreach (get_declared_classes() as $class)
        {
            if (is_subclass_of($class, 'Number') or ($class == 'Number'))
            {
                $deviceTable = Doctrine::getTable($class);
                
                $deviceTable->bind(array('Conference', array('local' => 'foreign_id', 'foreign' => 'conference_id')), Doctrine_Relation::ONE);
            }
        }

        $this->actAs('TelephonyEnabled');
    }
}
<?php defined('SYSPATH') or die('No direct access allowed.');

class AutoAttendantNumber extends Number
{
    public static $description = 'AutoAttendant';

    public function initialize()
    {
        $numberType = new NumberType();

        $numberType->class = 'AutoAttendantNumber';

        $numberType->module_id = 0;

        $numberType->save();
    }

    public function setUp()
    {
        parent::setUp();

        $this->setAttribute(Doctrine::ATTR_EXPORT, Doctrine::EXPORT_ALL ^ Doctrine::EXPORT_CONSTRAINTS);
        
        $this->hasOne('AutoAttendant as Destination', array('local'   => 'foreign_id',
                                   'foreign' => 'auto_attendant_id'));

        foreach (get_declared_classes() as $class)
        {
            if (is_subclass_of($class, 'Number') or ($class == 'Number'))
            {
                $numberTable = Doctrine::getTable($class);

                $numberTable->bind(array('AutoAttendant', array('local' => 'foreign_id', 'foreign' => 'auto_attendant_id')), Doctrine_Relation::ONE);
            }
        }

        $this->actAs('TelephonyEnabled');
    }
}

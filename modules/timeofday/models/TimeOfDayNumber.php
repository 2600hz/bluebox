<?php defined('SYSPATH') or die('No direct access allowed.');

class TimeOfDayNumber extends Number
{
    public static $description = 'TimeOfDay';

    public function initialize()
    {
        $numberType = new NumberType();
        $numberType->class = 'TimeOfDayNumber';
        $numberType->module_id = 0;
        $numberType->save();
    }

    public function setUp()
    {
        parent::setUp();

        $this->setAttribute(Doctrine::ATTR_EXPORT, Doctrine::EXPORT_ALL ^ Doctrine::EXPORT_CONSTRAINTS);

        $this->hasOne('TimeOfDay as Destination', array('local'   => 'foreign_id',
                                   'foreign' => 'time_of_day_id'));

        foreach (get_declared_classes() as $class)
        {
            if (is_subclass_of($class, 'Number') or ($class == 'Number'))
            {
                $numberTable = Doctrine::getTable($class);
                
                $numberTable->bind(array('TimeOfDay', array('local' => 'foreign_id', 'foreign' => 'time_of_day_id')), Doctrine_Relation::ONE);
            }
        }

        $this->actAs('TelephonyEnabled');
    }
}

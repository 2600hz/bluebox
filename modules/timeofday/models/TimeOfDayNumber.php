<?php defined('SYSPATH') or die('No direct access allowed.');

class TimeOfDayNumber extends Number {
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

        /*
         * Relationships
         */
        // Relate the auto attendant (auto_attendant_id) with a generic number identifier (foreign_id) in the Number class.
        // Note carefully that this only works because this model is related to Number
        // The Number class has some "magic" that auto relates the class
        $this->hasOne('TimeOfDay', array('local'   => 'foreign_id',
                                   'foreign' => 'time_of_day_id'));

        // Add relation on the other side, too, including all extended models that may have already loaded
        foreach (get_declared_classes() as $class)
        {
            if (is_subclass_of($class, 'Number') or ($class == 'Number')) {
                $numberTable = Doctrine::getTable($class);
                $numberTable->bind(array('TimeOfDay', array('local' => 'foreign_id', 'foreign' => 'time_of_day_id')), Doctrine_Relation::ONE);
            }
        }

        $this->actAs('TelephonyEnabled');
    }
}

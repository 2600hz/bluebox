<?php defined('SYSPATH') or die('No direct access allowed.');

class FeatureNumber extends Number
{
    public static $description = 'Feature';
    public $name_field = 'ftr_display_name';

	public function setUp()
    {
        parent::setUp();
        $this->setAttribute(Doctrine::ATTR_EXPORT, Doctrine::EXPORT_NONE);
        $this->hasOne('Feature as Destination', array('local'   => 'foreign_id',
                                          'foreign' => 'ftr_id',
                                          'owningSide' => FALSE));

        foreach (get_declared_classes() as $class)
        {
            if (is_subclass_of($class, 'Number') or ($class == 'Number'))
            {
                $deviceTable = Doctrine::getTable($class);
                $deviceTable->bind(array('Feature', array('local' => 'foreign_id', 'foreign' => 'ftr_id')), Doctrine_Relation::ONE);
            }
        }
        $this->actAs('TelephonyEnabled');
    }
}
?>
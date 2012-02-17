<?php defined('SYSPATH') or die('No direct access allowed.');

class FeatureCodeNumber extends Number
{
    public static $description = 'FeatureCode';

    public function setUp()
    {
        parent::setUp();

        $this->setAttribute(Doctrine::ATTR_EXPORT, Doctrine::EXPORT_NONE);

        $this->hasOne('FeatureCode as Destination', array('local'   => 'foreign_id',
                                          'foreign' => 'feature_code_id',
                                          'owningSide' => FALSE));

        foreach (get_declared_classes() as $class)
        {
            if (is_subclass_of($class, 'Number') or ($class == 'Number'))
            {
                $deviceTable = Doctrine::getTable($class);
                
                $deviceTable->bind(array('FeatureCode', array('local' => 'foreign_id', 'foreign' => 'feature_code_id')), Doctrine_Relation::ONE);
            }
        }

        $this->actAs('TelephonyEnabled');
    }
}
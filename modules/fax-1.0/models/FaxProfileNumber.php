<?php defined('SYSPATH') or die('No direct access allowed.');

class FaxProfileNumber extends Number
{
    public static $description = 'Inbound Fax';

	public function setUp()
    {
        parent::setUp();
        $this->setAttribute(Doctrine::ATTR_EXPORT, Doctrine::EXPORT_NONE);
        $this->hasOne('FaxProfile as Destination', array('local'   => 'foreign_id',
                                          'foreign' => 'fxp_id',
                                          'owningSide' => FALSE));

        foreach (get_declared_classes() as $class)
        {
            if (is_subclass_of($class, 'Number') or ($class == 'Number'))
            {
                $deviceTable = Doctrine::getTable($class);
                $deviceTable->bind(array('FaxProfile', array('local' => 'foreign_id', 'foreign' => 'fxp_id')), Doctrine_Relation::ONE);
            }
        }
        $this->actAs('TelephonyEnabled');
    }
}
?>
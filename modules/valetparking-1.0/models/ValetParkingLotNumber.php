<?php defined('SYSPATH') or die('No direct access allowed.');

class ValetParkingLotNumber extends Number
{
    public static $description = 'Valet Parking Lot';

	public function setUp()
    {
        parent::setUp();
        $this->setAttribute(Doctrine::ATTR_EXPORT, Doctrine::EXPORT_NONE);
        $this->hasOne('ValetParkingLot as Destination', array('local'   => 'foreign_id',
                                          'foreign' => 'vpl_id',
                                          'owningSide' => FALSE));

        foreach (get_declared_classes() as $class)
        {
            if (is_subclass_of($class, 'Number') or ($class == 'Number'))
            {
                $deviceTable = Doctrine::getTable($class);
                $deviceTable->bind(array('ValetParkingLot', array('local' => 'foreign_id', 'foreign' => 'vpl_id')), Doctrine_Relation::ONE);
            }
        }
        $this->actAs('TelephonyEnabled');
    }
}
?>
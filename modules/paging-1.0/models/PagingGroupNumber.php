<?php defined('SYSPATH') or die('No direct access allowed.');

class PagingGroupNumber extends Number
{
    public static $description = 'Paging Group';

	public function setUp()
    {
        parent::setUp();
        $this->setAttribute(Doctrine::ATTR_EXPORT, Doctrine::EXPORT_NONE);
        $this->hasOne('PagingGroup as Destination', array('local'   => 'foreign_id',
                                          'foreign' => 'pgg_id',
                                          'owningSide' => FALSE));

        foreach (get_declared_classes() as $class)
        {
            if (is_subclass_of($class, 'Number') or ($class == 'Number'))
            {
                $deviceTable = Doctrine::getTable($class);
                $deviceTable->bind(array('PagingGroup', array('local' => 'foreign_id', 'foreign' => 'pgg_id')), Doctrine_Relation::ONE);
            }
        }
        $this->actAs('TelephonyEnabled');
    }
}
?>
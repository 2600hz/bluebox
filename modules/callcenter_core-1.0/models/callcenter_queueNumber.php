<?php defined('SYSPATH') or die('No direct access allowed.');

class callcenter_queueNumber extends Number
{
    public static $description = 'Callcenter Queue';

    public function setUp()
    {
        parent::setUp();
        $this->setAttribute(Doctrine::ATTR_EXPORT, Doctrine::EXPORT_NONE);
        $this->hasOne('callcenter_queue as Destination',
			array(
				'local'   => 'foreign_id',
				'foreign' => 'ccq_id',
				'owningSide' => FALSE
			)
		);

        foreach (get_declared_classes() as $class)
        {
            if (is_subclass_of($class, 'Number') or ($class == 'Number'))
            {
                $deviceTable = Doctrine::getTable($class);
                $deviceTable->bind(array('callcenter_queue', array('local' => 'foreign_id', 'foreign' => 'ccq_id')), Doctrine_Relation::ONE);
            }
        }
        $this->actAs('TelephonyEnabled');
    }
}

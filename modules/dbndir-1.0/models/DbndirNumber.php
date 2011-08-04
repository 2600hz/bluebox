<?php defined('SYSPATH') or die('No direct access allowed.');

class DbndirNumber extends Number
{
    public static $description = 'Dial By Name Dir';

    public function setUp()
    {
        parent::setUp();
        $this->setAttribute(Doctrine::ATTR_EXPORT, Doctrine::EXPORT_NONE);
        $this->hasOne('dbndir as Destination',
			array(
				'local'   => 'foreign_id',
				'foreign' => 'dbn_id',
				'owningSide' => FALSE
			)
		);

        foreach (get_declared_classes() as $class)
            if (is_subclass_of($class, 'Number') or ($class == 'Number'))
            {
                $deviceTable = Doctrine::getTable($class);
                $deviceTable->bind(array('dbndir', array('local' => 'foreign_id', 'foreign' => 'dbn_id')), Doctrine_Relation::ONE);
            }
        $this->actAs('TelephonyEnabled');
    }
}

?>
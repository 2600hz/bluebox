<?php defined('SYSPATH') or die('No direct access allowed.');

class VoicemailNumber extends Number
{
    public static $description = 'Voicemail';

    public function setUp()
    {
        parent::setUp();

        $this->setAttribute(Doctrine::ATTR_EXPORT, Doctrine::EXPORT_NONE);

        $this->hasOne('Voicemail as Destination', array('local'   => 'foreign_id',
                                          'foreign' => 'voicemail_id',
                                          'owningSide' => FALSE));

        foreach (get_declared_classes() as $class)
        {
            if (is_subclass_of($class, 'Number') or ($class == 'Number'))
            {
                $deviceTable = Doctrine::getTable($class);
                
                $deviceTable->bind(array('Voicemail', array('local' => 'foreign_id', 'foreign' => 'voicemail_id')), Doctrine_Relation::ONE);
            }
        }
        
        $this->actAs('TelephonyEnabled');
    }
}

<?php defined('SYSPATH') or die('No direct access allowed.');

class DeviceRelation extends Bluebox_Relation
{
    protected $baseModelName = 'Device';

    protected $relationType = Doctrine_Relation::ONE;

    protected $cascade = TRUE;
}

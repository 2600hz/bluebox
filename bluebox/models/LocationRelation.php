<?php defined('SYSPATH') or die('No direct access allowed.');

class LocationRelation extends Bluebox_Relation
{
    protected $baseModelName = 'Location';

    protected $relationType = Doctrine_Relation::ONE;

    protected $cascade = TRUE;
}

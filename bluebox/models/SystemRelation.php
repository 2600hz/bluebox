<?php defined('SYSPATH') or die('No direct access allowed.');

class SystemRelation extends Bluebox_Relation
{
    protected $baseModelName = 'System';

    protected $relationType = Doctrine_Relation::ONE;

    protected $cascade = TRUE;
}

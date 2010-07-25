<?php defined('SYSPATH') or die('No direct access allowed.');

class PackageRelation extends Bluebox_Relation
{
    protected $baseModelName = 'Package';

    protected $relationType = Doctrine_Relation::ONE;

    protected $cascade = TRUE;
}

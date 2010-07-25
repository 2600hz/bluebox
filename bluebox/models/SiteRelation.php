<?php defined('SYSPATH') or die('No direct access allowed.');

class SiteRelation extends Bluebox_Relation
{
    protected $baseModelName = 'Site';

    protected $relationType = Doctrine_Relation::ONE;

    protected $cascade = TRUE;
}

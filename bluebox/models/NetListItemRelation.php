<?php defined('SYSPATH') or die('No direct access allowed.');

class NetListItemRelation extends Bluebox_Relation
{
    protected $baseModelName = 'NetListItem';

    protected $relationType = Doctrine_Relation::ONE;

    protected $cascade = TRUE;
}
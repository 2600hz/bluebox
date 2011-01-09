<?php defined('SYSPATH') or die('No direct access allowed.');

class NetListRelation extends Bluebox_Relation
{
    protected $baseModelName = 'NetList';

    protected $relationType = Doctrine_Relation::ONE;

    protected $cascade = TRUE;
}
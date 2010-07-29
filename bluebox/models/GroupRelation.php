<?php defined('SYSPATH') or die('No direct access allowed.');

class GroupRelation extends Bluebox_Relation
{
    protected $baseModelName = 'Group';

    protected $relationType = Doctrine_Relation::ONE;

    protected $cascade = TRUE;
}

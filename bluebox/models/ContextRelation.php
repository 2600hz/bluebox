<?php defined('SYSPATH') or die('No direct access allowed.');

class ContextRelation extends Bluebox_Relation
{
    protected $baseModelName = 'Context';

    protected $relationType = Doctrine_Relation::ONE;

    protected $cascade = TRUE;
}

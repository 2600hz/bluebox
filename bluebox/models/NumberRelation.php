<?php defined('SYSPATH') or die('No direct access allowed.');

class NumberRelation extends Bluebox_Relation
{
    protected $baseModelName = 'Number';

    protected $relationType = Doctrine_Relation::ONE;

    protected $cascade = TRUE;
}

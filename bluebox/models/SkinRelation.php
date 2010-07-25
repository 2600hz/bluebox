<?php defined('SYSPATH') or die('No direct access allowed.');

class SkinRelation extends Bluebox_Relation
{
    protected $baseModelName = 'Skin';

    protected $relationType = Doctrine_Relation::ONE;

    protected $cascade = TRUE;
}

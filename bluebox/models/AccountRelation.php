<?php defined('SYSPATH') or die('No direct access allowed.');

class AccountRelation extends Bluebox_Relation
{
    protected $baseModelName = 'Account';
    
    protected $relationType = Doctrine_Relation::ONE;

    protected $cascade = TRUE;
}

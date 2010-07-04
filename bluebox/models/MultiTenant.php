<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * MultiTenant.php - MultiTenant behavior
 *
 * Allows for using $this->actAs('MultiTenant') in a model to enforce verifying account_id
 * on that table (for updates & inserts, too!). Adds account_id as well if it's missing.
 *
 *
 * @author Darren Schreiber <d@d-man.org>
 * @license Closed/Copyright Darren Schreiber
 */

class MultiTenant extends Doctrine_Template {
    
    public function setTableDefinition()
    {
	$this->hasColumn('account_id', 'integer', 11, array('unsigned' => true, 'default' => NULL));

        $this->addListener(new MultiTenantRecordListener(), 'MultiTenant');
    }

    public function setUp()
    {
        $this->hasOne('Account', array('local' => 'account_id', 'foreign' => 'account_id'));
    }
}

<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * powerdns.php - The power dns domains tables
 *
 * @author K Anderson
 */

class PdnsSuperMaster extends Bluebox_Record {

    function setTableDefinition(){
        $this->hasColumn('id', 'string', 25, array('notnull' => true, 'notblank' => true));
        $this->hasColumn('nameserver', 'string', 255, array('notnull' => true, 'notblank' => true));
        $this->hasColumn('account', 'string', 40, array('notnull' => true, 'notblank' => true));
    }

    function setUp() {
        //$this->actAs('MultiTenant');
    }
}

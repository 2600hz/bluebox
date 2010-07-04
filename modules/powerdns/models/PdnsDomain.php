<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * powerdns.php - The power dns domains tables
 *
 * @author K Anderson
 */

class PdnsDomain extends Bluebox_Record {
    public static $errors = array (
        'name' => array (
            'unique' => 'Domain is not unique'
        )
    );

    function setTableDefinition(){
        $this->hasColumn('id', 'integer', 11, array('unsigned' => true, 'primary' => true, 'autoincrement' => true));
        $this->hasColumn('name', 'string', 255, array('notnull' => true, 'notblank' => true, 'unique' => true));
        $this->hasColumn('master', 'string', 128, array('default' => NULL));
        $this->hasColumn('last_check', 'integer', 11, array('notnull' => true, 'default' => 1));
        $this->hasColumn('type', 'string', 6, array('default' => 'NATIVE'));
        $this->hasColumn('notified_serial', 'integer', 11, array('unsigned' => true, 'default' => 0));
        $this->hasColumn('account', 'string', 40, array('default' => NULL));
    }

    function setUp() {
        $this->hasMany('PdnsRecord', array('local' => 'id', 'foreign' => 'domain_id'));
        //$this->actAs('MultiTenant');

        /*
         * CREATE UNIQUE INDEX name_index ON domains(name);
         */
    }
}

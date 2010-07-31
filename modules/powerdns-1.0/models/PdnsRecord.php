<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * powerdns.php - The power dns domains tables
 *
 * @author K Anderson
 */

class PdnsRecord extends Bluebox_Record {

    function setTableDefinition(){
        $this->hasColumn('id', 'integer', 11, array('unsigned' => true, 'primary' => true, 'autoincrement' => true));
        $this->hasColumn('domain_id', 'integer', 11, array('unsigned' => true, 'notnull' => true, 'notblank' => true));
        $this->hasColumn('name', 'string', 255, array('notnull' => true, 'notblank' => true));
        $this->hasColumn('type', 'string', 6, array('default' => 'A'));
        $this->hasColumn('content', 'string', 255, array('notnull' => true, 'notblank' => true));
        $this->hasColumn('ttl', 'integer', 11, array('unsigned' => true, 'default' => 600));
        $this->hasColumn('prio', 'integer', 11, array('unsigned' => true, 'default' => 0));
        $this->hasColumn('change_date', 'integer', 11, array('unsigned' => true, 'default' => 0));
    }

    function setUp() {
        $this->hasOne('PdnsDomain', array('local' => 'domain_id', 'foreign' => 'id'));
        //$this->actAs('MultiTenant');

        /*
         * CREATE INDEX rec_name_index ON records(name);
         * CREATE INDEX nametype_index ON records(name,type);
         * CREATE INDEX domain_id ON records(domain_id);
         */
    }
}

<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * @author Jon Blanton <rjdev943@gmail.com>
 * @author Rockwood Cataldo <rjdev943@gmail.com>
 * @license MPL
 * @package Xmpp
 */

class Xmpp extends Bluebox_Record
{
    function setTableDefinition()
    {
        $this->hasColumn('xmpp_id', 'integer', 11, array('primary' => true, 'unsigned' => true, 'autoincrement' => true));
        $this->hasColumn('name', 'string', 200, array('notnull' => true, 'notblank' => true));
        $this->hasColumn('login', 'string', 200, array('notnull' => true, 'notblank' => true));
    }

    public function setUp()
    {
        $this->actAs('GenericStructure');
        $this->actAs('Timestampable');
        $this->actAs('TelephonyEnabled');
        $this->actAs('MultiTenant');
    }
}

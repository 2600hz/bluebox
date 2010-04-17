<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * networklist.php - Network List Model
 *
 * @author Darren Schreiber <d@d-man.org>
 * @license MPL
 * @package FreePBX3
 * @subpackage NetList
 */

class NetList extends FreePbx_Record
{
    /**
     * Sets the table name, and defines the table columns.
     */
    function setTableDefinition()
    {
        // COLUMN DEFINITIONS
        $this->hasColumn('net_list_id', 'integer', 11, array('unsigned' => true, 'primary' => true, 'autoincrement' => true));
        $this->hasColumn('name', 'string', 100, array('notnull' => true, 'notblank' => true));
        $this->hasColumn('system_list', 'string', 100, array('default' => '', 'notnull' => true));
        //ALTER TABLE `freepbx`.`net_list` ADD COLUMN `allow` TINYINT(1)  NOT NULL DEFAULT 0 AFTER `system_list`;
        $this->hasColumn('allow', 'boolean', NULL, array('notnull' => true, 'default' => FALSE));
    }

    /**
     * Sets up relationships, behaviors, etc.
     */
    function setUp()
    {
        // RELATIONSHIPS
        $this->hasMany('NetListItem', array('local' => 'net_list_id', 'foreign' => 'net_list_id', 'onDelete' => 'CASCADE'));

        // BEHAVIORS
        $this->actAs('TelephonyEnabled');
        $this->actAs('Timestampable');
        $this->actAs('MultiTenant');
    }
}


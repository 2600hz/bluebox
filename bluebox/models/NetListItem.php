<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * networklist.php - Network List Model
 *
 * @author Darren Schreiber <d@d-man.org>
 * @license MPL
 * @package Bluebox
 * @subpackage NetList
 */

class NetListItem extends Bluebox_Record
{
    /**
     * Sets the table name, and defines the table columns.
     */
    function setTableDefinition()
    {
        // COLUMN DEFINITIONS
        $this->hasColumn('net_list_item_id', 'integer', 11, array('unsigned' => true, 'primary' => true, 'autoincrement' => true));
        $this->hasColumn('net_list_id', 'integer', 11, array('unsigned' => true));
        $this->hasColumn('record', 'string', 100, array('notnull' => true, 'notblank' => true));  // Can be an IP address, network range, or domain name
        $this->hasColumn('description', 'string', 100);
        $this->hasColumn('allow', 'boolean', NULL, array('notnull' => true, 'default' => TRUE));
        $this->hasColumn('trunk_id', 'integer', 11, array('unsigned' => true));
    }

    /**
     * Sets up relationships, behaviors, etc.
     */
    function setUp()
    {
        // RELATIONSHIPS
        $this->hasOne('NetList', array('local' => 'net_list_id', 'foreign' => 'net_list_id'));

        // BEHAVIORS
        $this->actAs('Timestampable');
    }
}


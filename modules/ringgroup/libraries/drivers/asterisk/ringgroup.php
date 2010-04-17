<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * ringgroup.php - Asterisk Ring Group driver
 *
 * @author K Anderson
 * @license LGPL
 * @package FreePBX3
 * @subpackage RingGroup
 */
class Asterisk_RingGroup_Driver extends Asterisk_Base_Driver {
    /**
     * Indicate we support Asterisk
     */
    public static function set($obj)
    {
        // Get the asterisk driver
        $doc = Telephony::getDriver()->doc;

        $ringgroup = 'ringgroup_' . $obj->ring_group_id;
        $doc->deleteContext('queues.conf', $ringgroup);

        $ringGroupMembers = $obj->RingGroupMember->toArray();
        foreach($ringGroupMembers as $ringGroupMember) {
            $device = Doctrine::getTable('Device')->findOneByDeviceId($ringGroupMember['device_id']);
            if (empty($device)) continue;
            $doc->append('queues.conf', $ringgroup, 'member', '> SIP/' .$device->Sip->username, TRUE);
        }
    }

    public static function delete($obj) {
        $doc = Telephony::getDriver()->doc;
        
        $ringgroup = 'ringgroup_' . $obj->ring_group_id;
        $doc->deleteContext('queues.conf', $ringgroup);
    }

    public static function dialplan($obj)
    {
        $doc = Telephony::getDriver()->doc;
        
        $doc->add('Queue(ringgroup_' .$obj->RingGroup->ring_group_id .')');
    }
}

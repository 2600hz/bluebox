<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * @author K Anderson
 * @license MPL
 * @package Bluebox
 */
class RingGroup_Plugin extends Bluebox_Plugin
{
    /*private $updateNumbers = FALSE;*/

    public function selector() {
        $subview = new View('ringgroup/selector');
        $subview->section = 'ringgroup';

        $groups = Doctrine::getTable('RingGroup')->findAll();

        $ringgroups = array();
        foreach($groups as $ringgroup) {
            $ringgroups[$ringgroup->ring_group_id] = $ringgroup->name;
        }

        if (empty($ringgroups)) {
            return FALSE;
        }

        $subview->ringGroups = $ringgroups;
        
        $subview->fallback_number = '1234';
        $subview->fallback_context = NULL;
        
        // Add our view to the main application
        $this->views[] = $subview;
    }

/*    public function checkChanges() {
        // get the base ring group
        $base = $this->ringgroup;

        // if that failed get out of here!
        if (!$base || empty($base->ring_group_id))
            return FALSE;

        // get a list of the modified fields
        $modified = $base->getModified();

        // if any of the fields that exist in the dialplan where modified then
        // set the flag to dirty any numbers associated with this ringgroup
        if (array_key_exists('timeout', $modified) ||
                array_key_exists('fallback_number_id', $modified)) {
            kohana::log('debug', 'Flagging this ringgroup as needing a diaplan rebuild.');
            $this->updateNumbers = TRUE;
        }
    }*/

    public function dirtyNumbers()
    {
        if (!$this->updateNumbers) return;
        
        // get the base ring group
        $base = $this->ringgroup;

        // if that failed get out of here!
        if (!$base || empty($base->ring_group_id))
            return FALSE;

        // get all the number records for this ring group
        $query = Doctrine_Query::create()
            ->select('number_id, number')
            ->from('Number')
            ->where('foreign_id = ?', $base->ring_group_id)
            ->andWhere('class_type = ?', 'RingGroupNumber');

        // dirty each record we got so the dialplan will be re-generated
        $results = $query->execute();
        foreach ($results as $result) {
            $result->markModified('number');
            $result->save();
        }
    }

}

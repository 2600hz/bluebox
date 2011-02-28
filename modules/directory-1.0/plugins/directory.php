<?php defined('SYSPATH') or die('No direct access allowed.');

class Directory_Plugin extends Bluebox_Plugin
{
    protected $name='directory';

    protected function viewSetup()
    {
        parent::viewSetup();
        
        $dropdown=array();

        $rec=Doctrine::getTable('Grouping')->findOneBylevel(0);
        $dropdown[$rec->grouping_id]=str_repeat("&nbsp;",($rec->level)*5).$rec->name;
        foreach ($rec->getNode()->getDescendants() AS $desc) {
            //$dropdown[$desc->grouping_id]=$desc->name;
            $dropdown[$desc->grouping_id]=str_repeat("&nbsp;",($desc->level)*5).$desc->name;
        }

        // Load the states array to the view
        $this->subview->groupings = $dropdown;

        return TRUE;
    }

}

<?php defined('SYSPATH') or die('No direct access allowed.');
/*
 * @author Michael Phillips
 * @license MPL
 * @package ACL
 * @subpackage ACL_Groups
 */

class Permission_Plugin extends FreePbx_Plugin
{
    protected $preloadModels = array('Groups');

    public function add()
    {
    }

    public function update()
    {
        $view = new View('permission/usergroup');
        $view->tab = 'main';
        $view->section = 'other';

		
        // What are we working with here?
        $base = $this->getBaseModelObject();

        // While the field may be defined by an app or another module, it may not be populated! Double-check
        if (!$base)
            return FALSE;	// Nothing to do here.

		if($this->input->post())
		{
			PermissionManager::assignUserGroups($base->user_id, $this->input->post('_group'));
		}	
        
        $view->groupsSelector = PermissionManager::renderGroupSelector($base->user_id, 1);
        
        // Add our view to the main application
        $this->views[] = $view;
    }

    public function save()
    {
    // What are we working with here?
        $base = $this->getBaseModelObject();

        // While the field may be defined by an app or another module, it may not be populated! Double-check
        if (!$base)
            return FALSE;	// Nothing to do here.


	}
}

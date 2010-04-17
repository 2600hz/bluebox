<?php
class Permission_Controller extends FreePbx_Controller
{
	public function __construct()
	{
		parent::__construct();
		//Doctrine::createTablesFromArray(array('Group'));
	}
	
	public function index()
	{
		
	}
	
	public function permission()
	{
	
		$this->view->title = 'Permissions';
		
		if($this->input->post())
		{
			foreach($this->input->post() as $group_id => $permission)
			{
				PermissionManager::setPermission($group_id,$permission);
			}
		}

		$account_id = 1;
		
		$this->view->table = PermissionManager::renderPermissionTable($account_id);
		
		
		
	}

	public function group()
	{
		$this->view->title = 'Groups';
		if($this->input->post())
		{
			PermissionManager::addGroup(1, $this->input->post('name'));
		}
		
		$account_id = 1;
		
		$this->view->table = PermissionManager::renderGroupTable($account_id);
	}
}
?>

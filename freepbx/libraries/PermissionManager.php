<?php
class PermissionManager
{
	public static function getUserGroups($user_id)
	{
		$groupArray = array();
		
		$groups = Doctrine::getTable('UserGroup')->findByUserId(array($user_id));
		
		foreach($groups as $group)
		{
			$groupArray[] = $group->group_id;
		}
		
		return $groupArray;
	}

	public static function assignUserGroups($user_id, $groups)
	{
		if(sizeof($groups) == 0)
		{
			return;
		}
		
		$q = Doctrine_Query::create()
		->delete('UserGroup ug')
		->where('ug.user_id = ' . $user_id);
		
		$q->execute();
		
		foreach($groups as $group_id)
		{
			$usergroup = new UserGroup();
			$usergroup->user_id = $user_id;
			$usergroup->group_id = $group_id;
			$usergroup->save();
		}
	}

	public static function renderPermissionTable($account_id)
	{
		
		$groups = self::getGroups($account_id);
		
		$modules = Doctrine::getTable('Module')->findAll();
		
		$html = '<table width="100%">';
		

		$html .= '<tr>';
		$html .= '<th>Modules</th>';
		
		foreach($groups as $group)
		{
			$html .= '<th> ' . $group . '</th>';
		}
		
		$html .= '</tr>';
		
				

		foreach($modules as $module)
		{
			$html .= '<tr>';
			$html .= '<td>' . $module->display_name . '</td>';
			foreach($groups as $group_id => $group_name)
			{
				$html .='<td> ' . form::checkbox($group_id . '[' . $module->name . ']', 'access', self::groupControllerAccess($group_id, $module->name)) .  '</td>';  
				//in the future 'access' will have add, delete, list etc...
			}
			
			$html .= '</tr>';
		}
		
		$html .= '</table>';
		return $html;
	}
	

	
	public static function renderGroupTable($account_id)
	{
		$groups = self::getGroups($account_id);
		
		$html = '<table width="50%">';
		$html .=	'<tr><th>Group</th></tr>';

		foreach($groups as $group_id => $group_name)
		{
			$html .= '<tr><td> ' . $group_name . '</tr>';
		}

						 
		$html .= '</table>';
		return $html;
	}

	public static function renderGroupSelector($user_id, $account_id)
	{
		$groups = self::getGroups($account_id);
		$usergroups = self::getUserGroups($user_id);
		
		$html = '<ul>';
		foreach($groups as $group_id => $group_name)
		{
			$granted = in_array($group_id, $usergroups);
			$html .= '<li>' . form::checkbox('_group[]', $group_id, $granted) . ' ' . $group_name . '</li>';
		}
		$html .= '</ul>';
		
		return $html;
	}

	
	public function addGroup($account_id, $name)
	{
		$group = new Group();
		$group->name = $name;
		$group->account_id = $account_id;
		$group->save();
	}
	
	public function deleteGroup($group_id)
	{
		$group = Doctrine::getTable('Group')->findOneByGroupId(array($group_id));
		$group->delete();
	}
	
	public function setPermission($group_id, $permission)
	{
		if(!is_numeric($group_id))
		{
			return false;
		}
		$acl = array();
		
		foreach($permission as $key => $value)
		{
			$acl[] = $key;
		}
		
		$group = Doctrine::getTable('Group')->findOneByGroupId(array($group_id));
		$group->permission = $acl;
		$group->save();
	}
	
	
	/*
	 * @todo make false the default return value
	 */
	public static function controllerAccess($controller)
	{
		
		$session = Session::instance();
		
		$user_id = $session->get('user_id');
		if($user_id == 1)
		{
			return TRUE; // User id 1 is the super global admin
		} 
		elseif(!$user_id)
		{
			return FALSE; //deny access to modules if user is not logged in
		}
		

		$group_id = 1; // need to look this up based on the user_id?
		
		$access = self::compilePermissionArray($user_id);
		
		return array_key_exists($controller, array_flip($access));


	}

	public static function compilePermissionArray($user_id)
	{
		$cache= Cache::instance(); // get a cache object
		
		if($cache->get('permission_' . $user_id))
		{
			return $cache->get('permission_' . $user_id);
		}
		
		/* the permission array does not exist in cache, so hit the database */
		$combined = array();
		
		$q = Doctrine_Query::create()
		->select('g.permission, ug.user_id')
		->from('Group g, g.UserGroup ug')
		->where('ug.user_id = ?', $user_id); //get all the groups the user is in
		
		$result = $q->execute(array(), Doctrine::HYDRATE_ARRAY);
	
		foreach($result as $permission)
		{
			$combined = array_merge($permission['permission'], $combined);
		}
		
		$combined = array_unique($combined);
		
		$cache->set('permission_' . $user_id, $combined, NULL, 60*10);
		
		return $combined;
	}

		
	public static function groupControllerAccess($group_id, $controller)
	{
		$group = Doctrine::getTable('Group')->findOneByGroupId(array($group_id));
		if(is_array($group->permission))
		{
			return array_key_exists($controller, array_flip($group->permission));
		} else {
			return FALSE;
		}
	}


	
	public static function getPublicMethods($module)
	{
	    /* Init the return array */
	    $modulesPath = dirname(__FILE__) . "/../../";
	    $controllerPath = sprintf('%s%s/controllers/%s.php', $modulesPath, $module, $module );
	    
	    require_once($controllerPath);
	    
	    $className = 'TrunkManager_Controller';
	    
	    $result = array();
	
	    /* Iterate through each method in the class */
	    foreach (get_class_methods($className) as $method) {
	
	        /* Get a reflection object for the class method */
	        $reflect = new ReflectionMethod($className, $method);
	
	        /* For private, use isPrivate().  For protected, use isProtected() */
	        /* See the Reflection API documentation for more definitions */
	        if($reflect->isPublic()) {
	            /* The method is one we're looking for, push it onto the return array */
	            array_push($result,$method);
	        }
	    }
	
	    /* return the array to the caller */
	    return $result;
	}
	
	public static function getGroups($account_id)
	{
		$groupAccess = array();
		
		$groups = Doctrine::getTable('Group')->findByAccountId(array($account_id));
		
		if($groups)
		{
			foreach($groups as $group)
			{
				$groupAccess[$group->group_id] = $group->name;
			}
			
		}
		
		return $groupAccess;
	}
}

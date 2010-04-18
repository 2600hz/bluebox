<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * permissionmanager.php - Permission Management Controller Class
 *
 * @author K Anderson
 * @license LGPL
 * @package FreePBX3
 * @subpackage PermissionManager
 */
class Permission_Controller extends FreePbx_Controller
{
    public $writable = array();
    
    protected $baseModel = 'Permission';

    public function __construct() {
        parent::__construct();
        Doctrine::getTable('User')->getRecordListener()->get('MultiTenant')->setOption('disabled', true);
        Doctrine::getTable('Location')->getRecordListener()->get('MultiTenant')->setOption('disabled', true);
    }

    public function index() {
        // set the title
        $this->view->title = __('Manage Permissions');

        // include our stylesheet
        stylesheet::add('permission', 50);


        // if this form is submitted
        if ($this->submitted()) {

            // get the user id out of the post
            $userID = $this->input->post('user_id', 0);

            // get the current permissions in the model
            $this->Permissions = Doctrine::getTable($this->baseModel)->findByUserId($userID);

            // foreach of the post vars build an array we can sync to the
            // users permissiongs
            $permissions = array();
            foreach ($_POST as $module => $permission) {

                // fix php weirdness
                $permission = (array)$permission;

                // if this is not a module_permission post var then skip
                if (empty($permission['module_permissions']) ||
                        $permission['module_permissions'] == 'full')
                    continue;

                $modulePermission = $permission['module_permissions'];

                // if the module has custom permissions
                if ($modulePermission == 'custom') {
                    // foreach of the modules views create a permission
                    foreach($permission['customize'] as $method => $customize) {
                        $permissions[] = array (
                            'user_id' => $userID,
                            'controller' => $module,
                            'method' => $method,
                            'permission' => $customize
                        );
                    }
                }

                // set the modules global permission
                $permissions[] = array (
                    'user_id' => $userID,
                    'controller' => $module,
                    'permission' => $modulePermission
                );
            }

            // sync this back with the permission model and save
            $this->Permissions->synchronizeWithArray($permissions);
            $this->Permissions->save();
        }
    }

    public function tree() {
        // get the request type
        $request = $this->input->get('root', '');

        // if there is no request type then die
        if (empty($request)) {
           die();
        }

        // if the request type also contains a primary key, seperete them out
        if ($request != 'source') {
            list($request, $id) = explode('_', $request);
        }

        // get the appropriate results for this request
        switch ($request) {
            case 'source':
                $accounts = Doctrine::getTable('Account')->findAll();
                $results = array();
                foreach ($accounts as $account) {
                    $hasChildren = TRUE;
                    if (count($account->Location) == 0) {
                        $hasChildren = FALSE;
                    }
                    $results[] = array(
                        'text' => $account['name'],
                        'hasChildren' => $hasChildren,
                        'id' => 'locations_' . $account['account_id'],
                        'classes' => 'account'
                    );
                }
                break;
            case 'locations':
                $locations = Doctrine::getTable('Location')->findByAccountId($id);
                $results = array();
                foreach ($locations as $location) {
                    $hasChildren = TRUE;
                    if (count($location->User) == 0) {
                        $hasChildren = FALSE;
                    }
                    $results[] = array(
                        'text' => $location['name'],
                        'hasChildren' => $hasChildren,
                        'id' => 'users_' . $location['location_id'],
                        'classes' => 'location'
                    );
                }
                break;
            case 'users':
                $users = Doctrine::getTable('User')->findByLocationId($id);
                $results = array();
                foreach ($users as $user) {
                    $results[] = array(
                        'text' => $user['first_name'] .' ' .$user['last_name'],
                        'id' => 'user_' . $user['user_id'],
                        'classes' => 'user'
                    );
                }
                break;
            default:
                $results = array();
        }

        // make the result in to a json string and send it out
        echo json_encode($results);
        flush();
        die();
    }

    public function permissions() {
        // get a list of the packages
        $packages = FreePbx_Installer::listPackages(FreePbx_Installer::TYPE_MODULE);

        // remove the packages that dont have a
        foreach ($packages as $name => $package) {
            if (empty($package['navStructures'])) {
                unset($packages[$name]);
                continue;
            }
            $this->view->$name = array('module_permissions' => 'full');
        }

        // get the user id from the post
        $userID = str_replace('user_', '', $this->input->post('root', 0));

        // get all the permissions already assigned
        $userPermissions = Doctrine::getTable($this->baseModel)->findByUserId($userID);
        
        $subViews = array();
        foreach ($userPermissions as $userPermission) {

            $controller = $userPermission['controller'];

            // if this permission rule applies to a custom we need to do some
            // special work
            if(!empty($userPermission['method'])) {

                // first we need to make sure we have the customize view for this
                // modules permissions
                if(!isset($subViews[$controller])) {
                    // if not create a new customize view and initalize it
                    $subViews[$controller] = new View('permission/customize');
                    $subViews[$controller]->module = $controller;
                    $subViews[$controller]->customizable = self::getCustomizable($packages[$controller]);
                }

                // if the there are repopulated vars already in the sub view
                if (isset($subViews[$controller]->$controller)) {
                    // get the array of repopulate vars and append to it
                    $current = $subViews[$controller]->$controller;
                    $current['customize'] += array(
                        $userPermission['method'] => $userPermission['permission']
                    );
                    $subViews[$controller]->$controller = $current;
                } else {
                    // add a new repopulate var for the dropdowns
                    $subViews[$controller]->$controller = array(
                        'customize' => array(
                            $userPermission['method'] => $userPermission['permission']
                        )
                    );
                }
            } else {
                // repopulate the modules radio buttons
                $this->view->$controller = array(
                    'module_permissions' => $userPermission['permission']
                );
            }
        }

        // we need to render the subviews now so the form helper will be happy
        foreach ($subViews as $controller => $subView) {
            $subViews[$controller] = (string)$subView;
        }
        
        $this->view->subViews = $subViews;
        $this->view->user = $userID;
        $this->view->packages = $packages;
    }

    public function customize() {
        // get the module that these customize options apply to
        $module = $this->input->post('module', '');
        $module = str_replace('_module_permissions_custom', '', $module);

        // get the package definition for this module
        $packages = FreePbx_Installer::listPackages();
        $package = $packages[$module];

        // no package, no love
        if (empty($package)) die();

        $this->view->module = $module;
        $this->view->customizable = self::getCustomizable($package);
    }

    public function disabled() {
        
    }

    protected function getCustomizable($package) {
        $customizable = array();

        foreach ($package['navStructures'] as $nav) {

            if (empty($nav['navSubmenu']) || !is_array($nav['navSubmenu'])) {
                $customizable[$nav['navLabel']] = $nav['navURL'];
                continue;
            }

            foreach ($nav['navSubmenu'] as $name => $submenu) {
                if (is_array($submenu)) {
                    $customizable[$name] = $submenu['url'];
                } else {
                    $customizable[$name] = $submenu;
                }
            }
        }

        return array_unique($customizable);
    }

}
<?php defined('SYSPATH') or die('No direct access allowed.');

class callcenter_agents_Controller extends Bluebox_Controller
{
    // This is required and should be the name of your primary Model
    protected $baseModel = 'callcenter_agent';

    public function __construct()
    {
        parent::__construct();
        stylesheet::add('callcenter_agents', 50);
    }

    public function index()
    {
        $this->template->content = new View('generic/grid');
        // Setup the base grid object
        $this->grid = jgrid::grid($this->baseModel, array(
                'caption' => 'Agents'
            )
        );
        // Add the base model columns to the grid
        $this->grid->add('cca_id', 'ID', array(
                'hidden' => true,
                'key' => true
            )
        );
        $this->grid->add('cca_displayname', 'Display Name');
        $this->grid->add('cca_loginid', 'Login ID');
        $this->grid->add('agentLocation/name', 'Location', array(
                'width' => '150',
                'search' => false,
            )
        );
        // Add the actions to the grid
        $this->grid->addAction('callcenter_agents/edit', 'Edit', array(
                'arguments' => 'cca_id'
            )
        );
        $this->grid->addAction('callcenter_agents/delete', 'Delete', array(
                'arguments' => 'cca_id'
            )
        );
        plugins::views($this);
        // Produce a grid in the view
        $this->view->grid = $this->grid->produce();

		$currentuser = users::getCurrentUser();
		if ($currentuser['user_type'] == User::TYPE_SYSTEM_ADMIN)
		{

			navigation::getNavTree();
			$submenu = navigation::getCurrentSubMenu();
			navigation::addSubmenuOption('callcenter_agents', 'Re-Install Features', 'callcenter_agents/installFeatures');
		}
    }

	function installFeatures()
    {
		CallCenterManager::installFeatures();
		message::set('Default Features Installed', 'alert');
        url::redirect(Router_Core::$controller);
	}
}
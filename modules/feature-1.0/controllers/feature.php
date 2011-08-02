<?php defined('SYSPATH') or die('No direct access allowed.');

class feature_Controller extends Bluebox_Controller
{
	protected $baseModel = 'Feature';

	function index()
	{
		$this->template->content = new View('generic/grid');

        // Setup the base grid object
        $this->grid = jgrid::grid($this->baseModel, array(
                'caption' => 'Features'
            )
        );

        // Add the base model columns to the grid
        $this->grid->add('ftr_id', 'ID', array(
                'hidden' => true,
                'key' => true
            )
        );
        $this->grid->add('ftr_display_name', 'Name');
        $this->grid->add('ftr_desc', 'Description');

        // Add the actions to the grid
        $this->grid->addAction('feature/edit', 'Edit', array(
                'arguments' => 'ftr_id'
            )
        );
        $this->grid->addAction('feature/delete', 'Delete', array(
                'arguments' => 'ftr_id'
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
			navigation::addSubmenuOption('feature', 'Re-Install Default Features', 'feature/installDefaultFeatures');
		}
	}

    protected function deleteOnSubmit($base)
    {
		$currentuser = users::getCurrentUser();
		if ($currentuser['user_type'] < $this->feature['ftr_edit_user_type'])
		{
			message::set('You do not have rights to delete this feature', 'alert');
			url::redirect(Router_Core::$controller);
		}
		parent::deleteOnSubmit($base);
	}

	public function installDefaultFeatures()
    {
		FeatureManager::installDefaultFeatures();
		message::set('Default Features Installed', 'alert');
        url::redirect(Router_Core::$controller);
	}

	public function getFeatureForm()
	{
		$feature = Input::instance()->post('Feature');

		if ($feature['ftr_package_id'] != 0)
		{
			$packageobj = Doctrine::getTable('package')->find($feature['ftr_package_id']);
			if ($packageobj)
				try {
					if (!$package = Package_Catalog::getInstalledPackage($packageobj->name))
					{
						echo 'Package not ' . $packageobj->name . ' found.';
						exit();
					}

					$formfile = $package['directory'] . '/views/' . $packageobj->name . '/' . $feature['ftr_name'] . '.php';
					kohana::Log('debug', 'Looking for view ' . $formfile);
					if (!file_exists($formfile))
					{
						kohana::Log('debug', 'View file not found.');
						exit();
					} else {
						kohana::Log('debug', 'View file found.');

						$featureFormView = new View($packageobj->name . '/' . $feature['ftr_name']);
						$featureFormView->set_global('Feature', (object) $feature);
						echo $featureFormView->render(TRUE);
					}
				} catch (Package_Catalog_Exception $e) {
					echo 'Package not ' . $packageobj->name . ' found.';
				}
		}
		exit();
	}

	public function getFeatureNumberOptionsForm()
	{
		$number = Input::instance()->post('number');
		if (isset($number['foreign_id']))
		{
			$featureobj = Doctrine::getTable('Feature')->find($number['foreign_id']);
			if (!$featureobj)
				throw new featureException('Feature ' . $featureid . ' not found.');
			$packageobj = Doctrine::getTable('package')->find($featureobj->ftr_package_id);
			if (!$packageobj)
				throw new featureException('Package ' . $featureobj->ftr_package_id . ' not found.');
			if (!$package = Package_Catalog::getInstalledPackage($packageobj->name))
				throw new featureException('Package ' . $packageob->name . ' not found.');
			$viewfile = $package['directory'] . 'views/' . $packageobj->name . '/' . $featureobj->ftr_name . 'Options.php';
			if (file_exists($viewfile))
			{
				$featureoptview = new view($pacjageobj->name . '/' . $featureobj->ftr_name . 'Options');
				$featureoptview->set_global('number', $number);
				echo $featureoptview->render(TRUE);
			}
		}
		exit();
	}
}
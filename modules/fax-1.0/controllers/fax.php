<?php defined('SYSPATH') or die('No direct access allowed.');
class fax_Controller extends Bluebox_Controller
{
    // This is required and should be the name of your primary Model
    protected $baseModel = 'FaxProfile';
    protected $authBypass = array('sendfax');

    public function __construct()
    {
        parent::__construct();
        stylesheet::add('fax', 50);
    }
    
    public function index()
	{
        $this->template->content = new View('generic/grid');
				
        // Setup the base grid object
        $this->grid = jgrid::grid('FaxProfile', array(
                'caption' => 'Fax Profiles'
            )
        )
        ->add('fxp_id', 'ID', array(
                'hidden' => true,
                'key' => true
            )
        )
        ->add('fxp_name', 'Name')
        ->add('fxp_default', 'Default')
        ->addAction('fax/edit', 'Edit', array(
                'arguments' => 'fxp_id'
            )
        )
        ->addAction('fax/delete', 'Delete', array(
                'arguments' => 'fxp_id'
            )
        );

        // Let plugins populate the grid as well
        plugins::views($this);

        // Produce a grid in the view
        $this->view->grid = $this->grid->produce();
    }
    
    public function sendfax($destination, $filename)
    {
		try {
			$faxprofile = Doctrine::getTable('InFax')->findOneBy('fxp_default', true);
			
			if (!$faxprofile)
				throw new faxException('No default fax profile found!!!');
			
			$eslCon = EslManager::getInstance()->getESL();
			$responseobj = $eslCon->sendRecv('originate {fax_verbose=true}' . $destination . ' &txfax(' . $faxprofile->fxp_spool_dir . $filename . ')');
			$responsestr = $responseobj->getBody();
			if (trim($responsestr) == '0 total.'  || trim($responsestr) == '+OK')
					echo 'Fax sent successfully to ' . $destination;
			if (substr(trim($responsestr), 0, 4) == '-ERR')
					throw new faxException($responsestr);
			
		} catch (Exception $e) {
			echo 'Error sending fax: ' . $e->getMessage();			
		}
		
		exit();	
    }
    
    public function outbound()
    {
        $this->template->content = new View('fax/outbound');
    }
    
    public function getDispositionForm()
	{
		$faxprof = Input::instance()->post('faxprofile');
		$faxdisp = Doctrine::getTable('FaxDisposition')->find($faxprof['fxp_fxd_id']);

		if ($faxdisp)
		{
			$packageobj = Doctrine::getTable('package')->find($faxdisp['fxd_package_id']);
			if ($packageobj)
				try {
					if (!$package = Package_Catalog::getInstalledPackage($packageobj->name))
					{
						echo 'Package not ' . $packageobj->name . ' found.';
						exit();
					}

					$formfile = $package['directory'] . '/views/' . $packageobj->name . '/' . $faxdisp['fxd_name'] . '.php';
					kohana::Log('debug', 'Looking for view ' . $formfile);
					if (file_exists($formfile))
					{
						kohana::Log('debug', 'View file found.');
						$faxprofobj = Doctrine::getTable('FaxProfile')->find($faxprof['fxp_id']);
						
						$featureFormView = new View($packageobj->name . '/' . $faxdisp['fxd_name']);
						$featureFormView->set_global('faxprofile', $faxprofobj);
						echo $featureFormView->render(TRUE);
					} else {
						kohana::Log('debug', 'View file not found.');
					}
				} catch (Package_Catalog_Exception $e) {
					echo 'Package not ' . $packageobj->name . ' found.';
				}
		}
		exit();
	}
    
}
?>
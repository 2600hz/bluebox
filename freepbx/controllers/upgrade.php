<?php
class Upgrade_Controller extends FreePbx_Controller
{
	private $upgradeMessage;
	protected $noAuth = array('index');
	private $path;
	
	public function index()
	{
		$this->path = $path = DOCROOT . 'freepbx/migrations';
		
		$this->view->versions = $this->scanMigrations();
		$this->view->currentVersion = $this->getCurrentVersion();
		
		
		if($this->input->post())
		{
			$version = (int)$this->input->post('version');
			$this->migrate($version);
		}
		
	}

	private function migrate($version)
	{
		if($version == 0)
		{
			$version = NULL;
		}
		
		$migration = new Doctrine_Migration($this->path);	
		try
		{
			$migration->migrate($version);	
		} catch(Exception $e)
		{
			message::set($e->getMessage());
		}
	}
	
	private function getCurrentVersion()
	{
		$migration = new Doctrine_Migration($this->path);	
		return $migration->getCurrentVersion();
	}
	
	private function scanMigrations()
	{
		$versions = array();
		
		$d = dir($this->path);

		$versions[0] = 'Latest'; 

		while (false !== ($entry = $d->read())) 
		{
			if($entry != '.' && $entry != '..')
			{
				$info = pathinfo($entry);
				$prefix = current(explode('_', $entry));
				
				$versions[$prefix] =  basename($entry,'.'.$info['extension']);
				
			}
		}
		$d->close();
		return $versions;
	}
}

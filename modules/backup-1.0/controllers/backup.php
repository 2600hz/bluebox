<?php

defined('SYSPATH') or die('No direct access allowed.');

class Backup_Controller extends Bluebox_Controller {

	protected $baseModel = 'Backup';

	public function index() {
		$data['title'] = "Import/Export";
		$data['list_options'] = Backup::listBackup();
		$this->template->content = new View('backup/update', $data);
	}

	public function export() {
		$result = Backup::export();
		$data['state'] = 1;
		$this->template->content = new View('backup/result', $data);
	}

	public function import() {
		$data['state'] = Backup::import($_REQUEST['file']);
		$this->template->content = new View('backup/result', $data);
	}

}
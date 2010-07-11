<?php defined('SYSPATH') or die('No direct access allowed.');
/*
 * Bluebox Modular Telephony Software Library / Application
 *
 * Module:
 * 
 * The contents of this file are subject to the Mozilla Public License
 * Version 1.1 (the "License"); you may not use this file except in
 * compliance with the License. You may obtain a copy of the License at
 * http://www.mozilla.org/MPL/
 * 
 * Software distributed under the License is distributed on an "AS IS"
 * basis, WITHOUT WARRANTY OF ANY KIND, either express or implied. See the
 * License for the specific language governing rights and limitations
 * under the License.
 * 
 * The Initial Developer of the Original Code is Michael Phillips <michael.j.phillips@gmail.com>.
 * 
 * Portions created by the Initial Developer are Copyright (C)
 * the Initial Developer. All Rights Reserved.
 *  
 * Contributor(s): 
 *  
 *
 */
/**
 * rosetta.php - Sofia Viewer Module
 *
 * @author Michael Phillips <michael.j.phillips@gmail.com>
 * @license MPL
 * @package Bluebox
 * @subpackage Rosetta
 */
class Rosetta_Controller extends Bluebox_Controller
{
	protected $noAuth = array('index');
	public function index()
	{
		
		//Doctrine::createTablesFromArray(array('Rosetta'));
		$r = RosettaManager::instance();
		$this->view->output = $r->setTo('ru')->translate('Bluebox now supports Freeswitch!');
		$this->view->moreoutput = $r->translate('Now with more features!');
	}
	
}

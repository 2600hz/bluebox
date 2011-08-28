<?php defined('SYSPATH') or die('No direct access allowed.');
/*
 * FreePBX Modular Telephony Software Library / Application
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
 * Michael Phillips
 *
 *
*/

/**
 * @author Your Name <your@email.org>
 * @license Your License
 * @package _Skeleton
 */
class Xmlcdr_Controller extends Bluebox_Controller {
    protected $authBypass = array('service');
    protected $baseModel = 'Xmlcdr';
	protected $summaryfields = array(
	);
    protected $detailfields = array(
                'Caller Name' => 'caller_id_name',
                'Caller Number' => 'caller_id_number',
                'Direction' => 'direction',
                'Desintation Number' => 'destination_number',
                'User Name' => 'user_name',
                'Context' => 'context',
                'Start' => 'start_stamp',
                'Answer' => 'answer_stamp',
                'End' => 'end_stamp',
                'Duration' => 'duration',
                'Billable Seconds' => 'billsec',
                'Hangup Cause' => 'hangup_cause',
                'UUID' => 'uuid',
                'B-Leg UUID' =>  'bleg_uuid',
                'Account Code' => 'accountcode',
                'Domain Name' => 'domain_name',
                'User Context' => 'user_context',
                'Read Codec' => 'read_codec',
                'Write Codec' => 'write_codec',
                'Dialed Domain' => 'dialed_domain',
                'Dialed User' => 'dialed_user'
    );
  
    public function  index($start_date = 'null', $end_date = 'null') {
    	// Download a CDR
        // Setup the base grid object
        if ($this->submitted())
        {
        	$start_date = $this->input->post('startdate');
        	$end_date = $this->input->post('enddate');
        	$exptype = $this->input->post('exporttype');
        	
        	$q = Doctrine_query::create()
        	->select('*')
        	->from('Xmlcdr');
        	
        	if ($start_date !== '')
        		$q->andWhere('start_stamp >= ?', $start_date . ' 00:00:00');
        	
        	if ($end_date !== '')
        		$q->andWhere('end_stamp <= ?', $end_date . ' 23:59:59');
        		
        	$exprecs = $q->execute();
        	
        	$this->doexport($exprecs, $exptype);
        }
        
        $this->grid = jgrid::grid($this->baseModel, array(
                'gridName' => 'downloadrange',
                'caption' => 'Caller Detail Records',
                'sortorder' => 'desc'
            )
        )
        // Add the base model columns to the grid
        ->add('xml_cdr_id', 'ID', array('hidden' => true, 'key' => true))
        ->add('direction', 'Direction')
        ->add('caller_id_name', 'Caller Name')
        ->add('caller_id_number', 'Caller Number')
        ->add('destination_number', 'Destination', array('callback' => array($this, 'formatNumber')))
        ->add('duration', 'Duration')
        ->add('hangup_cause', 'Call End Cause')
        ->addAction('xmlcdr/details', 'Details', array('arguments' => 'xml_cdr_id', 'attributes' => array('class' => 'qtipAjaxForm')
                )
        );
        
        // Only display records from the domains associated with the account
        $domainlist = Doctrine_Query::create()
           	->select('domain')
        	->from('Location')
        	->fetchArray();
		$inlist = array();
		foreach ($domainlist as $domain)
		{
        	$inlist[] = $domain['domain'];
		}
        $this->grid->whereIn('domain_name', '', $inlist);
        
        // Filter by starting and ending dates
        if($start_date !== 'null') {
            $this->grid->andWhere('start_stamp', '>=', $start_date . ' 00:00:00');
        }

        if($end_date !== 'null') {
          	$this->grid->andWhere('end_stamp', '<=', $end_date . ' 23:59:59');
        }

        // Let plugins populate the grid as well
        plugins::views($this);

        // Produce a grid in the view
        $this->view->grid = $this->grid->produce();

    }

    public function details($xml_cdr_id) {
    	if ($this->submitted())
    		$this->exitQtipAjaxForm();
    	
        $xmlcdr = Doctrine::getTable('Xmlcdr')->findOneBy('xml_cdr_id', $xml_cdr_id);
      	$this->xmlcdr = $xmlcdr;
        $this->view->detailfields = $this->detailfields;
        $this->view->details = $xmlcdr;
        // Execute plugin hooks here, after we've loaded the core data sets
        Event::run('bluebox.create_view', $this->view);
        plugins::views($this);
    }

    
    public function downloadrec($recid, $exptype)
    {
    	$exprec = Doctrine::getTable('Xmlcdr')->findOneBy('xml_cdr_id', $recid);
    	$this->doexport(array($exprec), $exptype);
    }
    
	public function doexport($exprecs, $exptype)
	{
		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Content-Type: application/force-download");
		header("Content-Type: application/octet-stream");
		header("Content-Type: application/download");
		header('Content-type: text/' . $exptype);
		header("Content-Disposition: attachment;filename=cdr." . $exptype);
		
		if ($exptype == 'csv')
		{
			//The fields in each CDR record vary based on the dialplan, call direction, etc.,
			//so to export the calls successfully, we need to first go through all of the cdr
			//records and make sure that we have a list of all of the possible fields.
			//Then, we must step through the cdr records again, and build an array that lines up
			//with the field list before writing it out.
			
			//Build the list of fields
			$fieldlist = array();
			$flatrecs = array();
			foreach ($exprecs as $exprec)
			{
				$flatarr = XmlcdrManager::buildcdrarray($exprec->cdrrec);
				$fieldlist = array_merge($fieldlist, $flatarr);
				$flatrecs[] = $flatarr;
			}
			$fieldlist = array_keys($fieldlist);
			asort($fieldlist);

			//write out the field list
			echo implode("\t", $fieldlist) . "\n";

			//step through each flattened record and write out the fields
			foreach ($flatrecs as $flatrec)
			{
				//step through the field list and write out the value
				foreach ($fieldlist as $curfield)
				{
					if (isset($flatrec[$curfield]))
						echo $flatrec[$curfield];
					echo "\t";
				}
				echo "\n";
			}
		} else {
			ob_end_clean();
			echo '<cdrs>' . "\n";
			foreach ($exprecs as $exprec)
			{
				echo substr($exprec['cdrrec'], (strpos($exprec['cdrrec'], "\n")+1));
			}
			echo '</cdrs>' . "\n";
		}
		ob_end_flush();
		exit();
	}
    
    public function service($key = NULL) {
        $this->auto_render = FALSE;
        
        if($this->input->post()) {
            $xml = $this->input->post('cdr');
            XmlcdrManager::addXMLCDR($xml);
        } else {
            $error =  "NO CDR RECORD FOUND IN POST HEADER";
            echo $error;
            Kohana::log('error', $error);
        }
    }


    public function formatDate($date) {
        $dt = new DateTime($date);
        return $dt->format('m/d/Y h:i:s a');
    }


    public function formatDuration ($sec, $padHours = false) {

        $hms = "";

        // there are 3600 seconds in an hour, so if we
        // divide total seconds by 3600 and throw away
        // the remainder, we've got the number of hours
        $hours = intval(intval($sec) / 3600);

        // add to $hms, with a leading 0 if asked for
        $hms .= ($padHours)
                ? str_pad($hours, 2, "0", STR_PAD_LEFT). ':'
                : $hours. ':';

        // dividing the total seconds by 60 will give us
        // the number of minutes, but we're interested in
        // minutes past the hour: to get that, we need to
        // divide by 60 again and keep the remainder
        $minutes = intval(($sec / 60) % 60);

        // then add to $hms (with a leading 0 if needed)
        $hms .= str_pad($minutes, 2, "0", STR_PAD_LEFT). ':';

        // seconds are simple - just divide the total
        // seconds by 60 and keep the remainder
        $seconds = intval($sec % 60);

        // add to $hms, again with a leading 0 if needed
        $hms .= str_pad($seconds, 2, "0", STR_PAD_LEFT);

        return $hms;
    }

    public function formatNumber($number)
    {
        return numbermanager::formatNumber($number);
    }

}

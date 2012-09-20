<?php defined('SYSPATH') or die('No direct access allowed.');
class FreeSwitch_faxprofile_Driver extends FreeSwitch_Base_Driver
{
	public static function getDialstring($faxProfile)
	{
		FaxDispositionManager::getDialstring($faxProfile);
	}
	   	
    public static function set($faxprofile) 
    {
    	if ($faxprofile instanceof FaxProfile)
    	{
			if ($faxprofile->fxp_default == true)
			{
				$xml = Telephony::getDriver()->xml;
				$root = '//document/section[@name="configuration"]/configuration[@name="fax.conf"][@description="SpanDSP Fax Config"]/settings';
				$xml->setXmlRoot($root);
	
				if (isset($faxprofile->fxp_ecm_mode) && !empty($faxprofile->fxp_ecm_mode) && $faxprofile->fxp_ecm_mode != '')
					switch ($faxprofile->fxp_ecm_mode)
					{
						case 1:
							$xml->update('/param[@name="fax_disable_ecm"]{@value="false"}');
							$xml->deleteNode('/param[@name="fax_use_ecm"]');
							break;
						case 2:
							$xml->update('/param[@name="fax_disable_ecm"]{@value="true"}');
							$xml->deleteNode('/param[@name="fax_use_ecm"]');
							break;
						case 3:
							$xml->update('/param[@name="fax_disable_ecm"]{@value="false"}');
							$xml->update('/param[@name="fax_use_ecm"]{@value="true"}');
							break;
						default:
							$xml->deleteNode('/param[@name="fax_disable_ecm"]');
							$xml->deleteNode('/param[@name="fax_use_ecm"]');
							break;
					}		
				else
				{
					$xml->deleteNode('/param[@name="fax_disable_ecm"]');
					$xml->deleteNode('/param[@name="fax_use_ecm"]');
				}
					
				if (isset($faxprofile->fxp_t38_mode) && !empty($faxprofile->fxp_t38_mode) && $faxprofile->fxp_t38_mode != '')
					switch ($faxprofile->fxp_t38_mode)
					{
						case 1:
							$xml->update('/param[@name="fax_enable_t38"]{@value="true"}');
							$xml->deleteNode('/param[@name="fax_enable_t38_request"]');
							$xml->deleteNode('/param[@name="fax_enable_t38_insist"]');
							break;
						case 2:
							$xml->update('/param[@name="fax_enable_t38"]{@value="true"}');
							$xml->update('/param[@name="fax_enable_t38_request"]{@value="true"}');
							$xml->deleteNode('/param[@name="fax_enable_t38_insist"]');
							break;
						case 3:
							$xml->update('/param[@name="fax_enable_t38"]{@value="false"}');
							$xml->update('/param[@name="fax_enable_t38_request"]{@value="true"}');
							$xml->update('/param[@name="fax_enable_t38_insist"]{@value="true"}');
							break;
						default:
							$xml->deleteNode('/param[@name="fax_enable_t38"]');
							$xml->deleteNode('/param[@name="fax_enable_t38_request"]');
							$xml->deleteNode('/param[@name="fax_enable_t38_insist"]');
							break;
					}		
				else
				{
					$xml->deleteNode('/param[@name="fax_enable_t38"]');
					$xml->deleteNode('/param[@name="fax_enable_t38_request"]');
					$xml->deleteNode('/param[@name="fax_enable_t38_insist"]');
				}
				
				if (isset($faxprofile->fxp_v17_mode) && !empty($faxprofile->fxp_v17_mode) && $faxprofile->fxp_v17_mode != '')
					switch ($faxprofile->fxp_v17_mode)
					{
						case 1:
							$xml->update('/param[@name="fax_disable_v17"]{@value="false"}');
							$xml->update('/param[@name="fax_v17_disabled"]{@value="false"}');
							break;
						case 2:
							$xml->update('/param[@name="fax_disable_v17"]{@value="false"}');
							$xml->update('/param[@name="fax_v17_disabled"]{@value="true"}');
							break;
						case 3:
							$xml->update('/param[@name="fax_disable_v17"]{@value="true"}');
							$xml->update('/param[@name="fax_v17_disabled"]{@value="true"}');
							break;
						default:
							$xml->deleteNode('/param[@name="fax_disable_v17"]');
							$xml->deleteNode('/param[@name="fax_v17_disabled"]');
							break;
					}		
				else
				{
					$xml->deleteNode('/param[@name="fax_disable_v17"]');
					$xml->deleteNode('/param[@name="fax_v17_disabled"]');
				}
				
				if (isset($faxprofile->fxp_force_caller) && !empty($faxprofile->fxp_force_caller) && $faxprofile->fxp_force_caller != '')
					switch ($faxprofile->fxp_force_caller)
					{
						case 1:
							$xml->update('/param[@name="fax_force_caller"]{@value="0"}');
							break;
						case 2:
							$xml->update('/param[@name="fax_force_caller"]{@value="1"}');
							break;
						default:
							$xml->deleteNode('/param[@name="fax_force_caller"]');
							break;
					}		
				else
				{
					$xml->deleteNode('/param[@name="fax_force_caller"]');
				}
				
				if (isset($faxprofile->fxp_start_page) && !empty($faxprofile->fxp_start_page) && $faxprofile->fxp_start_page != '' && $faxprofile->fxp_start_page > -1)
					$xml->update('/param[@name="fax_start_page"]{@value="' . $faxprofile->fxp_start_page . '"}');
				else
				{
					$xml->deleteNode('/param[@name="fax_start_page"]');
				}
				
				if (isset($faxprofile->fxp_end_page) && !empty($faxprofile->fxp_end_page) && $faxprofile->fxp_end_page != '' && $faxprofile->fxp_end_page > -1)
					$xml->update('/param[@name="fax_end_page"]{@value="' . $faxprofile->fxp_start_page . '"}');
				else
				{
					$xml->deleteNode('/param[@name="fax_end_page"]');
				}
				
				if (isset($faxprofile->fxp_ident) && !empty($faxprofile->fxp_ident) && $faxprofile->fxp_ident != '')
					$xml->update('/param[@name="fax_ident"]{@value="' . $faxprofile->fxp_ident . '"}');
				else
				{
					$xml->deleteNode('/param[@name="fax_ident"]');
				}
				
				if (isset($faxprofile->fxp_header) && !empty($faxprofile->fxp_header) && $faxprofile->fxp_header != '')
					$xml->update('/param[@name="fax_header"]{@value="' . $faxprofile->fxp_header . '"}');
				else
				{
					$xml->deleteNode('/param[@name="fax_header"]');
				}
				
				if (isset($faxprofile->fxp_prefix) && !empty($faxprofile->fxp_prefix) && $faxprofile->fxp_prefix != '')
					$xml->update('/param[@name="fax_prefix"]{@value="' . $faxprofile->fxp_prefix . '"}');
				else
				{
					$xml->deleteNode('/param[@name="fax_prefix"]');
				}
				
				if (isset($faxprofile->fxp_verbose) && !empty($faxprofile->fxp_verbose) && $faxprofile->fxp_verbose != '')
					switch ($faxprofile->fxp_verbose)
					{
						case 1:
							$xml->update('/param[@name="fax_verbose"]{@value="true"}');
							break;
						case 2:
							$xml->update('/param[@name="fax_verbose"]{@value="false"}');
							break;
						default:
							$xml->deleteNode('/param[@name="fax_verbose"]');
							break;
					}		
				else
				{
					$xml->deleteNode('/param[@name="fax_verbose"]');
				}
				
				if (isset($faxprofile->fxp_spool_dir) && !empty($faxprofile->fxp_spool_dir) && $faxprofile->fxp_spool_dir != '')
					$xml->update('/param[@name="spool-dir"]{@value="' . str_replace('/', '\/', '/' . $faxprofile->fxp_spool_dir) . '"}');
				else
				{
					$xml->deleteNode('/param[@name="spool-dir"]');
				}
			}
    	}
    }

    public static function delete($obj) 
    {
			$xml = Telephony::getDriver()->xml;
			$root = '//document/section[@name="configuration"]/configuration[@name="fax.conf"][@description="SpanDSP Fax Config"]';
			$xml->setXmlRoot($root);
			$xml->deleteNode('/settings');
    }

    public static function network()
	{
        FaxDispositionManager::network();
	}
	
	public static function conditioning()
	{
        FaxDispositionManager::conditioning();
	}
	
	public static function preRoute()
	{
        FaxDispositionManager::preRoute();
	}
		
	public static function postRoute()
	{
        FaxDispositionManager::postRoute();
	}
	
	public static function preAnswer()
	{
        FaxDispositionManager::preAnswer();
	}
	
	public static function postAnswer()
	{
        FaxDispositionManager::postAnswer();
	}
	
    public static function preNumber()
    {
        $xml = Telephony::getDriver()->xml;
        $number = Event::$data;
        $plugins = $number['plugins'];

        if (empty($plugins['fax']))
        {
            return;
        }

        if (empty($plugins['fax']['autodetect']) || $plugins['fax']['autodetect'] == 0)
        {
            return;
        }

        if (empty($plugins['fax']['autodetect_number']))
        {
            return;
        }
        
        FaxDispositionManager::preNumber();
        
        $transferNumbObj = Doctrine::getTable('Number')->findOneBy('number_id', $plugins['fax']['autodetect_number']);
        $xml->update('/action[@application="spandsp_start_fax_detect"]{@data="transfer ' . $transferNumbObj->number . ' 5"}');
        
        FaxDispositionManager::preNumber($transferNumbObj);
    }
        
	public static function dialplan($number)
    {
        $xml = Telephony::getDriver()->xml;
        $destination = $number['FaxProfile'];
       	
        $xml->update('/action[@application="stop_tone_detect"]');
        FaxDispositionManager::dialplan($number);      
        $xml->update('/action[@application="answer"]');
        $xml->update('/action[@application="playback"][@data="silence_stream:\/\/2000"]');
        $xml->update('/action[@application="rxfax"][@data="' .  str_replace('/', '\/', $destination->fxp_spool_dir . '${uuid}.rxfax.tif"]'));
        $xml->update('/action[@application="hangup"]');
    }
    
	public static function main()
	{
        FaxDispositionManager::main();
	}
	
	public static function postNumber()
	{
        FaxDispositionManager::postNumber();
	}
	
	public static function catchAll()
	{
        FaxDispositionManager::catchAll();
	}
	
	public static function postExecute()
	{
        FaxDispositionManager::postExecute();
	}   	
}
?>
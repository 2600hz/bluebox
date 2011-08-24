<?php defined('SYSPATH') or die('No direct access allowed.');

class EndpointManager_Controller extends Bluebox_Controller
{
    protected $baseModel = 'Endpoint';
    protected $authBypass = array('configfile');

    private function _identify_configfile($configfile,$debug) {
	$prov=$this->_getprovisioningdata();
	$matches=array();
	if ($debug) {
		print "Searching for config file $configfile\n";
	}
	
	foreach ($prov['files'] AS $index=>$possibility) {
		if ($debug) {
			print "Checking $index:\n";
			print_r($possibility);
		}
		if (preg_match('/^'.$possibility['regex'].'$/',$configfile,$args)) {
			$onematch=array("possibility"=>$possibility);
			array_shift($args);
			if (count($args)>0) {
				$onematch["args"]=array_combine($possibility['fields'],$args);
			}
			if (array_key_exists('mac',$onematch['args'])) {
				$ep=$onematch["endpoint"]=Doctrine::getTable('Endpoint')->findOneBy('mac',$onematch['args']['mac']);
				if (!is_object($ep)) {
					throw new Exception("Mac address ".$onematch["args"]["mac"]." is not set up in this system");
				}
				if (array_key_exists('phones',$prov) && array_key_exists($ep->brand,$prov['phones']) && array_key_exists($ep->model,$prov['phones'][$ep->brand])) {
					$onematch['provisioning']=$prov['phones'][$ep->brand][$ep->model];
				}
				# If this file does not belong to this make/model, skip it.
				if (($ep->brand!=$possibility['make']) || ($prov['phones'][$ep->brand][$ep->model]['family']!=$possibility['family'])) {
					if ($debug) {
						print "FAIL: Brand or Model doesn't match for endpoint with this mac address\n";
					}
					continue;
				}
			}
			# Eliminate possibilities where the string matched as "model" is not a valid model, or the model belongs to another family.
			if (array_key_exists('model',$onematch['args'])) {
				if (!array_key_exists($onematch['args']['model'],$prov['phones'][$possibility['make']])) {
					if ($debug) {
						print "FAIL: filename refers to a particular model, but this brand does not have that model\n";
					}
					continue;
				}
				if ($prov['phones'][$possibility['make']][$onematch['args']['model']]['family'] !== $possibility['family']) {
					if ($debug) {
						print "FAIL: filename refers to a particular model, but that model is in a different family\n";
					}
					continue;
				}
				# If there is a model and a mac, and the model doesnt match the model in the mac's endpoint, skip it.
				if (array_key_exists('endpoint',$onematch)) {
					if ($onematch['endpoint']->model != $onematch['args']['model']) {
						if ($debug) {
							print "FAIL: filename refers to a particular model, but phone with that mac is not that model\n";
						}
						continue;
					}
				}
				if (!array_key_exists('provisioning',$onematch)) {
					if (array_key_exists('phones',$prov) && array_key_exists($possibility['make'],$prov['phones']) && array_key_exists($onematch['args']['model'],$prov['phones'][$possibility['make']])) {
						$onematch['provisioning']=$prov['phones'][$possibility['make']][$onematch['args']['model']];
					}
				}
			}
			if ($debug) {
				print "SUCCESS\n";
			}
			array_push($matches,$onematch);
		} else {
			if ($debug) {
				print "FAIL - Regex doesn't match\n";
			}
		}
	}
	if (count($matches)==1) {
		return $matches[0];
	}
	if (count($matches)==0) {
		throw new Exception("Could not find config file $configfile");
	}
	# TODO - if there are multiple possibilities, all with the same digest, which do not require per-phone settings, we could call it a success.
	throw new Exception("Multiple possibilities for file $configfile");
    }

    public function configfile ()
    {
	$file=implode(DIRECTORY_SEPARATOR,func_get_args());
	$configfileinfo=$this->_identify_configfile($file,array_key_exists('debug',$_REQUEST));

	if (!array_key_exists('endpoint',$configfileinfo)) {
		# TODO - if there is no endpoint, there may still be enough data to serve the config. We know the filename, model, family, and other info.
		throw new Exception("Could not work out which endpoint the file belongs to");
	}
        $class = "endpoint_" . $configfileinfo["possibility"]["make"] . "_" . $configfileinfo["provisioning"]["family"] . '_phone';
	$provisioner_lib = new $class();
	foreach (array('mac','model') AS $attr) {
		$provisioner_lib->$attr=$configfileinfo['endpoint'][$attr];
	}
        $provisioner_lib->timezone = date_offset_get(new DateTime);

	$lines=array();
	foreach (unserialize($configfileinfo['endpoint']->lines) AS $index=>$line) {
		if (!empty($line['sip'])) {
			$device=Doctrine::getTable('Device')->find($line['sip']);
			$provisioner_lib->lines[$index]=array(
				"line"=>$index,
				"ext"=>$device['plugins']['sip']['username'],
				'displayname' => $device["plugins"]["sip"]["username"], // TODO - get this somewhere?!?!?
				'secret' => $device['plugins']['sip']['password'],
				'subscribe_mwi' => 1, // Todo - map this properly to voicemail boxes
			);
        		$dns = $device['User']['Location']['domain'];
		}
		
	}
	$provisioner_lib->server[1]['ip'] = $dns;
	$provisioner_lib->server[1]['port'] = 5060;
        $provisioner_lib->root_dir = dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . "libraries" . DIRECTORY_SEPARATOR;
        $provisioner_lib->processor_info = 'Endpoint Manager 1.2 for Blue.Box';
	$data = $provisioner_lib->parse_config_file($provisioner_lib->open_config_file($configfileinfo['possibility']['file']), FALSE);
	print $data;
	
	exit;
    }

   public function index() 
   {
	$this->template->content = new View('generic/grid');
	// Setup the base grid object
	$grid = jgrid::grid($this->baseModel, array(
                'caption' => 'Endpoints'
            )
        );
	$grid->add('name','Name');
	$grid->add('mac','MAC',array('key'=>true));
	$grid->add('brand','Brand');
	$grid->add('model','Model');

        $grid->addAction('endpointmanager/edit', 'Edit', array(
                'arguments' => 'endpoint_id',
                'width' => '120'
            )
        );
        $grid->addAction('endpointmanager/delete', 'Delete', array(
                'arguments' => 'endpoint_id',
                'width' => '20'
            )
        );


        // Let plugins populate the grid as well
        $this->grid = $grid;
        plugins::views($this);

        // Produce a grid in the view
        $this->view->grid = $this->grid->produce();

   }

   private function _xmlread($xml,$lists=array()) 
   {
	if (!is_object($xml)) {
		if (!is_array($lists)) {
			$lists=array($lists);
		}
		$filename=$xml;
		$xml=new XMLReader();
		$xml->open($filename);
		$assoc=$this->_xmlread($xml,$lists);
		$xml->close();
		return $assoc;
	}
	$tree = null;
	while($xml->read()) {
	        if($xml->nodeType == XMLReader::END_ELEMENT) {
			return $tree;
		} else if($xml->nodeType == XMLReader::ELEMENT) {
			$node = array();
			$tag=$xml->name;

			if(!$xml->isEmptyElement) {
				$childs = $this->_xmlread($xml, $lists);
				if (in_array($tag,$lists)) {
					$tree[$tag][] = $childs;
				} else {
					$tree[$tag] = $childs;
				}
			}

		} else if($xml->nodeType == XMLReader::TEXT) {
			$node = array();
			$node = $xml->value;
			$tree = $node;
		}
	}
	return $tree;
   }

   private function _getprovisioningdata() {
	# Note: $this->privateprovdata is PRIVATE to this function - do not use it directly. If you want the data, call this function.
	# TODO: bluebox style caching, building the data if neccesary.
	if (property_exists($this,'privateprovdata')) {
		return $this->privateprovdata;
	}
	$cache=Cache::instance();
	if ($data=$cache->get('endpointmanager->provisioningdata')) {
		$this->privateprovdata=$data;
		return $data;
	}
	$data =array('oui'=>array(),'phones'=>array(),"files"=>array());
	$xmlbase=dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'libraries' . DIRECTORY_SEPARATOR . 'endpoint' . DIRECTORY_SEPARATOR;

	$masterxml=$this->_xmlread($xmlbase."master.xml",array('brands'));
	foreach ($masterxml['data']['brands'] AS $brand) {
		$data['phones'][$brand["directory"]]=array();
		$brandxml=$this->_xmlread("$xmlbase$brand[directory]/brand_data.xml",array('family','oui_list'));
		foreach ($brandxml['data']['brands']['oui_list'] AS $oui) {
			$data['oui'][$oui['oui']]=$brand["directory"];
		}
		foreach ($brandxml['data']['brands']['family_list']['family'] AS $family) {
			$familyxml=$this->_xmlread("$xmlbase$brand[directory]/$family[directory]/family_data.xml",array("model_list","files"));
			$seen=array();
			foreach (explode(",",$familyxml['data']['configuration_files']) AS $conf_file) {
				preg_match_all('|\$([a-z]+)|',$conf_file,$fields);
				# Note - this is done in two steps, to allow things to $model to be replaced with a wildcard
				$munged_conf=str_replace('\\','\\\\',$conf_file);
				$munged_conf=str_replace(array('/',".",'$'),array('\/','\.','\$'),$munged_conf);
				$munged_conf=str_replace(array('\\$model','\\$ext'),array('(.+)','(\d+)'),$munged_conf);
				$contents=file_get_contents("$xmlbase$brand[directory]/$family[directory]/$conf_file");
				$parse=(strpos($contents,'{')!==FALSE);
				$digest=md5($parse);
				if (strpos($munged_conf,'\$mac')!==FALSE) {
					foreach ($brandxml['data']['brands']['oui_list'] AS $oui) {
						$data['files'][]=array(
							"regex"=>str_replace('\$mac',"(".$oui['oui']."[\da-f]{6})",$munged_conf),
							"fields"=>$fields[1],
							"makename"=>$brand["name"],
							"make"=>$brand["directory"],
							"familyname"=>$family["name"],
							"family"=>$family['directory'],
							"file"=>$conf_file,
							"path"=>"$xmlbase$brand[directory]/$family[directory]/",
							"parse"=>$parse,
							"digest"=>$digest,
						);
					}
				} else {
					$data['files'][]=array(
						"regex"=>$munged_conf,
						"fields"=>$fields[1],
						"makename"=>$brand["name"],
						"make"=>$brand["directory"],
						"familyname"=>$family["name"],
						"family"=>$family['directory'],
						"file"=>"$xmlbase$brand[directory]/$family[directory]/$conf_file",
						"parse"=>$parse,
						"digest"=>$digest,
					);
				}
			}
			foreach ($familyxml['data']['model_list'] AS $model) {
				foreach ($model['template_data']['files'] AS &$filename) {
					$filename="$xmlbase$brand[directory]/$family[directory]/$filename";
				}
				$data['phones'][$brand["directory"]][$model["model"]]=array(
					"lines"=>$model["lines"],
					"makename"=>$brand["name"],
					"make"=>$brand["directory"],
					"familyname"=>$family["name"],
					"family"=>$family['directory'],
					"path"=>"$xmlbase$brand[directory]/$family[directory]/",
					"templates"=>$model['template_data']['files'],
				);
			}
		}
	}
	$cache->set('endpointmanager->provisioningdata',$data,NULL,3600);
	return $data;
   }

   protected function prepareUpdateView($baseModel = NULL) {
	parent::prepareUpdateView($baseModel);
	$brandandmodel=$this->endpoint['brand']."|".$this->endpoint['model'];
	$prov=$this->_getprovisioningdata();
	# Note: this is not endpoint[brandandmodel], because it gets split into two fields in pre_save.
	$select="<select name='brandandmodel' class='endpoint_brandandmodel_selector'>\n";
	foreach ($prov['phones'] AS $brand=>$branddata) {
		$modeldata=array_values($branddata);
		$select.="\t<optgroup label='".$modeldata[0]['makename']."'>\n";
		foreach ($branddata AS $model=>$modeldata) {
			if ($brandandmodel=="$brand|$model") {
				$select.="\t\t<option selected value='$brand|$model'>$modeldata[makename] $model</option>\n";
			} else {
				$select.="\t\t<option value='$brand|$model'>$modeldata[makename] $model</option>\n";
			}
		}
		$select.="\t</optgroup>\n";
	}
	$select.="</select>\n";
	$this->view->brandandmodelselect=$select;
	$linedata=unserialize($this->endpoint['lines']);

	$brand=$this->endpoint['brand'];
	$model=$this->endpoint['model'];
	if (isset($brand) && isset($model)) {
		$this->view->models=$prov['phones'][$brand][$model];
		$deviceSelect=array();
		foreach (Doctrine::getTable("Device")->findAll(Doctrine::HYDRATE_ARRAY) AS $device) {
			for ($line=1; $line<=$this->view->models['lines']; $line++) {
				if (!array_key_exists($line,$deviceSelect)) {
					$deviceSelect[$line]="";
				}
				if (array_key_exists($line,$linedata) && ($device['device_id']==$linedata[$line]['sip'])) {
					$deviceSelect[$line].="\n\t<option selected value='$device[device_id]'>$device[name]</option>";
				} else {
					$deviceSelect[$line].="\n\t<option value='$device[device_id]'>$device[name]</option>";
				}
			}
		}
		$this->view->deviceSelect=$deviceSelect;
	} else {
		$this->view->models=null;
	}
	$this->view->oui=$prov['oui'];
   }
   protected function pre_save(&$object) {
	$mac=strtolower(str_replace(array(' ',':','_','\\','/'),array(),$object['mac']));
	if (strlen($mac)!=12) {
		throw new Exception("Invalid mac address - it should be 12 characters long, optionally with colons.");
	}
	if (preg_match('/^[0-9a-f]{12}$/',$mac)!==1) {
		throw new Exception("Invalid mac address - it should contain only digits (0-9), letters a-f, and optionally colons.");
	}
	$object['mac']=$mac;
	$brandandmodel=explode("|",$_POST['brandandmodel']);
	$object['brand']=$brandandmodel[0];
	$object['model']=$brandandmodel[1];
	if (array_key_exists('lines',$_POST)) {
		$object['lines']=serialize($_POST['lines']);;
	}
	parent::pre_save($object);

   }

}

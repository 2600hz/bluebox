<?php defined('SYSPATH') or die('No direct access allowed.');

class EndpointManager_Controller extends Bluebox_Controller
{
    protected $baseModel = 'Endpoint';
    protected $authBypass = array('config');

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

    public function config ()
    {
	$file=implode(DIRECTORY_SEPARATOR,func_get_args());
	$debug=array_key_exists('debug',$_REQUEST);
	$configfileinfo=$this->_identify_configfile($file,$debug);

	if (!array_key_exists('endpoint',$configfileinfo)) {
		# TODO - if there is no endpoint, there may still be enough data to serve the config. We know the filename, model, family, and other info.
		throw new Exception("Could not work out which endpoint the file belongs to");
	}
        $class = "endpoint_" . $configfileinfo["possibility"]["make"] . "_" . $configfileinfo["provisioning"]["family"] . '_phone';
	$provisioner_lib = new $class();
	foreach (array('mac','model') AS $attr) {
		$provisioner_lib->$attr=$configfileinfo['endpoint'][$attr];
	}
	$defaults = Doctrine::getTable('package')->findOneby('name','endpointmanager');
	if (isset($defaults['registry']['defaults'])) {
		$defaults=$defaults['registry']['defaults'];
	} else {
		$defaults=array();
	}
	if (isset($defaults['global']['timezone'])) {
		$tz=$defaults['global']['timezone'];
	} else {
		$tz=date_default_timezone_get();
	}
	$provisioner_lib->DateTimeZone=new DateTimeZone($tz);
	$provisioner_lib->timezone=$provisioner_lib->DateTimeZone->getOffset(new DateTime());

	$lineinfo=unserialize($configfileinfo['endpoint']->lines);
	$lines=array();
	for ($index=1; $index<=$configfileinfo['provisioning']['lines']; $index++) {
		if ((!array_key_exists($index,$lineinfo)) or (empty($lineinfo[$index]['sip']))) {
			$provisioner_lib->lines[$index]=array();
		} else {
			$device=Doctrine::getTable('Device')->find($lineinfo[$index]['sip']);
			$provisioner_lib->lines[$index]=array(
				"line"=>$index,
				"ext"=>$device['plugins']['sip']['username'],
				'displayname' => $device["plugins"]["sip"]["username"], // TODO - get this somewhere?!?!?
				'secret' => $device['plugins']['sip']['password'],
				'subscribe_mwi' => 1, // Todo - map this properly to voicemail boxes
				'user_host'=>$device['User']['Location']['domain'],
			);
        		$dns = $device['User']['Location']['domain'];
		}
		
	}
	$provisioner_lib->provisioning_type='http';
	# Note: slashes are forward slashes in windows too, because this is in web-space.
	$provisioner_lib->provisioning_path=Kohana::config('core.site_domain').Kohana::config('core.index_page').'/endpointmanager/config';
	$provisioner_lib->server[1]['ip'] = $dns;
	$provisioner_lib->server[1]['port'] = 5060;
        $provisioner_lib->root_dir = dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . "libraries" . DIRECTORY_SEPARATOR;
        $provisioner_lib->processor_info = 'Endpoint Manager 1.2 for Blue.Box';
	$provisioner_lib->options=array();
	foreach ($configfileinfo['provisioning']['templates'] AS $templatedata) {
		foreach ($templatedata['subcategory'] AS $subcat) {
			foreach ($subcat['item'] AS $item) {
				if (in_array($item['type'],array('break','group'))) {
					continue; 
				} elseif (in_array($item['type'],array('loop_line_options'))) {
					foreach ($item['data']['item'] AS $subitem) {
						if ($subitem['type']=='break') { 
							continue; 
						} elseif (in_array($subitem['type'],array('input','radio','list'))) {
							$realvar=$subitem['variable'];
							if (substr($realvar,0,1)=='$') {
								$realvar=substr($realvar,1);
							}
							for ($index=1; $index<=$configfileinfo['provisioning']['lines']; $index++) {
								if (!array_key_exists($realvar,$provisioner_lib->lines[$index])) {
									$provisioner_lib->lines[$index][$realvar]=$subitem['default_value'];
								}
							}
						} else {
							if ($debug) {
								#TODO: other types?
								print "Unknown type:\n";
								print_r($subitem);
							}
						}
					}
				} elseif (in_array($item['type'],array('input','radio','list'))) {
				} elseif (in_array($item['type'],array('input','radio','list'))) {
					$realvar=$item['variable'];
					if (substr($realvar,0,1)=='$') {
						$realvar=substr($realvar,1);
					}
					if (!array_key_exists($realvar,$provisioner_lib->options)) {
						$provisioner_lib->options[$realvar]=$item['default_value'];
					}
				} else {
					if ($debug) {
						#TODO: other types, including loops?
						print "Unknown type:\n";
						print_r($item);
					}
				}
			}
		}
	}
	print $provisioner_lib->generate_file($file,$configfileinfo['possibility']['file']);
	exit;
    }
   public function settings() {
	$this->view->title="Global Endpoint Settings"; // Some pages have the title automagically - how does that work?

        // $this->loadBaseModel($id):
        $this->package = Doctrine::getTable('package')->findOneby('name','endpointmanager');
        $this->view->set_global('base', 'package');
	if (isset($this->package['registry']['defaults']['global']['timezone'])) {
		$this->view->savedtimezone=$this->package['registry']['defaults']['global']['timezone'];
	} else {
		$this->view->savedtimezone=null;
	}
        Event::run('bluebox.load_base_model', $this->package);

        $this->updateOnSubmit($this->package);

        $this->prepareUpdateView('Package');
	
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
			$templates=array(); # $templates[$templatename]
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
#					"templates"=>$model['template_data']['files'],
				);
				foreach ($model['template_data']['files'] AS $file) {
					if (!array_key_exists($file,$templates)) {
						$templates[$file]=$this->_xmlread($file,array("item","subcategory"));
					}
					$data['phones'][$brand["directory"]][$model["model"]]["templates"][]=$templates[$file]['template_data']['category'];
				}
			}
		}
	}
	$cache->set('endpointmanager->provisioningdata',$data,NULL,3600);
	return $data;
   }
   public function dump() {
	header('Content-type: text/plain');
	print_r($this->_getprovisioningdata());
	exit;
   }

   protected function prepareUpdateView($baseModel = NULL) {
	parent::prepareUpdateView($baseModel);
	if (($baseModel!='Endpoint') && (!is_null($baseModel))) { return; }
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
	if (!is_array($linedata)) {
		$linedata=array();
	}

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
	if (get_class($object)!='Endpoint') {return;}
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

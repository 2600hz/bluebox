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
		//throw new Exception("Could not find config file $configfile");
                //Throw 404, don't throw kohana error message, phones expecting firmware get html and get confused..or crash
                header("HTTP/1.0 404 Not Found");
                die();
	}
	# TODO - if there are multiple possibilities, all with the same digest, which do not require per-phone settings, we could call it a success.
	throw new Exception("Multiple possibilities for file $configfile");
    }

    /* 
	This gets function gets the global defaults for all phones. There are some hard-coded default defaults.
     */
    private function _get_defaults() {
	$default_defaults=array(
		'global'=>array(
			'timezone'=>date_default_timezone_get(),
			'vlan_id'=>"0",
			'vlan_qos'=>"5",
			'ntpserver'=>'ntp.ubuntu.com',
		),
		'snom'=>array(
			'tone_scheme'=>'USA',
			'http_user'=>'admin',
			'http_pass'=>'snom',
			'time_24_format'=>'off',
			'date_us_format'=>'on',
			'admin_pass'=>'1234',
		),
	);
	$flag=0;
	$store = Doctrine::getTable('package')->findOneby('name','endpointmanager');
	$reg=array_merge_recursive($store->registry);
	$queue=array(array(&$reg,array("defaults"=>$default_defaults)));
	while ($queueent=array_shift($queue)) {
		foreach ($queueent[1] AS $key=>$val) {
			if (!array_key_exists($key,$queueent[0])) {
				$queueent[0][$key]=$val;
				$flag=1;
			} elseif ((is_array($queueent[0][$key])) && (is_array($queueent[1][$key]))) {
				$queue[]=array(&$queueent[0][$key],$queueent[1][$key]);
			}
		}
	}
	if ($flag==1) {
		$store->registry=$reg;
		$store->save();
	}
	return $store->registry['defaults'];
    }

    public function config ()
    {
	$file=implode(DIRECTORY_SEPARATOR,func_get_args());
        //die($file);
	$debug=array_key_exists('debug',$_REQUEST);
        
        require_once(dirname(dirname(__FILE__)).'/libraries/endpoint/base.php');
        
        $data = Provisioner_Globals::dynamic_global_files($file,dirname(dirname(__FILE__)).'/firmwares/','http://'.$_SERVER["SERVER_ADDR"].Kohana::config('core.site_domain').Kohana::config('core.index_page').'/endpointmanager/config/');
        if($data !== FALSE) {
            print $data;
            exit;
        }
        
	$configfileinfo=$this->_identify_configfile($file,$debug);

	if (!array_key_exists('endpoint',$configfileinfo)) {
		# TODO - if there is no endpoint, there may still be enough data to serve the config. We know the filename, model, family, and other info.
		//throw new Exception("Could not work out which endpoint the file belongs to");
                //Please don't send kohana 404s it serisouly confuses phones when they expect firmwares (polycom) but get html.
                header("HTTP/1.0 404 Not Found");
                die();
	}
        
        $class = "endpoint_" . $configfileinfo["possibility"]["make"] . "_" . $configfileinfo["provisioning"]["family"] . '_phone';
	$provisioner_lib = new $class();
	$provisioner_lib->options=array();
	foreach (array('mac','model') AS $attr) {
		$provisioner_lib->$attr=$configfileinfo['endpoint'][$attr];
	}
	$defaults = $this->_get_defaults();

	$provisioner_lib->DateTimeZone=new DateTimeZone($defaults['global']['timezone']);
	$provisioner_lib->timezone=$provisioner_lib->DateTimeZone->getOffset(new DateTime());
	$provisioner_lib->vlan_id=$defaults['global']['vlan_id'];
	$provisioner_lib->vlan_qos=$defaults['global']['vlan_qos'];

	if (array_key_exists($provisioner_lib->brand_name,$defaults)) {
		$provisioner_lib->options=array_merge_recursive($defaults[$provisioner_lib->brand_name],$provisioner_lib->options);
	}

	$lineinfo=unserialize($configfileinfo['endpoint']->lines);
	if ($lineinfo===false) {
		$lineinfo=array();
	}
	$lines=array();
	for ($index=1; $index<=$configfileinfo['provisioning']['lines']; $index++) {
		if ((!array_key_exists($index,$lineinfo)) or (empty($lineinfo[$index]['sip']))) {
			$provisioner_lib->lines[$index]=array();
		} else {
			$device=Doctrine::getTable('Device')->find($lineinfo[$index]['sip']);
			if (isset($device['plugins']['callerid']['internal_name'])) {
				$displayname=$device['plugins']['callerid']['internal_name'];
			} elseif (isset($device['plugins']['callerid']['external_name'])) {
				$displayname=$device['plugins']['callerid']['external_name'];
			} else {
				$displayname=$device['plugins']['sip']['username'];
			}
	
			$provisioner_lib->lines[$index]=array(
				"line"=>$index,
				"ext"=>$device['plugins']['sip']['username'],
				'displayname' => $displayname,
				'secret' => $device['plugins']['sip']['password'],
				'subscribe_mwi' => 1,
				'user_host'=>$device['User']['Location']['domain'],
			);
		}
		
	}
	$provisioner_lib->provisioning_type='http';
	# Note: slashes are forward slashes in windows too, because this is in web-space.
	$provisioner_lib->provisioning_path=Kohana::config('core.site_domain').Kohana::config('core.index_page').'/endpointmanager/config';

	if (!isset($provisioner_lib->server[1])) {
		$provisioner_lib->server[1]=array('ip'=>$configfileinfo['endpoint']['Account']['Location'][0]['domain'],'port'=>5060);
	}
        $provisioner_lib->root_dir = dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . "libraries" . DIRECTORY_SEPARATOR;
        $provisioner_lib->processor_info = 'Endpoint Manager 1.2 for Blue.Box';
	$provisioner_lib->prepare_for_generateconfig();
	print $provisioner_lib->generate_file($file,$configfileinfo['possibility']['file']);
	exit;
    }
   private function _autoquestion($make,$model,$templatevariable,$forvariable,$currentvalue=NULL) {
	$prov=$this->_getprovisioningdata();
	$structures=$prov['phones'][$make][$model]['templates'];
	while (count($structures)>=1) {
		$item=array_pop($structures);
		if (!is_array($item)) {
		} elseif ((isset($item['variable'])) && ($item['variable']==$templatevariable)) {
			$question=$item;
			break;
		} else {
			foreach ($item AS $value) {
				$structures[]=$value;
			}
		}
		if (count($structures)==0) { // question not found.
			return;
		}
	}

	if ((is_null($currentvalue)) && (isset($item['default_value']))) {
		$currentvalue=$item['default_value'];
	}
	$result='<div class="field">';
	$result.=form::label(array(
		'for'=>$forvariable,
	//	'hint'=>$item['description'],
	//	'help'=> - from $item['help']?
		), $item['description'].':');
	switch ($item['type']) {
		case 'input':
			$result.=form::input($forvariable,$currentvalue);
			break;
		case 'list':
			$result.="<select name='$forvariable'>";
			foreach ($item['data'] AS $data) {
				$selected=($data['value']==$currentvalue)?"selected":"";
				$result.="<option value='$data[value]' $selected>$data[text]</option>";
			}
			$result.="</select>";
			break;
	}
	$result.='</div>';
	return $result;
   }
   public function settings() {
	$this->view->title="Global Endpoint Settings"; // Some pages have the title automagically - how does that work?

	$defaults = $this->_get_defaults();

        // $this->loadBaseModel($id):
        $this->package = Doctrine::getTable('package')->findOneby('name','endpointmanager');
        $this->view->set_global('base', 'package');
	$this->view->savedtimezone=$defaults['global']['timezone'];

	$additionalquestions=array(
		'Snom'=>array(
			$this->_autoquestion("snom","300",'$tone_scheme','package[registry][defaults][snom][tone_scheme]',$defaults['snom']['tone_scheme']),
			$this->_autoquestion("snom","300",'$http_user','package[registry][defaults][snom][http_user]',$defaults['snom']['http_user']),
			$this->_autoquestion("snom","300",'$http_pass','package[registry][defaults][snom][http_pass]',$defaults['snom']['http_pass']),
			$this->_autoquestion("snom","300",'$admin_pass','package[registry][defaults][snom][admin_pass]',$defaults['snom']['admin_pass']),
			$this->_autoquestion("snom","300",'$time_24_format','package[registry][defaults][snom][time_24_format]',$defaults['snom']['time_24_format']),
			$this->_autoquestion("snom","300",'$date_us_format','package[registry][defaults][snom][date_us_format]',$defaults['snom']['date_us_format']),
		)
	);
	$this->view->additionalquestions="";
	foreach ($additionalquestions AS $make=>$questionlist) {
		$this->view->additionalquestions.=form::open_section("$make Settings");
		$this->view->additionalquestions.=implode("",$questionlist);
	 	$this->view->additionalquestions.=form::close_section();
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
		if (!file_exists($filename)) {
			throw new Exception("Could not find file $filename");
		}
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
	# This makes an OUI case-insensitive, in a regex.
	$repl=array('A'=>'[aA]','B'=>'[bB]','C'=>'[cC]','D'=>'[dD]','E'=>'[eE]','F'=>'[fF]');
	
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
		$brandxml=$this->_xmlread("$xmlbase$brand[directory]/brand_data.xml",array('family','oui'));
		foreach ($brandxml['data']['brands']['oui_list']['oui'] AS $oui) {
			$data['oui'][$oui]=$brand["directory"];
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
					foreach ($brandxml['data']['brands']['oui_list']['oui'] AS $oui) {
						$data['files'][]=array(
							"regex"=>str_replace('\$mac',"(".str_replace(array_keys($repl),array_values($repl),strtoupper($oui))."[\da-fA-F]{6})",$munged_conf),
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
						$templates[$file]=$this->_xmlread($file,array("item","subcategory","data"));
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
	$models=array();
	$brandname="<select id='brand_select' name='endpoint[brand]' onchange='update_models(this.value)'>";
	if (is_null($this->endpoint['brand'])) {
		$brandname.="<option value=''>Select</option>";
	}
	foreach ($prov['phones'] AS $brand=>$branddata) {
		$modeldata=array_values($branddata);
		if ($this->endpoint['brand']==$brand) {
			$selected="selected";
		} else {
			$selected="";
		}
		$brandname.="\t<option value='$brand' $selected>".$modeldata[0]["makename"]."</option>\n";
		$models[$brand]='';
		foreach (array_keys($branddata) AS $model) {
			if ($this->endpoint['model']==$model) {
				$models[$brand].="<option selected>$model</option>";
			} else {
				$models[$brand].="<option>$model</option>";
			}
		}
	}
	#print_r($models);
	$this->view->brandnameselect=$brandname."</select>";
	$this->view->oui_json=json_encode($prov['oui']);
	$this->view->models_json=json_encode($models);
	if (is_null($this->endpoint['brand'])) {
		$this->view->modelselect="<option value=''>Select</select>";
	} else {
		$this->view->modelselect=$models[$this->endpoint['brand']];
	}
	$linedata=unserialize($this->endpoint['lines']);
	if (!is_array($linedata)) {
		$linedata=array();
	}

	$brand=$this->endpoint['brand'];
	$model=$this->endpoint['model'];
	if (($brand!='') && ($model!='')) {
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
	if (array_key_exists('lines',$_POST)) {
		$object['lines']=serialize($_POST['lines']);;
	}
	parent::pre_save($object);

   }
    protected function updateOnSubmit($base)
    {
	// Parts copied from parent::updateOnSubmit
        if ($action = $this->submitted())
        {
            Event::run('bluebox.updateOnSubmit', $action);

            if ($action == self::SUBMIT_CONFIRM) {
		if (!isset($_POST['dontsave']) || ($_POST['dontsave']!='true')) {
			if ($this->formSave($base)) {
				$this->returnQtipAjaxForm($base);
				url::redirect(Router_Core::$controller);
			}
		} else { // This else clause is copied from parent::formSave().
		        if (get_parent_class($base) == 'Bluebox_Record') {
				$baseClass = get_class($base);
			} else {
				$baseClass = get_parent_class($base);
			}

		        // Import any post vars with the key of this model into the object
		        $formData = $this->input->post(strtolower($baseClass), array());
	
		        $base->fromArray($formData);
		}
            } 
            else if ($action == self::SUBMIT_DENY)
            {
                $this->exitQtipAjaxForm();

                url::redirect(Router_Core::$controller);
            }
        }
    }

}

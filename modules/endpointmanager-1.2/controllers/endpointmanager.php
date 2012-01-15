<?php defined('SYSPATH') or die('No direct access allowed.');

class EndpointManager_Controller extends Bluebox_Controller
{
    protected $baseModel = 'Endpoint';
    protected $authBypass = array('config');

    private function _identify_configfile($configfile,$debug) {
        $output = array();
        
        $filename = basename($_SERVER["PHP_SELF"]);
        $strip = str_replace('spa', '', $filename);
        if(preg_match('/[0-9A-Fa-f]{12}/i', $strip, $matches) && !(preg_match('/[0]{10}[0-9]{2}/i',$strip))) {
	$mac=strtolower($matches[0]);
        $p = Doctrine::getTable('Endpoint')->findOneBy('mac',$mac);
            if($p) {
                $dir = dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . "libraries" . DIRECTORY_SEPARATOR . 'endpoint/' . $p->brand;
                foreach (glob($dir."/*", GLOB_ONLYDIR) as $filename) {
                    $filename = $filename .'/family_data.json';
                    if(file_exists($filename)) {
                        $data = $this->_jsonread($filename);
                        $f = $this->arraysearchrecursive($p->model, $data, 'model');
                        if($f !== FALSE) {
                            $output['family'] = $data['data']['directory'];
                            $output['brand'] = $p->brand;
                            $output['mac'] = $p->mac;
                            $output['model'] = $p->model;
                            $output['lines'] = $p->lines;
                            $output['settings'] = $p->settings;
                            $output['max_lines'] = $data['data']['model_list'][$f[2]]['lines'];
                            return($output);
                        }
                    }
                }
            } else {
                header("HTTP/1.0 404 Not Found");
		print "Device with MAC $mac not found\n";
		Kohana::log('debug', "Device with MAC $mac not found, looking for file $configfile");
		exit;
            }
        } else {
            header("HTTP/1.0 404 Not Found");
	    print "No MAC address found in file $configfile\n";
	    Kohana::log('debug', "No MAC address found in requested filename $configfile");
	    exit;
        }
    }
    
    private function arraysearchrecursive($Needle, $Haystack, $NeedleKey="", $Strict=false, $Path=array()) {
        if (!is_array($Haystack))
            return false;
        foreach ($Haystack as $Key => $Val) {
            if (is_array($Val) &&
                    $SubPath = $this->arraysearchrecursive($Needle, $Val, $NeedleKey, $Strict, $Path)) {
                $Path = array_merge($Path, Array($Key), $SubPath);
                return $Path;
            } elseif ((!$Strict && $Val == $Needle &&
                    $Key == (strlen($NeedleKey) > 0 ? $NeedleKey : $Key)) ||
                    ($Strict && $Val === $Needle &&
                    $Key == (strlen($NeedleKey) > 0 ? $NeedleKey : $Key))) {
                $Path[] = $Key;
                return $Path;
            }
        }
        return false;
    }
    
    private function siteURL() {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $domainName = $_SERVER['HTTP_HOST'];
        return $protocol.$domainName;
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
		'cisco'=>array(
			'date_template'=>'M/D/YA',
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
        if(empty($file)) {
            echo "Endpoint Manager works!";
            exit;
        }
	$debug=array_key_exists('debug',$_REQUEST);
        
        if(!file_exists(dirname(dirname(__FILE__)).'/libraries/endpoint/base.php')) {
            header('HTTP/1.1 500 Internal Server Error');
            exit;
        }
        
        require_once(dirname(dirname(__FILE__)).'/libraries/endpoint/base.php');
        
        $data = Provisioner_Globals::dynamic_global_files($file,dirname(dirname(__FILE__)).'/firmwares/','http://'.$_SERVER["SERVER_ADDR"].Kohana::config('core.site_domain').Kohana::config('core.index_page').'/endpointmanager/config/');
        if($data !== FALSE) {
            print $data;
            exit;
        }
        
	$configfileinfo=$this->_identify_configfile($file,$debug);
        
        $class = "endpoint_" . $configfileinfo["brand"] . "_" . $configfileinfo["family"] . '_phone';
	$provisioner_lib = new $class();
	$provisioner_lib->options=array();

	$defaults = $this->_get_defaults();

	$provisioner_lib->DateTimeZone=new DateTimeZone($defaults['global']['timezone']);

        $provisioner_lib->brand_name = $configfileinfo["brand"];
        $provisioner_lib->family_line = $configfileinfo["family"];
        $provisioner_lib->model = $configfileinfo["model"];
        
        $provisioner_lib->mac = $configfileinfo["mac"];
        
	if (array_key_exists($provisioner_lib->brand_name,$defaults)) {
		$provisioner_lib->settings=array_merge_recursive($defaults[$provisioner_lib->brand_name],$provisioner_lib->settings);
	}

	$lineinfo=unserialize($configfileinfo['lines']);
	if ($lineinfo===false) {
		$lineinfo=array();
	}
	$lines=array();
	for ($index=0; $index<$configfileinfo['max_lines']; $index++) {
		if ((!array_key_exists($index,$lineinfo)) or (empty($lineinfo[$index]['sip']))) {
			$provisioner_lib->lines[]=array();
		} else {
			$device=Doctrine::getTable('Device')->find($lineinfo[$index]['sip']);
			if (isset($device['plugins']['callerid']['internal_name'])) {
				$displayname=$device['plugins']['callerid']['internal_name'];
			} elseif (isset($device['plugins']['callerid']['external_name'])) {
				$displayname=$device['plugins']['callerid']['external_name'];
			} else {
				$displayname=$device['plugins']['sip']['username'];
			}
	
			$provisioner_lib->settings['line'][]=array(
				"line"=>$index,
				"username"=>$device['plugins']['sip']['username'],
                            	"authname"=>$device['plugins']['sip']['username'],
				'displayname' => $displayname,
				'secret' => $device['plugins']['sip']['password'],
				'subscribe_mwi' => 1,
				'server_host'=>$device['User']['Location']['domain'],
                                'server_port' => 5060,
                                'server_expires' => 3600,
                                'backup_server_host' => '',
                                'backup_server_port' => 5060,
			);
		}
		
	}

        $pp = $this->siteURL().strstr($_SERVER["PHP_SELF"], 'endpointmanager', true).'endpointmanager/config/';
        $provisioner_lib->settings['provision'] = array(
            "type" => "dynamic",
            "protocol" => "http",
            "path" => $pp,
            "encryption" => FALSE,
            );

        $provisioner_lib->settings['network'] = array(
            "dhcp" => TRUE,
            "ipv4" => "",
            "ipv6" => "",
            "subnet" => "255.255.255.0",
            "gateway" => ""
            );
        
        $provisioner_lib->settings['network']['vlan'] = array(
          "id" => $defaults['global']['vlan_id'],
          "qos" => $defaults['global']['vlan_qos']
        );
        
        $provisioner_lib->settings['ntp'] = $defaults['global']['ntpserver'];
        $provisioner_lib->settings['network']['local_port'] = 5060; //here incase we want to randomize it...
        
        $provisioner_lib->root_dir = dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . "libraries" . DIRECTORY_SEPARATOR;
        $provisioner_lib->processor_info = 'Endpoint Manager 1.3 for Blue.Box';
	//$provisioner_lib->prepare_for_generateconfig();
	//print $provisioner_lib->generate_file($file,$configfileinfo['possibility']['file']);
        
        $rd = $provisioner_lib->generate_all_files();
                
        if(array_key_exists($file, $rd)) {
            print $rd[$file];
        } else {
            header("HTTP/1.0 404 Not Found");
            die();
        }
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
		),
		'Cisco'=>array(
			$this->_autoquestion("cisco","7941G",'$date_template','package[registry][defaults][cisco][date_template]',$defaults['cisco']['date_template']),
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
       if(file_exists(dirname(dirname(__FILE__)).'/libraries/endpoint/master/json')) {
           $message = "<b>To use Endpoint Manager you must run the commands below as root:</b><br />".
                   "<b>From this location: </b><br />".
                   "<i>".$_SERVER['DOCUMENT_ROOT'].dirname(dirname($_SERVER["PHP_SELF"]))."</i><br />".
                   "<b>Note: They must be run in this order! </b><br />".
                   "<i>git stash</i><br />".
                   "<i>git pull</i><br />".
                   "<i>git stash apply</i><br />".
                   "<i>git submodule update --init --recursive</i><br />";
           $this->template->content = $message;
       } else {
           $message = "<b>Set your phones/dhcp option 66 to:</b> <i>".$this->siteURL().$_SERVER["PHP_SELF"].'/config/</i><br />'.
                    '<b>Put Firmwares or other files you wish the phones to retrieve under:</b> <i>'.strstr(__DIR__,'controllers',TRUE).'firmwares/</i>';
       
            $this->template->content = new View('generic/grid');
            // Setup the base grid object
            $grid = jgrid::grid($this->baseModel, array(
                        'caption' => 'Endpoints'
                            )
            );
            $grid->add('name', 'Name');
            $grid->add('mac', 'MAC', array('key' => true));
            $grid->add('brand', 'Brand');
            $grid->add('model', 'Model');

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
            
            $this->template->content .= $message;
       }
   }

   private function _jsonread($json) 
   {
       $data = file_get_contents($json);
       
	return json_decode($data,TRUE);
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

	$masterxml=$this->_jsonread($xmlbase."master.json");
	foreach ($masterxml['data']['brands'] AS $brand) {
		$data['phones'][$brand["directory"]]=array();
		$brandxml=$this->_jsonread("$xmlbase$brand[directory]/brand_data.json");
		foreach ($brandxml['data']['brands']['oui_list'] AS $oui) {
			$data['oui'][$oui]=$brand["directory"];
		}
		foreach ($brandxml['data']['brands']['family_list'] AS $family) {
			$templates=array(); # $templates[$templatename]
			$familyxml=$this->_jsonread("$xmlbase$brand[directory]/$family[directory]/family_data.json");
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
				foreach ($model['template_data'] AS &$filename) {
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
				foreach ($model['template_data'] AS $file) {
					if (!array_key_exists($file,$templates)) {
						$templates[$file]=$this->_jsonread($file);
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

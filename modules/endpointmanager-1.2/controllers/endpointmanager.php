<?php defined('SYSPATH') or die('No direct access allowed.');

class EndpointManager_Controller extends Bluebox_Controller
{
    protected $baseModel = 'Endpoint';
    protected $authBypass = array('config');
    private $keyevents=array(
	'F_ACCEPTED_LIST'=>'Accepted Calls',
	'F_CALL_LIST'=>'Call Lists',
	'F_REGS'=>'Change Active ID',
	'F_CANCEL'=>'Clear Pickup Info',
	'F_CONFERENCE'=>'Conference',
	'F_CONTACTS'=>'Contacts',
	'F_ADR_BOOK'=>'Directory',
	'F_DND'=>'DND',
	'F_FAVORITES'=>'Favorites',
	'F_REDIRECT'=>'Forward All',
	'F_SUPPORT'=>'Help',
	'F_R'=>'Hold',
	'F_DIRECTORY_SEARCH'=>'LDAP Directory',
	'F_LOGOFF_ALL'=>'Logoff Identities',
	'F_SETTINGS'=>'Menu',
	'F_MISSED_LIST'=>'Missed Calls',
	'F_DIALOG'=>'Monitor Calls',
	'F_MUTE'=>'Mute',
	'F_NEXT_ID'=>'Next Outgoing ID',
	'F_PREV_ID'=>'Prev. Outgoing ID',
	'F_REBOOT'=>'Reboot',
	'F_RECORD'=>'Record',
	'F_REDIAL'=>'Redial',
	'F_RETRIEVE'=>'Retrieve',
	'F_TRANSFER'=>'Transfer',
	'KEY_F_PKEY_LIST'=>'Virtual Keys',
    );

    private function _identify_configfile_regex($configfile) {
	$debug=array_key_exists('debug',$_REQUEST);
	$prov=$this->_getprovisioningdata();
	$matches=array();
	if ($debug) {
		print "<pre>";
		print "Searching for config file $configfile\n";
	}
	foreach ($prov['files'] AS $index=>$possibility) {
		if (array_key_exists('search',$_REQUEST)) {
			$debug=($_REQUEST['search']==implode("|",array($possibility["brand"],$possibility["family"],$possibility['file'])));
		}
		if ($debug) {
			print "\n\nChecking $index:\n";
			print_r($possibility);
		}

		if (!preg_match('/^'.$possibility['regex'].'$/',$configfile,$args)) {
			if ($debug) 
				print "FAIL: regex\n";
			continue;
		}

		$onematch=array("details"=>$possibility,'args'=>array());
		array_shift($args);
		if (count($args)>0) {
			$onematch["args"]=array_combine($possibility['fields'],$args);
		}

		if (array_key_exists('mac',$onematch['args'])) {
			$ep=Doctrine::getTable('Endpoint')->findOneBy('mac',$onematch['args']['mac']);
			$onematch["endpoint"]=$ep;
			if (!is_object($ep)) {
				if ($debug)
					print "FAIL: MAC address not found\n";
				continue;
			}
			if (array_key_exists('phones',$prov) && array_key_exists($ep->brand,$prov['phones']) && array_key_exists($ep->model,$prov['phones'][$ep->brand])) {
				$onematch['provisioning']=$prov['phones'][$ep->brand][$ep->model];
			}
			# If this file does not belong to this brand/model, skip it.
			if ($ep->brand!=$possibility['brand']) { 
				if ($debug)
					print "FAIL: Make doesn't match\n";
				continue;
			}
			if (!array_key_exists($ep->model,$prov['phones'][$ep->brand][$possibility['family']]['models'])) {
				if ($debug)
					print "FAIL: Model doesn't match\n";
				continue;
			}
		}
		if ($debug) {
			print "SUCCESS\n";
		} elseif (array_key_exists('search',$_REQUEST)) {
			print "\n\nChecking $index:\n";
			print_r($possibility);
			print "SUCCESS\n";
		}
		array_push($matches,$onematch);
	}
	if ($debug) {
		print "\n\n\n\n\n\n"; print_r($matches);
	}
	if (count($matches)==0) {
		if ((!$debug) && (!array_key_exists('search',$_REQUEST))) {
			header("HTTP/1.0 404 Not Found");
		}
		print "File not found\n";
		Kohana::log('debug', "No match found for requested filename $configfile");
		exit;
	}
	if (count($matches)>1) {
		if ((!$debug) && (!array_key_exists('search',$_REQUEST))) {
			header("HTTP/1.0 404 Not Found");
		}
		print "Multiple possible files found\n<!--\n";
		print_r($matches);
		print "\n-->";
		Kohana::log('debug', "Multiple matches found for requested filename $configfile");
		exit;
	}
	return $matches[0];
    }

    private function _identify_configfile($configfile) {
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
				return array(
					'endpoint'=>$p,
					'args'=>array('mac'=>$mac),
					'details'=>array(
						'brand'=>basename($dir),
						'family'=>$data['data']['directory'],
						'familyname'=>$data['data']['name'],
						'path'=>$dir.DIRECTORY_SEPARATOR.$data['data']['directory'].DIRECTORY_SEPARATOR,
					),
					'linecount'=>$data['data']['model_list'][$f[2]]['lines'],
				);
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
	if (in_array($domainName,array('127.0.0.1','localhost'))) {
		$domainName=gethostname();
	}
        return $protocol.$domainName;
    }

    /* 
	This gets function gets the global defaults for all phones. There are some hard-coded default defaults.
     */
    private function _get_defaults() {
	$default_defaults=array(
		'global'=>array(
			'linedisplay'=>'name',
			'timezone'=>date_default_timezone_get(),
			'network'=>array(
				'vlan'=>array('id'=>0,'qos'=>5),
				"dhcp" => TRUE,
				"ipv4" => "",
				"ipv6" => "",
				"subnet" => "255.255.255.0",
				"gateway" => "",
        			'local_port' => 5060, //here incase we want to randomize it...
			),
			'ntp'=>'ntp.ubuntu.com',
			'voicemail_extension'=>'*97',
			'provision'=>array(
				"type" => "dynamic",
				"protocol" => "http",
				"path" => $this->siteURL().strstr($_SERVER["PHP_SELF"], 'endpointmanager', true).'endpointmanager/config/',
				"encryption" => FALSE,
			),
			'dateformat'=>'little-endian',
			'timeformat'=>'24-hour',
			'tonescheme'=>'USA',
		),
		'snom'=>array(
			'tone_scheme'=>'USA',
			'http_user'=>'admin',
			'http_pass'=>'snom',
			'time_24_format'=>'off',
			'date_us_format'=>'on',
			'admin_mode_password'=>'1234',
		),
		'cisco'=>array(
			'date_template'=>'M/D/YA',
			'preferredcodec'=>'none',
			'image_name'=>'',
			'firmware'=>array(
				'SPA941'=>'','SPA942'=>'','SPA2102'=>'',
				'SIP7941_SIP7961'=>'','SIP7942_SIP7962'=>'','SIP7945_SIP7965'=>'',
			),
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
    private function _findfamily($brand,$model) {
	$prov=$this->_getprovisioningdata();
	foreach ($prov['phones'][$brand] AS $family=>$familydata) {
		if (array_key_exists($model,$familydata['models'])) {
			return $family;
		}
	}
	return;
    }

    public function config ()
    {
	$use_regex=false;
	if (array_key_exists('file',$_REQUEST)) {
		$file=$_REQUEST['file'];
	} else {
		$file=implode(DIRECTORY_SEPARATOR,func_get_args());
	}
        if(empty($file)) {
            echo "Endpoint Manager works!";
            exit;
        }
        if(!file_exists(dirname(dirname(__FILE__)).'/libraries/endpoint/base.php')) {
            header('HTTP/1.1 500 Internal Server Error');
            exit;
        }
        
        require_once(dirname(dirname(__FILE__)).'/libraries/endpoint/base.php');

        $data = Provisioner_Globals::dynamic_global_files($file,dirname(dirname(__FILE__)).'/firmwares/',$this->siteURL().Kohana::config('core.site_domain').Kohana::config('core.index_page').'/endpointmanager/config/');
        if($data !== FALSE) {
            print $data;
            exit;
        }
	if ($use_regex) {
		$configfileinfo=$this->_identify_configfile_regex($file);
	} else {
		$configfileinfo=$this->_identify_configfile($file);
	}
        
        $class = "endpoint_" . $configfileinfo["details"]["brand"] . "_" . $configfileinfo["details"]["family"] . '_phone';
	$provisioner_lib = new $class();

	$defaults = $this->_get_defaults();

	$provisioner_lib->DateTimeZone=new DateTimeZone($defaults['global']['timezone']);

        $provisioner_lib->brand_name = $configfileinfo['endpoint']["brand"];
        $provisioner_lib->family_line = $this->_findfamily($configfileinfo['endpoint']['brand'],$configfileinfo['endpoint']["model"]);
        $provisioner_lib->model = $configfileinfo['endpoint']["model"];
        
        $provisioner_lib->mac = $configfileinfo["endpoint"]["mac"];
        
	$provisioner_lib->settings=array_merge_recursive($defaults['global'],$provisioner_lib->settings);
	if (array_key_exists($provisioner_lib->brand_name,$defaults)) {
		$provisioner_lib->settings=array_merge_recursive($defaults[$provisioner_lib->brand_name],$provisioner_lib->settings);
	}
	if (array_key_exists('firmware',$provisioner_lib->settings) && is_array($provisioner_lib->settings['firmware'])) {
		foreach ($provisioner_lib->settings['firmware'] AS $key=>$value) {
			if (in_array($provisioner_lib->model,explode("/",$key))) {
				$provisioner_lib->settings['firmware']=$provisioner_lib->settings['firmware'][$provisioner_lib->model];
			}
		}
		if (is_array($provisioner_lib->settings['firmware'])) {
			$provisioner_lib->settings['firmware']='';
		}
	}

	$lineinfo=unserialize($configfileinfo['endpoint']['lines']);
	if ($lineinfo===false) {
		$lineinfo=array();
	}
	$lines=array();
	$provisioner_lib->settings["line"]=array();
	for ($index=0; $index<$configfileinfo['linecount']; $index++) {
		if ((!array_key_exists($index,$lineinfo)) or (empty($lineinfo[$index]['sip']))) {
		} else {
			$device=Doctrine::getTable('Device')->find($lineinfo[$index]['sip']);
			if ($defaults['global']['linedisplay']=='extension') {
				$displayname=$device['plugins']['sip']['username'];
			} elseif (isset($device['plugins']['callerid']['internal_name'])) {
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
	if ($provisioner_lib->brand_name == "snom") {
		foreach ($configfileinfo["endpoint"]->registry["buttons"] AS $index=>$button) {
			if ($button["type"]=='none') {
				// do nothing!
			} elseif ($button["type"]=='sipaccount') {
				if (isset($provisioner_lib->settings['line'][$button["sipaccount"]-1])) {
					$displayname=$provisioner_lib->settings['line'][$button["sipaccount"]-1]["displayname"];
				} else {
					$displayname="?";
				}
				$provisioner_lib->settings["loops"]["functionkey"][$index]=array(
					"type"=>"line",
					"label"=>$displayname,
					"context"=>"active",
					"value"=>""
				);
			} elseif ($button["type"]=="keyevent") {
				$provisioner_lib->settings["loops"]["functionkey"][$index]=array(
					"type"=>"keyevent",
					"label"=>$this->keyevents[$button["keyevent"]],
					"context"=>"active",
					"value"=>$button["keyevent"],
				);
			} elseif ($button["type"]=="blf") {
				$dev= Doctrine::getTable('Device')->findOneBy('device_id',$button["blf"]);
				if (isset($dev->plugins["callerid"]["internal_name"])) {
					$displayname=$dev->plugins["callerid"]["internal_name"];
				} else {
					$displayname=$dev->name;
				}
				$provisioner_lib->settings["loops"]["functionkey"][$index]=array(
					"type"=>"blf",
					"label"=>$displayname,
					"context"=>"active",
					"value"=>"<sip:".$dev->plugins["sip"]["username"].'@'.$device['User']['Location']['domain'].">|*22"
				);
			} elseif ($button["type"]=="speeddial") {
				$sd=Doctrine::getTable("ExternalXfer")->findOneBy('external_xfer_id',$button["speeddial"]);
				$provisioner_lib->settings["loops"]["functionkey"][$index]=array(
					"type"=>"dest",
					"label"=>$sd->name,
					"context"=>"active",
					"value"=>$sd->Number[0]["number"].'@'.$device['User']['Location']['domain']
				);
			} elseif ($button["type"]=="internal_dial") {
				$provisioner_lib->settings["loops"]["functionkey"][$index]=array(
					"type"=>"dest",
					"label"=>$button["internal_dial_label"],
					"context"=>"active",
					"value"=>$button["internal_dial"].'@'.$device['User']['Location']['domain']
				);
			} elseif ($button["type"]=="external_dial") {
				$provisioner_lib->settings["loops"]["functionkey"][$index]=array(
					"type"=>"dest",
					"label"=>$button["external_dial_label"],
					"context"=>"active",
					"value"=>"1".$button["external_dial"].'@'.$device['User']['Location']['domain']
				);
			} else {
				$provisioner_lib->settings["loops"]["functionkey"][$index]=array("type"=>"bob","label"=>"bob","context"=>"active","value"=>"val");
				print_r($button);
			}
		}
	}
        
        $provisioner_lib->root_dir = dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . "libraries" . DIRECTORY_SEPARATOR;
        $provisioner_lib->processor_info = 'Endpoint Manager 1.3 for Blue.Box';
	if ($use_regex) {
		header("content-type: text/plain");
		$provisioner_lib->prepare_for_generateconfig();
		print $provisioner_lib->generate_file($file,$configfileinfo['possibility']['file']);
		exit;
	} else {
        	$rd = $provisioner_lib->generate_all_files();
        	if(array_key_exists($file, $rd)) {
		    header("content-type: text/plain");
        	    print $rd[$file];
	        } else {
	            header("HTTP/1.0 404 Not Found");
		    print "File $file not found\n";
	            die();
        	}
	}
	exit;
    }
   private function _autoquestion($brand,$family,$templatevariable,$target,$replacementquestion=NULL) {
	$forvariable='package[registry][defaults]['.implode("][",$target)."]";
	$currentvalue=$this->view->defaults;
	foreach ($target AS $field) {
		$currentvalue=$currentvalue[$field];
	}
	$prov=$this->_getprovisioningdata();
	$structures=$prov['phones'][$brand][$family]['templates'];
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
			print "<!--";
			print_r($prov['phones'][$brand][$family]['templates']);
			print "--!>";
			throw new exception("template question $templatevariable not found in $brand $family");
			return;
		}
	}
	if ((is_null($currentvalue)) && (isset($item['default_value']))) {
		$currentvalue=$item['default_value'];
	}
	$result="<div class='field'>";
	$result.=form::label(array(
		'for'=>$forvariable,
	//	'hint'=>$item['description'],
	//	'help'=> - from $item['help']?
		), ($replacementquestion!==NULL?$replacementquestion:$item['description']).':');
	switch ($item['type']) {
		case 'list':
			$result.="<select name='$forvariable'>";
			foreach ($item['data'] AS $data) {
				$selected=($data['value']==$currentvalue)?"selected":"";
				$result.="<option value='$data[value]' $selected>$data[text]</option>";
			}
			$result.="</select>";
			break;
		case 'input':
		default:
			$result.=form::input($forvariable,$currentvalue);
			break;
	}
	$result.='</div>';
	return $result;
   }

   // $glob can be either a glob, or it can be an array of options.
   public function _auto_firmware($make,$family,$models,$description) {
	$prov=$this->_getprovisioningdata();
	$familyinfo=&$prov['phones'][$make][$family]['models'];
	foreach (explode("_",$models) as $onemodel) {
		if ((array_key_exists($onemodel,$familyinfo)) && (array_key_exists("firmware",$familyinfo[$onemodel]))) {
			$glob=$familyinfo[$onemodel]["firmware"];
		} else {
			throw new exception("Could not find firmware info for $make $family $onemodel");
		}
	}

	if (!is_array($glob)) {
		$search=dirname(dirname(__FILE__))."/firmwares/$glob";
		$glob=array();
		foreach (glob($search) AS $val) {
			$glob[$val]=basename($val);
		}
	}
	if (!array_key_exists($make,$this->view->defaults)) {
		$this->view->defaults[$make]=array();
	}
	if (!array_key_exists('firmware',$this->view->defaults[$make])) {
		$this->view->defaults[$make]['firmware']=array();
	}
	if (!array_key_exists($models,$this->view->defaults[$make]['firmware'])) {
		$this->view->defaults[$make]['firmware'][$models]="";
	}

	$result="<div class='field'>";
	$result.=form::label(array(
		'for'=>"package[registry][defaults][$make][firmware][$models]",
		), $description.':');
	$result.="<select name='package[registry][defaults][$make][firmware][$models]'>";
	$result.="<option value=''>(none)</option>";
	foreach ($glob AS $file=>$display) {
		$selected=($file==$this->view->defaults[$make]['firmware'][$models])?"selected":"";
		$result.="<option value='$file' $selected>$display</option>";
	}
	$result.="</select>";
	$result.='</div>';
	return $result;
   }
   public function settings() {
	$this->view->title="Global Endpoint Settings"; // Some pages have the title automagically - how does that work?

	$this->view->defaults = $this->_get_defaults();

        // $this->loadBaseModel($id):
        $this->package = Doctrine::getTable('package')->findOneby('name','endpointmanager');
        $this->view->set_global('base', 'package');
	$this->view->savedtimezone=$this->view->defaults['global']['timezone'];
	$this->view->additional_global_questions=implode("",array(
		$this->_autoquestion('','','$vlan_id',array('global','network','vlan','id')),
		$this->_autoquestion('','','$vlan_qos',array('global','network','vlan','qos')),
		$this->_autoquestion('','','$voicemail_extension',array('global','voicemail_extension')),
		$this->_autoquestion('','','$ntp',array('global','ntp')),
		$this->_autoquestion('','','$timeformat',array('global','timeformat')),
		$this->_autoquestion('','','$dateformat',array('global','dateformat')),
		$this->_autoquestion('','','$tonescheme',array('global','tonescheme')),
	));

	$additionalquestions=array(
		'Snom'=>array(
			$this->_autoquestion("snom","3xx820m3",'$http_user',array('snom','http_user')),
			$this->_autoquestion("snom","3xx820m3",'$http_pass',array('snom','http_pass')),
			$this->_autoquestion("snom","3xx820m3",'$admin_mode_password',array('snom','admin_mode_password')),
   			$this->_auto_firmware("snom","3xx820m3","300","Snom 300 firmware"),
   			$this->_auto_firmware("snom","3xx820m3","320","Snom 320 firmware"),
   			$this->_auto_firmware("snom","3xx820m3","360","Snom 360 firmware"),
   			$this->_auto_firmware("snom","3xx820m3","370","Snom 370 firmware"),
   			$this->_auto_firmware("snom","3xx820m3","820","Snom 820 firmware"),
		),
		'Cisco/Linksys'=>array(
			$this->_autoquestion("cisco","sip79x1G",'$preferredcodec',array('cisco','preferredcodec')),
   			$this->_auto_firmware("cisco","sip79x1G","7941G_7961G","7941/7961 SIP firmware"),

   			$this->_auto_firmware("cisco","spa","SPA941","SPA-941 firmware"),
   			$this->_auto_firmware("cisco","spa","SPA942","SPA-942 firmware"),
   			$this->_auto_firmware("cisco","linksysata","SPA2102","SPA-2102 firmware"),
		),
	);
	$this->view->additionalquestions="";
	ksort($additionalquestions);
	foreach ($additionalquestions AS $brand=>$questionlist) {
		$this->view->additionalquestions.=form::open_section("$brand Settings")."\n";
		$this->view->additionalquestions.=implode("\n",$questionlist)."\n";
	 	$this->view->additionalquestions.=form::close_section()."\n\n\n";
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
	if (!file_exists($json)) {
		throw new exception("Could not find json file $json");
	}
	$data = file_get_contents($json);
	if ($data===NULL) {
		throw new exception("Could not read json file $json");
	}
	$data=json_decode($data,TRUE);
	if(!function_exists('json_last_error')) { 
		return $data;
	};

	$errno=json_last_error();
	$errors=array(
		'NONE'=>NULL, // NULL => OK.
		'DEPTH'=>'The maximum stack depth has been exceeded',
		'STATE_MISMATCH'=>'Invalid or malformed JSON',
		'CTRL_CHAR'=>'Control character error, possibly incorrectly encoded',
		'SYNTAX'=>'Syntax error',
		'UTF8'=>'Malformed UTF-8 characters, possibly incorrectly encoded',
	);
	foreach ($errors AS $const=>$message) {
		$const='JSON_ERROR_'.$const;
		if (!defined($const)) {
			continue;
		}
		if (constant($const)!=$errno) {
			continue;
		}
		if ($message==NULL) {
			return $data;
		}
		throw new exception("Error parsing json file $json: $message");
	}
	throw new exception("Error parsing json file $json: Unknown error $errno");
   }

   private function _getprovisioningdata() {
	$cache=Cache::instance();
	if ($data=$cache->get('endpointmanager->provisioningdata')) {
		$this->privateprovdata=$data;
		return $data;
	}
	# This makes an OUI case-insensitive, in a regex.
	$repl=array('A'=>'[aA]','B'=>'[bB]','C'=>'[cC]','D'=>'[dD]','E'=>'[eE]','F'=>'[fF]');
	
	# Note: $this->privateprovdata is PRIVATE to this function - do not use it directly. If you want the data, call this function.
	if (property_exists($this,'privateprovdata')) {
		return $this->privateprovdata;
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
			$conf_files=array();
			if (array_key_exists("files",$familyxml['data'])) {
				foreach ($familyxml['data']['files'] AS $file=>$conf_file) {
					$conf_file['file']=$file;
					$conf_files[]=$conf_file;
				}
			} else {
				foreach (explode(",",$familyxml['data']['configuration_files']) AS $conf_file) {
					$conf_files[]=array("file"=>$conf_file);
				}
			}
			foreach ($conf_files AS $conf_file) {
				# Add a regex, if it doesn't exist
				if (!array_key_exists('fields',$conf_file)) {
					preg_match_all('|\$([a-z]+)|',$conf_file['file'],$fields);
					$conf_file['fields']=$fields[1];
				}
				if (!array_key_exists('regex',$conf_file)) {
					$conf_file['regex']=$conf_file['file'];
					$conf_file['regex']=str_replace('\\','\\\\',$conf_file['regex']);
					$conf_file['regex']=str_replace(array('/',".",'$'),array('\/','\.','\$'),$conf_file['regex']);
					foreach ($conf_file['fields'] AS $param) {
						if ($param=='mac') {
							$replace='';
							foreach ($brandxml['data']['brands']['oui_list'] AS $oui) {
								if ($replace!='')
									$replace.="|";
								$replace.=str_replace(array_keys($repl),array_values($repl),strtoupper($oui))."[\da-fA-F]{6}";
							}
						} elseif ($param=='model') {
							$models=array();
							foreach ($familyxml['data']['model_list'] AS $model) {
								$models[]=$model["model"];
							}
							$replace=implode("|",$models);
						} else {
							$replace='.*';
						}
						$conf_file['regex']=str_replace('\$'.$param,'('.$replace.')',$conf_file['regex']);
					}
					$conf_file['regex']='^'.$conf_file['regex'].'$';
				}
				$conf_file['brandname']=$brand['name'];
				$conf_file["brand"]=$brand["directory"];
				$conf_file["familyname"]=$family["name"];
				$conf_file["family"]=$family["directory"];
				$conf_file["path"]="$xmlbase$brand[directory]/$family[directory]/";
				$data['files'][]=$conf_file;
			}

			foreach ($familyxml['data']['model_list'] AS $model) {
				$data['phones'][$brand["directory"]][$family["directory"]]['models'][$model["model"]]=array(
					"lines"=>$model["lines"],
					"brandname"=>$brand["name"],
					"brand"=>$brand["directory"],
					"familyname"=>$family["name"],
					"family"=>$family['directory'],
					"path"=>"$xmlbase$brand[directory]/$family[directory]/",
					"templates"=>$model["template_data"],
				);
				if (array_key_exists("firmware",$model)) {
					$data['phones'][$brand["directory"]][$family["directory"]]['models'][$model["model"]]["firmware"]=$model["firmware"];
				}
				foreach ($model["template_data"] as $filename) {
					$data['phones'][$brand["directory"]][$family["directory"]]['templates'][$filename]=$this->_jsonread("$xmlbase$brand[directory]/$family[directory]/$filename");
				}
			}
		}
	}
	$data['phones']['']['']['templates']['global_template_data.json']=$this->_jsonread("$xmlbase/global_template_data.json");
	$cache->set('endpointmanager->provisioningdata',$data,NULL,3600);
	return $data;
   }
   public function dump() {
	$data=$this->_getprovisioningdata();
	header('Content-type: text/plain');
	print_r($data);
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
		if ($brand=='') continue; // special "global" settings.
		foreach ($branddata AS $family) {
			$modeldata=array_values($family['models']);
		}
		if ($this->endpoint['brand']==$brand) {
			$selected="selected";
		} else {
			$selected="";
		}
		$brandname.="\t<option value='$brand' $selected>".$modeldata[0]["brandname"]."</option>\n";
		$models[$brand]='';
		foreach ($branddata AS $family) {
			foreach (array_keys($family['models']) AS $model) {
				if ($this->endpoint['model']==$model) {
					$models[$brand].="<option selected>$model</option>";
				} else {
					$models[$brand].="<option>$model</option>";
				}
			}
		}
	}
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
		foreach ($prov['phones'][$brand] as $familyname=>$familyarray) {
			if (array_key_exists($model,$familyarray['models'])) {
				$family=$familyname;
			}
		}
		$linelist="";
		$this->view->models=$prov['phones'][$brand][$family]['models'][$model];
		$devices=array();
		foreach (Doctrine::getTable("Device")->findAll(Doctrine::HYDRATE_ARRAY) AS $device) {
			$devices[$device["device_id"]]=$device["plugins"]["callerid"]["internal_number"].": ".$device["name"];
		}
		$speeddials=array();
		foreach (Doctrine::getTable("ExternalXfer")->findAll(Doctrine::HYDRATE_ARRAY) AS $speeddial) {
			$speeddials[$speeddial["external_xfer_id"]]=$speeddial["name"];
		}

		// This puts the "Select" option at the top, with everything else sorted.
		$devices[""]="";
		$speeddials[""]="";
		asort($devices);
		asort($speeddials);
		$devices[""]="Select:";
		$speeddials[""]="Select:";
		$this->view->tabs="";
		$this->view->buttontabs="";
		$this->view->buttonlist="";
		$linedropdown=array();

		for ($line=1; $line<=$this->view->models['lines']; $line++) {
			$linedropdown[$line]="Line ".$line;
			$this->view->tabs.="<li><a href='#line_$line'><span style='font-size: 90%'>Line $line</span></a></li>\n";
			$view=new View('endpointmanager/line.mus');
			$view->speeddials=$speeddials;
			$view->devices=$devices;
			$view->line=$line;
			if (!array_key_exists($line,$linedata)) {
				$view->linedata=array("sip"=>null);
			} else {
				$view->linedata=$linedata[$line];
			}
			$linelist.=(string)$view;
		}
		$this->view->linelist=$linelist;
		$phonedata=$this->_getprovisioningdata();
		$phonedata=$phonedata["phones"][$brand];
		foreach ($phonedata AS $family) {
			if (array_key_exists($model,$family["models"])) {
				$myfamily=$family;
			}
		}
		$this->view->functionkeys=0;
		$this->view->buttons=array();
		foreach ($myfamily["models"][$model]["templates"] AS $template) {
			$queue=array(&$myfamily["templates"][$template]);
			$matches=array();
			while (count($queue)>0) {
				$item=array_pop($queue);
				if (is_array($item)) {
					foreach ($item AS $subitem) {
						array_push($queue,$subitem);
					}
					if (array_key_exists("name",$item) && ($item["name"]=="functionkeys")) {
						for ($button=$item["item"][0]["loop_start"]; $button<=$item["item"][0]["loop_end"]; $button++) {
							$view=new View('endpointmanager/button-snom.mus');
							$view->keyeventfunctions=$this->keyevents;
array(
								'F_ACCEPTED_LIST'=>'Accepted Calls',
								'F_CALL_LIST'=>'Call Lists',
								'F_REGS'=>'Change Active ID',
								'F_CANCEL'=>'Clear Pickup Info',
								'F_CONFERENCE'=>'Conference',
								'F_CONTACTS'=>'Contacts',
								'F_ADR_BOOK'=>'Directory',
								'F_DND'=>'DND',
								'F_FAVORITES'=>'Favorites',
								'F_REDIRECT'=>'Forward All',
								'F_SUPPORT'=>'Help',
								'F_R'=>'Hold',
								'F_DIRECTORY_SEARCH'=>'LDAP Directory',
								'F_LOGOFF_ALL'=>'Logoff Identities',
								'F_SETTINGS'=>'Menu',
								'F_MISSED_LIST'=>'Missed Calls',
								'F_DIALOG'=>'Monitor Calls',
								'F_MUTE'=>'Mute',
								'F_NEXT_ID'=>'Next Outgoing ID',
								'F_PREV_ID'=>'Prev. Outgoing ID',
								'F_REBOOT'=>'Reboot',
								'F_RECORD'=>'Record',
								'F_REDIAL'=>'Redial',
								'F_RETRIEVE'=>'Retrieve',
								'F_TRANSFER'=>'Transfer',
								'KEY_F_PKEY_LIST'=>'Virtual Keys',
							);
							$view->button=$button;
							if (isset($this->endpoint["registry"]["buttons"][$button])) {
								$view->buttondata=$this->endpoint["registry"]["buttons"][$button];
							} else {
								$view->buttondata=array();
							}
							// Special exception, defaults for snom300.
							$snom300defaults=array(2=>'F_REDIAL',3=>'F_ADR_BOOK',4=>'F_TRANSFER',5=>'F_MUTE');
							if (($model==300) && array_key_exists($button,$snom300defaults) && (!array_key_exists("type",$view->buttondata))) {
								$view->buttondata["type"]="keyevent";
								$view->buttondata["keyevent"]=$snom300defaults[$button];
							}
							// Set defaults for function keys - button X is set to line (X+1).
							if ($button+1<=$this->view->models["lines"]) {
								if (!array_key_exists("type",$view->buttondata)) {
									$view->buttondata["type"]="sipaccount";
								}
								if (!array_key_exists("sipaccount",$view->buttondata)) {
									$view->buttondata["sipaccount"]=$button+1;
								}
							}
							foreach (array("type","sipaccount","speeddial","blf","internal_dial","internal_dial_label","external_dial","external_dial_label","keyevent") AS $field) {
								if (!array_key_exists($field,$view->buttondata)) {
									$view->buttondata[$field]=null;
								}
							}
							$view->devices=$devices;
							$view->speeddials=$speeddials;
							$view->linedropdown=$linedropdown;
							$this->view->buttontabs.="\n<li><a href='#button_$button'><span style='font-size: 90%'>Button ".($button+1)."</span></a></li>";
							$this->view->buttonlist.="\n".(string)$view;
						}
					}
				}
			}
		}
	} else {
		$this->view->models=null;
		$this->view->linelist="";
	}
   }
   protected function formSave(&$object,$saveMessage = NULL, $saveEvents = array()) {
	if (get_class($object)=='Package') {
		$reg=$object->registry;
		$reg['defaults']=$_POST['package']['registry']['defaults'];
		$object->registry=$reg;
		unset($_POST['package']['registry']);
	}
	return parent::formSave($object,$saveMessage,$saveEvents);
   }

   protected function pre_save(&$object) {
	if (get_class($object)=='Endpoint') {
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

<?php defined('SYSPATH') or die('No direct access allowed.');

class Directory_Controller extends Bluebox_Controller
{
    protected $authBypass = array('listing','xmlout','jsonout');
    public function __construct()
    {
        parent::__construct();
        stylesheet::add("directory.css");
    }
    private function _callback_add_extensions(&$item,$key,$extensions)
    {
        if (is_array($item)) {
            array_walk($item, array($this,'_callback_add_extensions'),$extensions);
            if (
                    (array_key_exists('attr', $item)) &&
                    (is_array($item['attr'])) &&
                    (array_key_exists('id', $item['attr'])) &&
                    (substr($item['attr']['id'],0,5)=='node_') &&
                    (array_key_exists(substr($item['attr']['id'],5),$extensions)) // substr: because id=node_1
                    ) {
			if (!array_key_exists('children',$item)) {
				$item['children']=array();
                    	}
                	foreach ($extensions[substr($item['attr']['id'],5)] AS $ext) {
				array_unshift($item['children'],array(
					'data'=>$ext['name'],
					'attr'=>array('id'=>'dev_'.$ext['device_id'],"rel"=>"extension")
				));
				$item["state"]="open";
                	}
            }
        }
    }
    public function server()
    {
        if (array_key_exists('id',$_REQUEST)) {
            $id=$_REQUEST['id'];
        } else {
            $id='';
        }
        if ($id=='') {
            $rec=Doctrine::getTable('Grouping')->findOneBy('level',0);
        } elseif (substr($id,0,4)=="dev_") {
            // devs respond only to move_node. copy doesnt work, and the target must be a node.
            //if (($_REQUEST['operation']=='move_node') && ($_REQUEST['copy']!=1) && (substr($_REQUEST['ref'],0,5)=='node_')) {
            if (($_REQUEST['operation']=='move_node') && ($_REQUEST['copy']!=1) && (substr($_REQUEST['ref'],0,5)=='node_')) {
		if (!array_key_exists('debug',$_REQUEST)) {
	                header("HTTP/1.0 200 OK");
       	         	header('Content-type: text/json; charset=utf-8');
       	         	header("Cache-Control: no-cache, must-revalidate");
       	         	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
       	         	header("Pragma: no-cache");
		}
                $device=Doctrine::getTable('Device')->findOneBy("device_id",substr($id,4));
                $device['plugins']=array_merge($device['plugins'],array('directory'=>array(
                    'group'=>substr($_REQUEST['ref'],5),'order'=>$_REQUEST['position']
                )));
                $device->save();
                print json_encode(array('status'=>1));
            } else {
                print json_encode(array('status'=>0));
            }
            exit;
        } elseif (substr($id,0,5)=='node_') {
            $rec=Doctrine::getTable('Grouping')->findOneBy('grouping_id',substr($id,5));
        } else {
            $rec=Doctrine::getTable('Grouping')->findOneBy('grouping_id',$id);
        }
        if ($_REQUEST['operation']=='get_children') {
	    $extensions=$this->_get_grouped_extensions();
            $rec->jsTree_header(array_key_exists('debug',$_REQUEST));
            $data=$rec->jsTree_get_children();
            array_walk($data, array($this,'_callback_add_extensions'),$extensions);
            print json_encode($data);
            exit;
        } else {
            $rec->jsTree_controller($_REQUEST['operation'],$_REQUEST,array_key_exists('debug', $_REQUEST));
        }
        exit;
    }
    public function _get_grouped_extensions()
    {
	$extensions=array();
	foreach (Doctrine::getTable('Device')->findAll() AS $extension) {
		if (array_key_exists('directory',$extension['plugins']) && array_key_exists('group',$extension['plugins']['directory'])) {
			$group=$extension['plugins']['directory']['group'];
		} else {
			$group=1; // default group.
		}
                $extensions[$group][]=$extension;
        }
	return $extensions;
    }
    public function index() {
	//Most of the data comes in via AJAX. Only thing we need is the URL:
	$this->view->url=Kohana::config('core.site_domain').Kohana::config('core.index_page').'/directory/jsonout';
    }
    public function arrange()
    {
        javascript::add("jstree/_lib/jquery.cookie.js");
        javascript::add("jstree/_lib/jquery.hotkeys.js");
        javascript::add("jstree/jquery.jstree.js");
	$this->view->serverurl=Kohana::config('core.site_domain').Kohana::config('core.index_page').'/directory/server';
	$this->view->imagedir=Kohana::config('core.site_domain').'/modules/directory-1.0/assets/images/';
    }
    public function jsonout()
    {
        $listing=$this->_build_listing();
	if (!array_key_exists('debug',$_REQUEST)) {
 		header('Content-type: text/json');
	} else {
 		header('Content-type: text/plain');
	}
        print json_encode($listing);
        exit;
    }
    public function xmlout()
    {
	if (!array_key_exists('debug',$_REQUEST)) {
        	header('Content-type: text/xml');
	} else {
 		header('Content-type: text/plain');
	}
        $listing=$this->_build_listing();
        $writer=new XMLWriter();
        $writer->openURI('php://output');
        $writer->setIndentString('    ');
        $writer->setIndent(1);
        $writer->startDocument('1.0');
        $this->_xml_unparser($listing, $writer);
        $writer->endDocument();
        $writer->flush();
        exit;
    }
    public function _build_listing()
    {
        // TODO - UI for $node['plugins']['directory']['remote'] (fetch remote directory from XML URL (like http://localhost/directory/xmlout)

        if (!array_key_exists('cascade',$_REQUEST)) {
            $remotes=array();
        }
	$extensions=$this->_get_grouped_extensions();

        $stack=array(array());

        $tree=Doctrine::getTable('Grouping')->getTree()->fetchTree();
	$channels=$this->getchannels('presence_id');

	// TODO: Make this module location aware. Until then, use the first location.
	$location=Doctrine::getTable('Location')->findAll();
	$location='@'.$location[0]['domain'];
        $interfaces = SofiaManager::getSIPInterfaces();
	$registered=array();

        if ($interfaces) foreach($interfaces as $sipinterface_id => $interface) {
		foreach (SofiaManager::getRegistrations($interface) AS $reg) {
			$registered[$reg['user']]=1;
		}
        }

        foreach ($tree AS $node) {
		$stack[$node['level']]['value'][]=array('tag'=>'grouping','attributes'=>array('description'=>$node['name']));
		$stack[$node['level']+1]=&$stack[$node['level']]['value'][count($stack[$node['level']]['value'])-1];
		if (array_key_exists('directory',$node['plugins']) &&
			array_key_exists('remote',$node['plugins']['directory']) && 
			array_key_exists('cascade',$_REQUEST)) {
			$stack[$node['level']+1]['value']=$this->_remote_dir_fetch($node['plugins']['directory']['remote']);
		}
		if (array_key_exists($node['grouping_id'],$extensions)) {
			foreach ($extensions[$node['grouping_id']] AS $ext) {
				if (
					array_key_exists('callerid',$ext['plugins']) && 
					array_key_exists('internal_name',$ext['plugins']['callerid']) &&
					array_key_exists('internal_number',$ext['plugins']['callerid'])
					) {
					if (array_key_exists($ext['plugins']['sip']['username'].$location,$channels)) {
						$state=$channels[$ext['plugins']['sip']['username'].$location]['callstate'];
						if ($state=='ACTIVE') {
							$state='InUse';
						} elseif ($state=='RINGING') {
							$state='Ringing';
						} else {
							$state='InUse';
						}
					} elseif (array_key_exists($ext['plugins']['sip']['username'].$location,$registered)) {
						$state='Idle';
					} else {
						$state='Unavailable';
					}
					// example: {"tag":"extn","attributes":{"description":"Tim Figgins","extension":"9853","state":"InUse"}}
					$stack[$node['level']+1]['value'][]=array('tag'=>'extn','attributes'=>array(
						'description'=>$ext['plugins']['callerid']['internal_name'],
						'extension'=>$ext['plugins']['callerid']['internal_number'],
						'state'=>$state,
					));
				}
			}
		}
	}
        return $stack[0]['value'][0];
    }
	// This function gets an array of SimpleXMLElement objects - just (randomly) numerically indexed, or if
        // key is one of the fields, keyed by that field.
    public function getchannels($key=NULL) {
	$eslManager = new EslManager();
	if(!$eslManager->isConnected()) {print "Not connected\n"; exit; }
	$res=$eslManager->api("show","channels","as","xml");
        $xml=new XMLReader();
        $xml->xml($eslManager->getResponse($res));
	$channel=false;
	$thisch=array();
	$thistag="";
	$result=array();
	while ($xml->read()) {
            switch ($xml->nodeType) {
                case XMLReader::ELEMENT:
			$thistag=$xml->name;
			if ($thistag=='row') {
				if ($channel) {
					if (array_key_exists($key,$thisch)) {
						$result[$thisch[$key]]=$thisch;
					} else {
						$result[]=$thisch;
					}
				}
				$channel=true;
				$thisch=array();
			}
                    	break;
                case XMLReader::TEXT:
                case XMLReader::CDATA:
			$thisch[$thistag]=$xml->value;
	    }
	}
	if ($channel) {
		if (array_key_exists($key,$thisch)) {
			$result[$thisch[$key]]=$thisch;
		} else {
			$result[]=$thisch;
		}
	}
	return $result;
    }

    public function test() {
	print_r($this->getchannels('presence_id'));
	exit;
    }
    public function _remote_dir_fetch($url)
    {
        $tree=null;
        $xml=new XMLReader();
        $xml->open($url);
        $xml=$this->_xml_parser($xml);
        while ($xml[0]['tag']!='grouping') {
            $xml=$xml[0]['value'];
        }
        return $xml;
    }
    public function _xml_parser($xml)
    {
        $tree = null;
        while($xml->read())
            switch ($xml->nodeType) {
                case XMLReader::END_ELEMENT: return $tree;
                case XMLReader::ELEMENT:
                    if ($xml->name=='context') {
                        $node = array('tag' => 'grouping');
                    } else {
                        $node = array('tag' => $xml->name);
                    }
                    if (!$xml->isEmptyElement) {$node['value']=$this->_xml_parser($xml);}
                    if($xml->hasAttributes)
                        while($xml->moveToNextAttribute())
                            $node['attributes'][$xml->name] = $xml->value;
                        $tree[] = $node;
                    break;
                case XMLReader::TEXT:
                case XMLReader::CDATA:
                    $tree .= $xml->value;
            }
        return $tree;
    }
    public function _xml_unparser($object,$writer) {
        $writer->startElement($object['tag']);
        foreach ($object['attributes'] AS $key=>$value) {
            $writer->writeAttribute($key,$value);
        }
        if (array_key_exists('value', $object)) {
            foreach ($object['value'] AS $subobj){
                $this->_xml_unparser($subobj, $writer);
            }
        }
        $writer->endElement();
    }
}




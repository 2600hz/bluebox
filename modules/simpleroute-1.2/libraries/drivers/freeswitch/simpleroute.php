<?php defined('SYSPATH') or die('No direct access allowed.');

class FreeSwitch_SimpleRoute_Driver extends FreeSwitch_Base_Driver
{
    public static function set($base)
    {
	if ($base instanceOf Device) {
		if (isset($base['plugins']['simpleroute']['tags'])) {
			$tags=preg_split('/[\s\,]+/',$base['plugins']['simpleroute']['tags']);
			sort($tags);
			$tags=implode(" ",$tags);
		} else {
			$tags="";
		};

		$domain = '$${location_' .$base['User']['location_id'] .'}';

		$xml = FreeSwitch::setSection('user', $domain, $base['device_id']);

		$xml->update('/variables/variable[@name="bluebox-tags"]{@value="' .$tags .'"}');
	} elseif ($base instanceOf Simpleroute) {
            foreach (Doctrine::getTable('Context')->findAll() AS $thiscontext) {
		$routenumber=0;
		if (array_key_exists("simpleroute",$thiscontext->plugins)) {
			foreach ($thiscontext->plugins["simpleroute"] AS $route) {
				$trunk=Doctrine::getTable('Trunk')->find($route["trunk"]);
				$destination=Doctrine::getTable('SimpleRoute')->find($route["destination"]);
				$pattern_number=0;
                		foreach (simplerouter::getOutboundPattern($route["destination"], 'freeswitch') AS $pattern) {
					$routenumber++;
					$pattern_number++;
					$xml = FreeSwitch::createExtension("route_$routenumber",'main','context_'. $thiscontext->context_id);
					if (!isset($route["taglist"])) {
						$route["taglist"]="";
					}
					# Remove all the children, and put in a comment so we can see where we are!
					$xml->replaceWithXML('<!-- CONTEXT: '.$thiscontext->context_id.' - '.$thiscontext->name.'; DESTINATION: '.$route["destination"].' - '.$destination->name.'; TRUNK: '.$route["trunk"].' - '.$trunk->name.'; PATTERN: '.$pattern_number.' - '.$pattern." TAGS: $route[taglist] -->");
					if ($route["taglist"]!="") {
						$taglist=preg_split('/[\s\,]+/',$route["taglist"]);
						sort($taglist);
						$taglist='^(.* |)'.implode('( .* | )',$taglist).'( .*|)$';
						$xml->update('/condition[@field="${bluebox-tags}"][@break="on-false"][@expression="'.$taglist.'"]');
					}
	        			$xml->update('/condition[@field="destination_number"][@break="on-false"][@expression="'.$pattern.'"][@bluebox="extension-in"]');
                			// If a Caller ID module is installed and caller ID is set, use it
			                // TODO: Integrate this into the plugin
			                $caller_id = '/condition[@field="${outbound_caller_id_number}"][@expression="^.+$"][@break="never"][@bluebox="caller_id"]';

			                $xml->update($caller_id .'/action[@application="set"][@data="effective_caller_id_name=${outbound_caller_id_name}"]');

			                $xml->update($caller_id .'/action[@application="set"][@data="effective_caller_id_number=${outbound_caller_id_number}"]');
                			// Put Caller ID into the right place
			                if (isset($sip['caller_id_field'])) {
						if (($sip['caller_id_field'] == 'rpid') or ($sip['caller_id_field'] == 'pid')) {
							$xml->update($caller_id . '/action[@application="export"][@bluebox="caller_id_field"]{@data="sip_cid_type=' . $sip['caller_id_field'] . '"}');
						} else {
							// Assume Caller ID is default or elsewhere
							$xml->deleteNode($caller_id . '/action[@application="export"][@bluebox="caller_id_field"]');
						}
					}
					$dummy = '/condition[@field="destination_number"][@break="never"][@expression="'.$pattern.'"]';

					if (!empty($route['continue_on_fail'])) {
						$xml->update($dummy .'/action[@application="set"][@bluebox="setting_continue_on_fail"]{@data="failure_causes=NORMAL_CLEARING,ORIGINATOR_CANCEL,CRASH"}');
					} else {
						$xml->deleteNode($dummy .'/action[@application="set"][@bluebox="setting_continue_on_fail"]');
					}
					$xml->update($dummy . '/action[@application="bridge"][@bluebox="extension_out"]{@data="sofia\/gateway\/trunk_' .$route['trunk'] . '\/'.$route["dialstring"].'"}');
				}
			}
		}
		// If there are any routes > routenumber, then they are old and need to be deleted.
		$routenumber++;
		$xml = FreeSwitch::createExtension("route_$routenumber",'main','context_'. $thiscontext->context_id,array("existing"));
		while ($xml!==FALSE) {
			$xml->deleteNode();
			$routenumber++;
			$xml = FreeSwitch::createExtension("route_$routenumber",'main','context_'. $thiscontext->context_id,array("existing"));
		}
	    }
	}
    }
    
    public static function delete($base)
    {
        if (empty($base['plugins']['simpleroute']))
        {
            return;
        }

        $simpleroute = $base['plugins']['simpleroute'];
	$routenumber==0;

	$xml = FreeSwitch::createExtension("route_$routenumber",'main','context_'. $base->context_id);
	while ($xml!==FALSE) {
		$xml->deleteNode();
		$routenumber++;
		$xml = FreeSwitch::createExtension("route_$routenumber",'main','context_'. $base->context_id,array("existing"));
        }
    }
}

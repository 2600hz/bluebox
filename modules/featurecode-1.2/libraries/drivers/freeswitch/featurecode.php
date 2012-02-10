<?php defined('SYSPATH') or die('No direct access allowed.');

class FreeSwitch_FeatureCode_Driver extends FreeSwitch_Base_Driver
{
    public static function set($obj) 
    {

    }

    public static function delete($obj) 
    {

    }

    public static function dialplan($number)
    {
        $xml = Telephony::getDriver()->xml;
        $destination = $number['Destination'];

	$replacements=array(
		"\n"=>"\n\t",
		"%ACCOUNT_ID%"=>$destination['account_id']
	);

	$xmlText=$destination['CustomFeatureCode']['dialplan_code'];

	// whitespace at start & end make it look nice, matching style as at writing. As nice as possible. 
	$xmlText="\n\t".str_replace(array_keys($replacements),array_values($replacements),$xmlText)."\n        ";
	$xmlText=str_replace('\n','\n\t',$xmlText);
	$struct=new SimpleXMLElement("<extension>".$xmlText."</extension>"); // Validate! - if it fails dialplan doesn't get changed.
	// it should also be validated (as xml) on insertion.
	$iscondition=NULL;
	foreach ($struct->children() AS $a=>$b) {
		if ($a=="condition") {
			$iscondition=$b;
			break;
		}
	}
	$xml->deleteChildren();
	if ($iscondition!==NULL) {
		// If there is a condition, put this XML AFTER the current condition
		// so it becomes a logical "and". 
		// Also update setXmlRoot, so the "hangup" gets put in the right place.
		$newXmlFragment = $xml->createDocumentFragment();
		$newXmlFragment->appendXML($xmlText);

		$xp = new DOMXPath($xml);
		$elements = $xp->query($xml->preUpdate(''));
		$xml->appendSibling($newXmlFragment,$elements->item(0));

		$newcond="/condition";
		foreach ($struct->condition[0]->attributes() AS $attr=>$value) {
			$newcond.="[@$attr=\"$value\"]";
		}
		$xml->setXmlRoot($xml->getExtensionRoot().$newcond);
	} else {
		$xml->replaceWithXml($xmlText);
	}
    }

    public function postRoute() {
    {
        $xml = FreeSWITCH::createExtension('global');

        // This XML code will track the number dialed, the caller ID of the last inbound call and/or some other basic info
        $newXml = <<<XML

      <condition>
        <action application="hash" data="insert/\${domain_name}-spymap/\${caller_id_number}/\${uuid}"/>
        <action application="hash" data="insert/\${domain_name}-last_dial/\${caller_id_number}/\${destination_number}"/>
        <action application="hash" data="insert/\${domain_name}-last_dial/global/\${uuid}"/>
        <action application="set" data="RFC2822_DATE=\${strftime(%a, %d %b %Y %T %z)}"/>
      </condition>
    
XML;

        $xml->replaceWithXml($newXml);
    }

    }
}

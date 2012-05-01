<?php defined('SYSPATH') or die('No direct access allowed.');

class FreeSwitch_NumberCleanup_Driver extends FreeSwitch_Base_Driver {
	public static function conditioning() {
		foreach (Doctrine::getTable("Context")->findAll(Doctrine::HYDRATE_ARRAY) AS $context) {
			$newxml="";
			if (isset($context["plugins"]["numbercleanup"])) {
				foreach ($context["plugins"]["numbercleanup"] as $cleanup) {
					$newxml.="        <condition field=\"$cleanup[numberclean_searchfieldselect]\" break=\"never\" expression=\"$cleanup[numberclean_pattern]\">\n";
					if ($cleanup["numberclean_replacefieldselect"]=="destination_number") {
						$newxml.="            <action application=\"transfer\" data=\"$cleanup[numberclean_newvalue]\"/>\n";
					} else {
						$newxml.="            <action inline=\"true\" application=\"set\" data=\"$cleanup[numberclean_replacefieldselect]=$cleanup[numberclean_newvalue]\"/>\n";
					}
					$newxml.="        </condition>\n";
				}
			}
			$xml = FreeSWITCH::createExtension('numbercleanup',"conditioning","context_".$context["context_id"]);
			if ($newxml=="") {
				$xml->deleteNode();
			} else {
				$xml->replaceWithXml("\n".$newxml."    ");
			}
		}
	}
	public static function delete($obj) { }
	public static function set($obj) {}
}


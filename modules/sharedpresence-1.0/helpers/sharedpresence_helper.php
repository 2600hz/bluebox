<?php defined('SYSPATH') or die('No direct access allowed.');

class sharedpresence_helper
{
	public static function getDBList()
	{
		$returnlist = array();
		$dblist = Doctrine::getTable('SharedPresenceDB')->findAll();
		foreach ($dblist as $shareddb)
		{
			$returnlist[$shareddb->spd_id] = $shareddb->spd_name;
		}
		return $returnlist;
	}
}

?>

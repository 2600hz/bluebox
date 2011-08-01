<?php defined('SYSPATH') or die('No direct access allowed.');

class paging_Plugin extends Bluebox_Plugin
{
	public function viewcreate_update()
	{
        $devices = Doctrine::getTable('Device')->findAll(Doctrine::HYDRATE_ARRAY);
        foreach ($devices as $device)
        {
            $target_objects[$device['device_id']] = $device['name'];
        }
		Event::$data->template->content->devicelist = $target_objects;
	}	
}
?>
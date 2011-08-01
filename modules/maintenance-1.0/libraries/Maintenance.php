<?php defined('SYSPATH') or die('No direct access allowed.');

class Maintenance
{
    public static function trigger()
    {
        $maintenance_event = Doctrine::getTable('MaintenanceEvent')->find('1');

        if (!$maintenance_event)
        {
            $maintenance_event = new MaintenanceEvent;
        }

        $maintenance_event['last_run'] = time();

        $maintenance_event->save();
    }

    public static function conditionalTrigger()
    {
        if (!kohana::config('maintenance.on_requests', FALSE))
        {
            return;
        }
        
        $maintenance_event = Doctrine::getTable('MaintenanceEvent')->find('1');

        if (!$maintenance_event)
        {
            return self::trigger();
        }

        $elapsed = time() - $maintenance_event['last_run'];
        
        if ($elapsed > kohana::config('maintenance.cycle_time', 300))
        {
            self::trigger();
        }
    }
}

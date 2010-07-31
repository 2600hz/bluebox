<?php defined('SYSPATH') or die('No direct access allowed.');

class FreeSwitch_Odbc_Driver extends FreeSwitch_Base_Driver
{
    public static function set($base)
    {
        if ($base instanceof Odbc)
        {
            $xml = FreeSwitch::setSection('odbc');

            $dsn = $base['dsn_name'] . ':' . $base['user'] . ':' .$base['pass'];
            
            $xml->update('/X-PRE-PROCESS[@cmd="set"][@bluebox="odbc_' . $base['odbc_id'] . '"]{@data="odbc_' . $base['odbc_id'] . '=' . $dsn . '"}');
        }
        else if ($base instanceof SipInterface)
        {
            $xml = FreeSwitch::setSection('sofia', 'sipinterface_' . $base['sipinterface_id']);

            if (empty($base['plugins']['odbc']['odbc_id']))
            {
                $xml->deleteNode('/settings/param[@name="odbc-dsn"]');

                return;
            }

            if(!Doctrine::getTable('Odbc')->find($base['plugins']['odbc']['odbc_id']))
            {
                $xml->deleteNode('/settings/param[@name="odbc-dsn"]');

                return;
            }

            $xml->update('/settings/param[@name="odbc-dsn"]{@value="$${odbc_' .$base['plugins']['odbc']['odbc_id'] . '}"}');
        }
    }

    public static function delete($base)
    {
        if ($base instanceof Odbc)
        {
            $xml = FreeSwitch::setSection('odbc');
            
            $xml->deleteNode('/X-PRE-PROCESS[@cmd="set"][@bluebox="odbc_' . $base['odbc_id'] . '"]');

            if (class_exists('SipInterface'))
            {
                $interfaces = Doctrine::getTable('SipInterface')->findAll();
                
                foreach ($interfaces as $interface)
                {
                    if (empty($interface['plugins']['odbc']['odbc_id']))
                    {
                        continue;
                    }

                    if ($interface['plugins']['odbc']['odbc_id'] != $base['odbc_id'])
                    { 
                        continue;
                    }

                    $plugins = $interface['plugins'];

                    unset($plugins['odbc']);

                    $interface['plugins'] = $plugins;

                    $interface->save();

                    $xml = FreeSwitch::setSection('sofia', 'sipinterface_' .$interface['sipinterface_id']);

                    $xml->deleteNode('/settings/param[@name="odbc-dsn"]');
                }
            }
        }
    }
}

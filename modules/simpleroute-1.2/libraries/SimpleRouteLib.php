<?php defined('SYSPATH') or die('No direct access allowed.');

class SimpleRouteLib
{
    public static function importRoutesNewAccount()
    {
        if (strtolower(Router::$controller) != 'accountmanager' OR strtolower(Router::$method) != 'create')
        {
            return;
        }

        Doctrine::getTable('SimpleRoute')->bind(array('Account', array('local' => 'account_id', 'foreign' => 'account_id')), Doctrine_Relation::ONE);

        Doctrine::getTable('SimpleRoute')->getRecordListener()->get('MultiTenant')->setOption('disabled', TRUE);

        Event::$data['SimpleRoute']->fromArray(self::importConfigRoutes());
    }

    public static function importConfigRoutes()
    {
        $outboundPatterns = kohana::config('simpleroute.outbound_patterns');

        if (!is_array($outboundPatterns))
        {
            return;
        }

        // This is the second work arround for the double loading issue... hmmm
        $createdPatterns = array();

        $simpleRoutes = array();

        foreach ($outboundPatterns as $outboundPattern)
        {
            if (empty($outboundPattern['name']))
            {
                continue;
            }

            if (in_array($outboundPattern['name'], $createdPatterns))
            {
                continue;
            }

            $createdPatterns[] = $outboundPattern['name'];

            if (empty($outboundPattern['patterns']))
            {
                continue;
            }

            if (!is_array($outboundPattern['patterns']))
            {
                $outboundPattern['patterns'] = array($outboundPattern['patterns']);
            }

            $simpleRoute = &$simpleRoutes[];

            $simpleRoute['name'] = $outboundPattern['name'];

            $simpleRoute['patterns'] = $outboundPattern['patterns'];
        }

        return $simpleRoutes;
    }
}
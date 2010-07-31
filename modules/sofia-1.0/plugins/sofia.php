<?php defined('SYSPATH') or die('No direct access allowed.');

class Sofia_Plugin extends Bluebox_Plugin
{
    protected $name = 'sofia';

    public function index()
    {
        $this->grid->add('sofia_status', 'Status', array(
                'align' => 'center',
                'search' => false,
                'sortable' => false,
                'callback' => array(
                    'function' => array($this, '_showStatus'),
                    'arguments' => array('plugins', 'User/location_id')
                )
            )
        );
    }

    public function _showStatus($null, $plugins, $location_id)
    {
        if (empty($plugins['sip']))
        {
            return;
        }

        $sip = $plugins['sip'];
        return SofiaManager::isDeviceActive($sip['username'], locations::getLocationDomain($location_id));
        //return $sip['username'] .'@' .locations::getLocationDomain($location_id);
    }
}
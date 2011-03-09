<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * @author Your Name <your@email.org>
 * @license Your License
 * @package _Skeleton
 */
class EndpointManager_Plugin extends Bluebox_Plugin
{
    protected $name = 'endpointdevice';

    public function addPluginData() {
        parent::addPluginData();

        $device = $this->base;
        if((isset($device['plugins']['endpointdevice']['mac_address'])) && ($device['plugins']['endpointdevice']['mac_address'] != "")) {
            include_once(MODPATH . 'endpointmanager-1.1' . DIRECTORY_SEPARATOR . "functions.php");
            $endpoint = new endpointman();
            $phone_info = array();
            if($endpoint->mac_check_clean($device['plugins']['endpointdevice']['mac_address'])) {
                $dns = $device['User']['Location']['domain'];

                $key = explode("|",$device['plugins']['endpointdevice']['brand']);
                $phone_info['brand'] = $key[0];
                $phone_info['family'] = $key[1];
                $phone_info['model'] = $key[2];
                $phone_info['mac'] = $endpoint->mac_check_clean($device['plugins']['endpointdevice']['mac_address']);
                $phone_info['line'][1]['line'] = 1;
                $phone_info['line'][1]['ext'] = $device['plugins']['sip']['username'];
                $phone_info['line'][1]['description'] = $device['plugins']['endpointdevice']['display_name'];
                $phone_info['line'][1]['secret'] = $device['plugins']['sip']['password'];

		$phone_info['host'] = $dns;
		$phone_info['port'] = 5060;

		$phone_info['options'] = array();

		// Is a Redbox in use?
		if (arr::get($device, 'plugins', 'endpointdevice', 'proxy_ip')) {
	                $phone_info['line'][1]['options']['enable_outbound_proxy_server'] = 1;
	                $phone_info['line'][1]['options']['outbound_proxy_server'] = arr::get($device, 'plugins', 'endpointdevice', 'proxy_ip');
              
			$phone_info['line'][1]['options']['outbound_proxy_server_port'] = (($port = arr::get($device, 'plugins', 'endpointdevice', 'proxy_port')) ? $port : 5060);

			$phone_info['options']['voice_vlan_enable'] = (($vlan_enable = arr::get($device, 'plugins', 'endpointdevice', 'vlan')) ? $vlan_enable : '0');
			$phone_info['options']['voice_vlan_id'] = (($voice_vlan = arr::get($device, 'plugins', 'endpointdevice', 'voice_vlan')) ? $voice_vlan : '0');
			$phone_info['options']['data_vlan_enable'] = $vlan_enable;
			$phone_info['options']['data_vlan_id'] = (($data_vlan = arr::get($device, 'plugins', 'endpointdevice', 'data_vlan')) ? $data_vlan : '4095');
		} else {
	                $phone_info['line'][1]['options'] = array();
		}

		$phone_info['options']['update_mode'] = 4;
		$phone_info['options']['update_frequency'] = 5;	// every 5 minutes
 
		$phone_info['timezone'] = "-8";
                $endpoint->prepare_configs($phone_info);
            } else {
                return false;
            }
        }
        return true;
    }

}

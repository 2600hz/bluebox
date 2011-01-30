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
                $phone_info['host'] = $dns;      // Replace this with the DNS location of this tenant/user
                $phone_info['port'] = 5060;
                $phone_info['timezone'] = "-8";
                $endpoint->prepare_configs($phone_info);
            } else {
                return false;
            }
        }
        return true;
    }

}

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

        if((isset($_REQUEST['endpointdevice']['mac_address'])) && ($_REQUEST['endpointdevice']['mac_address'] != "")) {
            include_once(MODPATH . 'endpointmanager-1.1' . DIRECTORY_SEPARATOR . "functions.php");
            $endpoint = new endpointman();
            $phone_info = array();
            if($endpoint->mac_check_clean($_REQUEST['endpointdevice']['mac_address'])) {
                $key = explode("|",$_REQUEST['endpointdevice']['brand']);
                $phone_info['brand'] = $key[0];
                $phone_info['family'] = $key[1];
                $phone_info['model'] = $key[2];
                $phone_info['mac'] = $endpoint->mac_check_clean($_REQUEST['endpointdevice']['mac_address']);
                $phone_info['line'][1]['line'] = 1;
                $phone_info['line'][1]['ext'] = $_REQUEST['sip']['username'];
                $phone_info['line'][1]['description'] = $_REQUEST['callerid']['external_name'];
                $phone_info['line'][1]['secret'] = $_REQUEST['sip']['password'];
                $endpoint->prepare_configs($phone_info);
            } else {
                return false;
            }
        }
        return true;
    }

}

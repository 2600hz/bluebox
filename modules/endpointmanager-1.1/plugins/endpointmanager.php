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


        //$endpoint = Doctrine::getTable('Endpoint')->findOneByMac($mac_address);
        //if (!$endpoint) {
        //    // TODO: Add code to automatically add a new device here, detecting it's model, family and brand based on it's
        //    // UID
        //}

        /*$model = $endpoint->EndpointModel;
        $family = $model->EndpointBrand;
        $brand = $model->EndpointBrand;*/

        // Replace all bogus data below with $endpointdevice['XXX'] from plugins field of basemodel

        $model = array('name' => 'T22');
        $family = array('name' => 't2x');
        $brand = array('name' => 'yealink');

        $class = "endpoint_" . $brand['name'] . "_" . $family['name'] . '_phone';

        $endpoint = new $class();

        //have to because of versions less than php5.3
        $endpoint->brand_name = $brand['name'];
        $endpoint->family_line = $family['name'];

        //Mac Address
        $endpoint->mac = $mac_address;

        //Phone Model (Please reference family_data.xml in the family directory for a list of recognized models)
        $endpoint->model = $model['name'];

        //Timezone
        $endpoint->timezone = 'GMT-11:00';

        //Server IP
        $endpoint->server[1]['ip'] = "10.10.10.10";
        $endpoint->server[1]['port'] = 5060;

        $endpoint->server[2]['ip'] = "20.20.20.20";
        $endpoint->server[2]['port'] = 7000;

        //Provide alternate Configuration file instead of the one from the hard drive
        //$endpoint->config_files_override['$mac.cfg'] = "{\$srvip}\n{\$admin_pass|0}\n{\$test.line.1}";

        //Pretend we have three lines, we could just have one line or 20...whatever the phone supports
        $endpoint->lines[1] = array('ext' => '103', 'secret' => 'blah', 'displayname' => 'Joe Blow', 'vmail' => 'whee');
        $endpoint->lines[2] = array('ext' => '104', 'secret' => 'blah4', 'displayname' => 'Display Name');
        $endpoint->lines[3] = array('ext' => '105', 'secret' => 'blah5', 'displayname' => 'Other Account');

        $endpoint->root_dir = MOD_PATH . 'endpointmanager-1.1' . DIRECTORY_SEPARATOR . 'libraries' . DIRECTORY_SEPARATOR;

        $endpoint->processor_info = 'blue.box and Andrew Nagy, who\'s an INI file ninja';

        //Set Variables according to the template_data files included. We can include different template.xml files within family_data.xml also one can create
        //template_data_custom.xml which will get included or template_data_<model_name>_custom.xml which will also get included
        //line 'global' will set variables that aren't line dependant
        //$endpoint->options =    array("admin_pass" =>  "password","main_icon" => "Main ICON Line #3");
        //Setting a line variable here...these aren't defined in the template_data.xml file yet. however they will still be parsed
        //and if they have defaults assigned in a future template_data.xml or in the config file using pipes (|) those will be used, pipes take precedence


        // Because every brand is an extension (eventually) of endpoint, you know this function will exist regardless of who it is
        $data = $endpoint->generate_config();

        foreach ($data as $filename => $output) {
            // WRITE FILE TO HARD DRIVE HERE
            // echo $output;
        }
        
        return true;
    }

}
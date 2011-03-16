<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
*/

/**
 * Description of functions
 *
 * @author Andrew
 */
class endpointman {
    //put your code here

    public $main_path = "";
    public $mod_path = "";
    public $global_cfg = "";

    function  __construct() {
        if (!defined("PHONE_MODULES_PATH")) {
		define("PHONE_MODULES_PATH", MODPATH . 'endpointmanager-1.1' . DIRECTORY_SEPARATOR . 'libraries' . DIRECTORY_SEPARATOR);
	}
        $this->main_path = MODPATH . 'endpointmanager-1.1' . DIRECTORY_SEPARATOR;
        $this->mod_path = MODPATH . 'endpointmanager-1.1' . DIRECTORY_SEPARATOR . 'libraries' . DIRECTORY_SEPARATOR . 'endpoint' . DIRECTORY_SEPARATOR;
        $this->global_cfg['config_location'] = "/tftpboot/";
    }

    function get_devices_list() {
        $list = array();
        echo "<pre>";
        $master = $this->xml2array($this->mod_path . "master.xml");
        if ($master) foreach($master['data']['brands'] as $data) {
            $list[$data['name']] = array();
            $brands = $this->xml2array($this->mod_path . $data['directory'] . DIRECTORY_SEPARATOR . "brand_data.xml");
            $brand_list = $this->fix_single_array_keys($brands['data']['brands']['family_list']['family']);
            foreach($brand_list as $brandies) {
                $family = $this->xml2array($this->mod_path . $data['directory'] . DIRECTORY_SEPARATOR . $brandies['directory'] . DIRECTORY_SEPARATOR . "family_data.xml");
                $model_list = $this->fix_single_array_keys($family['data']['model_list']);
                $i = 0;
                foreach($model_list as $models) {
                   $list[$data['name']][$data['directory']."|".$brandies['directory']."|".$models['model']] = $models['model'];
                   $i++;
                }
            }
        }
        return $list;
    }

    function prepare_configs($phone_info) {
        //$endpoint = Doctrine::getTable('Endpoint')->findOneByMac($mac_address);
        //if (!$endpoint) {
        //    // TODO: Add code to automatically add a new device here, detecting it's model, family and brand based on it's
        //    // UID
        //}

        /*$model = $endpoint->EndpointModel;
        $family = $model->EndpointBrand;
        $brand = $model->EndpointBrand;*/

        // Replace all bogus data below with $endpointdevice['XXX'] from plugins field of basemodel

        $model = array('name' => $phone_info['model']);
        $family = array('name' => $phone_info['family']);
        $brand = array('name' => $phone_info['brand']);

        $phone_info['directory'] = $phone_info['brand'];
        $phone_info['cfg_dir'] = $phone_info['family'];

        $class = "endpoint_" . $brand['name'] . "_" . $family['name'] . '_phone';

        $provisioner_lib = new $class();

        //have to because of versions less than php5.3
        $provisioner_lib->brand_name = $brand['name'];
        $provisioner_lib->family_line = $family['name'];

        //Mac Address
        $provisioner_lib->mac = $phone_info['mac'];

        //Phone Model (Please reference family_data.xml in the family directory for a list of recognized models)
        $provisioner_lib->model = $model['name'];

        //Timezone
        $provisioner_lib->timezone = date_offset_get(new DateTime);

        //Server IP
        if (isset($phone_info['host'])) {
            $provisioner_lib->server[1]['ip'] = $phone_info['host'];
        } else {
            $provisioner_lib->server[1]['ip'] = $_SERVER["SERVER_ADDR"];
        }

        if (isset($phone_info['port'])) {
            $provisioner_lib->server[1]['port'] = $phone_info['port'];
        } else {
            $provisioner_lib->server[1]['port'] = 5060;
        }

	$provisioner_lib->options = $phone_info['options'];

        //Provide alternate Configuration file instead of the one from the hard drive
        //$endpoint->config_files_override['$mac.cfg'] = "{\$srvip}\n{\$admin_pass|0}\n{\$test.line.1}";

        //Pretend we have three lines, we could just have one line or 20...whatever the phone supports
        foreach($phone_info['line'] as $line) {
            $provisioner_lib->lines[$line['line']] = array('ext' => $line['ext'], 'secret' => $line['secret'], 'displayname' => $line['description'], 'options' => $line['options']);
        }

        $provisioner_lib->root_dir = MODPATH . 'endpointmanager-1.1' . DIRECTORY_SEPARATOR . 'libraries' . DIRECTORY_SEPARATOR;

        $provisioner_lib->processor_info = 'Endpoint Manager 1.1 for Blue.Box';

        //Set Variables according to the template_data files included. We can include different template.xml files within family_data.xml also one can create
        //template_data_custom.xml which will get included or template_data_<model_name>_custom.xml which will also get included
        //line 'global' will set variables that aren't line dependant
        //$endpoint->options =    array("admin_pass" =>  "password","main_icon" => "Main ICON Line #3");
        //Setting a line variable here...these aren't defined in the template_data.xml file yet. however they will still be parsed
        //and if they have defaults assigned in a future template_data.xml or in the config file using pipes (|) those will be used, pipes take precedence


        // Because every brand is an extension (eventually) of endpoint, you know this function will exist regardless of who it is
        $data = $provisioner_lib->generate_config();

        //Start timer
        $time_start = microtime(true);
        $returned_data = $provisioner_lib->generate_config();
        //End timer
        $time_end = microtime(true);
        $time = $time_end - $time_start;
        if($time > 360) {
            $this->error['generate_time'] = "It's taking a awfully long time to generate configs...(".round($time,2)." seconds)";
        }
        //Create Directory Structure (If needed)
        if(isset($provisioner_lib->directory_structure)) {
            foreach($provisioner_lib->directory_structure as $data) {
                $dir = $this->global_cfg['config_location'] . $data;
                if(!file_exists($dir)) {
                    mkdir($dir, 0777);
                }
            }
        }

        //Copy Files/Directories (If needed)
        if(isset($provisioner_lib->copy_files)) {
            foreach($provisioner_lib->copy_files as $data) {
                if(((file_exists($this->global_cfg['config_location'].$data)) AND (!in_array($data,$provisioner_lib->protected_files))) OR (!file_exists($this->global_cfg['config_location'].$data))) {
                    if(is_dir(PHONE_MODULES_PATH."endpoint/".$phone_info['directory']."/".$phone_info['cfg_dir']."/".$data)) {
                        if(!file_exists($this->global_cfg['config_location'].$data)) {
                            if (!@mkdir($this->global_cfg['config_location'].$data, 0777)) {
                                $this->error['parse_configs'] = "Could Not Create Directory: ".$data;
                            }
                        }
                        $dir_iterator = new RecursiveDirectoryIterator(PHONE_MODULES_PATH."endpoint/".$phone_info['directory']."/".$phone_info['cfg_dir']."/".$data."/");
                        $iterator = new RecursiveIteratorIterator($dir_iterator, RecursiveIteratorIterator::SELF_FIRST);
                        // could use CHILD_FIRST if you so wish
                        foreach ($iterator as $file) {
                            if(is_dir($file)) {
                                $dir = str_replace(PHONE_MODULES_PATH."endpoint/".$phone_info['directory']."/".$phone_info['cfg_dir']."/".$data."/", "", $file);
                                if(!file_exists($this->global_cfg['config_location'].$data."/".$dir)) {
                                    if (!@mkdir($this->global_cfg['config_location'].$data."/".$dir, 0777)) {
                                        $this->error['parse_configs'] = "Could Not Create Directory: ".$data."/".$dir;
                                    }
                                }
                            } else {
                                $dir = str_replace(PHONE_MODULES_PATH."endpoint/".$phone_info['directory']."/".$phone_info['cfg_dir']."/".$data."/", "", $file);
                                if(!@copy($file, $this->global_cfg['config_location'].$data."/".$dir)) {
                                   $this->error['parse_configs'] = "Could Not Copy File: ".$data."/".$dir;
                                } else {
                                    chmod($this->global_cfg['config_location'].$data."/".$dir, 0777);
                                }
                            }
                        }
                    } else {
                        copy(PHONE_MODULES_PATH."endpoint/".$phone_info['directory']."/".$phone_info['cfg_dir']."/".$data,$this->global_cfg['config_location'].$data);
                        chmod($this->global_cfg['config_location'].$data, 0777);
                    }
                }
            }
        }

        //Generate Files
        foreach($returned_data as $key => $data) {
            if(((file_exists($this->global_cfg['config_location'].$key)) AND (!in_array($key,$provisioner_lib->protected_files))) OR (!file_exists($this->global_cfg['config_location'].$key))) {
                $fp = fopen($this->global_cfg['config_location'].$key, 'w');
                fwrite($fp, $data);
                fclose($fp);
                if(!file_exists($this->global_cfg['config_location'].$key)) {
                    $this->error['parse_configs'] = "File not written to hard drive!";
                }
            }
        }
    }

    /**
     * This function takes a string and tries to determine if it's a valid mac addess, return FALSE if invalid
     * @param string $mac The full mac address
     * @return mixed The cleaned up MAC is it was a MAC or False if not a mac
     */
    function mac_check_clean($mac) {
        if ((strlen($mac) == "17") OR (strlen($mac) == "12")) {
            //It might be better to use switch here instead of these IF statements...

            //Is the mac separated by colons(:)?
            if (preg_match("/[0-9a-f][0-9a-f][:-]".
            "[0-9a-f][0-9a-f][:-]".
            "[0-9a-f][0-9a-f][:-]".
            "[0-9a-f][0-9a-f][:-]".
            "[0-9a-f][0-9a-f][:-]".
            "[0-9a-f][0-9a-f]/i", $mac)) {
                return(strtoupper(str_replace(":", "", $mac)));
                //Is the string exactly 12 characters?
            } elseif(strlen($mac) == "12") {
                //Now is the string a valid HEX mac address?
                if (preg_match("/[0-9a-f][0-9a-f]".
                "[0-9a-f][0-9a-f]".
                "[0-9a-f][0-9a-f]".
                "[0-9a-f][0-9a-f]".
                "[0-9a-f][0-9a-f]".
                "[0-9a-f][0-9a-f]/i", $mac)) {
                    return(strtoupper($mac));
                } else {
                    return(FALSE);
                }
                //Is the mac separated by whitespaces?
            } elseif(preg_match("/[0-9a-f][0-9a-f][\s]".
            "[0-9a-f][0-9a-f][\s]".
            "[0-9a-f][0-9a-f][\s]".
            "[0-9a-f][0-9a-f][\s]".
            "[0-9a-f][0-9a-f][\s]".
            "[0-9a-f][0-9a-f]/i", $mac)) {
                return(strtoupper(str_replace(" ", "", $mac)));
            } else {
                return(FALSE);
            }
        } else {
            return(FALSE);
        }
    }

    function fix_single_array_keys($array) {
        if (!is_array($array))
        {
            return $array;
        }

        if((empty($array[0])) AND (!empty($array)))
        {
            $array_n[0] = $array;

            return($array_n);
        }

        return empty($array) ? '' : $array;

        /*
        if((empty($array[0])) AND (!empty($array))) {
            $array_n[0] = $array;
            return($array_n);
        } elseif(!empty($array)) {
            return($array);
        //This is so stupid?! PHP gets confused.
        } elseif($array == '0') {
            return($array);
        } else {
            return("");
        }
         * *
         */
    }
    
    function xml2array($url, $get_attributes = 1, $priority = 'tag') {
        $contents = "";
        if (!function_exists('xml_parser_create')) {
            return array();
        }
        $parser = xml_parser_create('');
        if (!($fp = @ fopen($url, 'rb'))) {
            return array();
        }
        while (!feof($fp)) {
            $contents .= fread($fp, 8192);
        }
        fclose($fp);
        xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, "UTF-8");
        xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
        xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
        xml_parse_into_struct($parser, trim($contents), $xml_values);
        xml_parser_free($parser);
        if (!$xml_values) {
            return; //Hmm...
        }
        $xml_array = array();
        $parents = array();
        $opened_tags = array();
        $arr = array();
        $current = & $xml_array;
        $repeated_tag_index = array();
        foreach ($xml_values as $data) {
            unset($attributes, $value);
            extract($data);
            $result = array();
            $attributes_data = array();
            if (isset($value)) {
                if ($priority == 'tag') {
                    $result = $value;
                } else {
                    $result['value'] = $value;
                }
            }
            if (isset($attributes) and $get_attributes) {
                foreach ($attributes as $attr => $val) {
                    if ($priority == 'tag') {
                        $attributes_data[$attr] = $val;
                    } else {
                        $result['attr'][$attr] = $val; //Set all the attributes in a array called 'attr'
                    }
                }
            }
            if ($type == "open") {
                $parent[$level - 1] = & $current;
                if (!is_array($current) or (!in_array($tag, array_keys($current)))) {
                    $current[$tag] = $result;
                    if ($attributes_data) {
                        $current[$tag . '_attr'] = $attributes_data;
                    }
                    $repeated_tag_index[$tag . '_' . $level] = 1;
                    $current = & $current[$tag];
                } else {
                    if (isset($current[$tag][0])) {
                        $current[$tag][$repeated_tag_index[$tag . '_' . $level]] = $result;
                        $repeated_tag_index[$tag . '_' . $level]++;
                    } else {
                        $current[$tag] = array($current[$tag], $result);
                        $repeated_tag_index[$tag . '_' . $level] = 2;
                        if (isset($current[$tag . '_attr'])) {
                            $current[$tag]['0_attr'] = $current[$tag . '_attr'];
                            unset($current[$tag . '_attr']);
                        }
                    }
                    $last_item_index = $repeated_tag_index[$tag . '_' . $level] - 1;
                    $current = & $current[$tag][$last_item_index];
                }
            } else if ($type == "complete") {
                if (!isset($current[$tag])) {
                    $current[$tag] = $result;
                    $repeated_tag_index[$tag . '_' . $level] = 1;
                    if ($priority == 'tag' and $attributes_data) {
                        $current[$tag . '_attr'] = $attributes_data;
                    }
                } else {
                    if (isset($current[$tag][0]) and is_array($current[$tag])) {
                        $current[$tag][$repeated_tag_index[$tag . '_' . $level]] = $result;
                        if ($priority == 'tag' and $get_attributes and $attributes_data) {
                            $current[$tag][$repeated_tag_index[$tag . '_' . $level] . '_attr'] = $attributes_data;
                        }
                        $repeated_tag_index[$tag . '_' . $level]++;
                    } else {
                        $current[$tag] = array($current[$tag], $result);
                        $repeated_tag_index[$tag . '_' . $level] = 1;
                        if ($priority == 'tag' and $get_attributes) {
                            if (isset($current[$tag . '_attr'])) {
                                $current[$tag]['0_attr'] = $current[$tag . '_attr'];
                                unset($current[$tag . '_attr']);
                            }
                            if ($attributes_data) {
                                $current[$tag][$repeated_tag_index[$tag . '_' . $level] . '_attr'] = $attributes_data;
                            }
                        }
                        $repeated_tag_index[$tag . '_' . $level]++; //0 and 1 index is already taken
                    }
                }
            } else if ($type == 'close') {
                $current = & $parent[$level - 1];
            }
        }
        return ($xml_array);
    }

}
?>

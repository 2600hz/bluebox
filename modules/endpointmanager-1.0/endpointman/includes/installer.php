<?PHP
/**
 * Endpoint Manager Install File
 *
 * @author Andrew Nagy
 * @license MPL / GPLv2 / LGPL
 * @package Provisioner
 */
require 'functions.inc';

$endpoint = new endpointmanager();

function out($text){
    echo $text."<br />";
}

if($_REQUEST['type'] == "brand") {
    $endpoint->update_brand($_REQUEST['id']);
} elseif($_REQUEST['type'] == "js-multiple") {
    $list = explode(",",$_REQUEST['id']);
    sort($list,SORT_STRING);

    foreach($list as $data) {

    }
} elseif($_REQUEST['type'] == "firmware") {
    $endpoint->install_firmware($data['id']);
} elseif($_REQUEST['type'] == "manual_install") {
    switch($_REQUEST['install_type']) {
        case "upload_master_xml":
            if (file_exists(PHONE_MODULES_PATH."temp/master.xml")) {
                $handle = fopen(PHONE_MODULES_PATH."temp/master.xml", "rb");
                $contents = stream_get_contents($handle);
                fclose($handle);
                @$a = simplexml_load_string($contents);
                if($a===FALSE) {
                    echo "Not a valid xml file";
                    break;
                } else {
                    rename(PHONE_MODULES_PATH."temp/master.xml", PHONE_MODULES_PATH."master.xml");
                    echo "Move Successful<br />";
                    $endpoint->brand_update_check();
                    echo "Updating Brands<br />";
                }
            } else {
            }
            break;
        case "upload_provisioner":
            if (file_exists(PHONE_MODULES_PATH."temp/".$_REQUEST['package'])) {
                echo "Extracting Provisioner Package <br />";
                exec("tar -xvf ".PHONE_MODULES_PATH.'temp/'. $_REQUEST['package'] ." -C ".PHONE_MODULES_PATH."temp/");

                if(!file_exists(PHONE_MODULES_PATH."endpoint")) {
                    echo "Creating Provisioner Directory <br />";
                    mkdir(PHONE_MODULES_PATH."endpoint");
                }

                $endpoint_last_mod = filemtime(PHONE_MODULES_PATH."temp/endpoint/base.php");

                //rename(PHONE_MODULES_PATH."temp/setup.php", PHONE_MODULES_PATH."setup.php");

                rename(PHONE_MODULES_PATH."temp/endpoint/base.php", PHONE_MODULES_PATH."endpoint/base.php");

                echo "Updating Last Modified <br />";
                $sql = "UPDATE endpointman_global_vars SET value = '".$endpoint_last_mod."' WHERE var_name = 'endpoint_vers'";
                $endpoint->db->query($sql);
            }
            break;
        case "upload_brand":
            if ((file_exists(PHONE_MODULES_PATH."temp/".$_REQUEST['package'])) AND (file_exists(PHONE_MODULES_PATH."temp/".$_REQUEST['xml']))) {
                $temp = $endpoint->xml2array(PHONE_MODULES_PATH."temp/".$_REQUEST['xml']);

                $brand_name = $temp['data']['brands']['name'];
                $brand_id = $temp['data']['brands']['brand_id'];
                $brand_version = $temp['data']['brands']['version'];
                $package = $temp['data']['brands']['package'];
                $brand_directory = $temp['data']['brands']['directory'];

                if(!file_exists(PHONE_MODULES_PATH."endpoint/".$brand_directory)) {
                    echo "Creating ".$brand_name." Directory <br />";
                    mkdir(PHONE_MODULES_PATH."endpoint/".$brand_directory);
                }

                rename(PHONE_MODULES_PATH."temp/".$_REQUEST['xml'], PHONE_MODULES_PATH."endpoint/".$brand_directory."/brand_data.xml");

                echo "Extracting Tarball........";
                exec("tar -xvf ".PHONE_MODULES_PATH.'temp/'. $package ." -C ".PHONE_MODULES_PATH."temp/");
                echo "Done!<br />";

                echo "Creating Directory Structure/Moving Files...............";
                $dir_iterator = new RecursiveDirectoryIterator(PHONE_MODULES_PATH."temp/".$brand_directory."/");
                $iterator = new RecursiveIteratorIterator($dir_iterator, RecursiveIteratorIterator::SELF_FIRST);
                // could use CHILD_FIRST if you so wish

                foreach ($iterator as $file) {
                    if(is_dir($file)) {
                        $dir = str_replace(PHONE_MODULES_PATH."temp/".$brand_directory."/", "", $file);
                        if(!file_exists(PHONE_MODULES_PATH."endpoint/".$brand_directory."/".$dir)) {
                            mkdir(PHONE_MODULES_PATH."endpoint/".$brand_directory."/".$dir);
                        }
                    } else {
                        if(basename($file) != "brand_data.xml") {
                            $dir = str_replace(PHONE_MODULES_PATH."temp/".$brand_directory."/", "", $file);
                            rename($file, PHONE_MODULES_PATH."endpoint/".$brand_directory."/".$dir);
                        }
                    }
                }
                echo "Done!<br />";
                echo "Removing Temporary Files..............";
                $endpoint->deltree(PHONE_MODULES_PATH."temp/" .$brand_directory);
                unlink(PHONE_MODULES_PATH.'temp/'. $_REQUEST['package']);
                echo "Done!<br />";

                $last_mod = "";
                $temp['data']['brands']['family_list']['family'] = $endpoint->fix_single_array_keys($temp['data']['brands']['family_list']['family']);
                foreach($temp['data']['brands']['family_list']['family'] as $family_list) {
                    echo "Updating Family Lines.................<br/>";
                    $last_mod = max($last_mod, $family_list['last_modified']);

                    $family_line_xml = $endpoint->xml2array(PHONE_MODULES_PATH.'/endpoint/'.$brand_directory.'/'.$family_list['directory'].'/family_data.xml');
                    $data =& $endpoint->db->getOne("SELECT id FROM endpointman_product_list WHERE id='".$brand_id.$family_line_xml['data']['id']."'", array(), DB_FETCHMODE_ASSOC);
                    $short_name = preg_replace("/\[(.*?)\]/si", "", $family_line_xml['data']['name']);
                    if($data) {
                        $sql = "UPDATE endpointman_product_list SET short_name = '".$short_name."', long_name = '".$family_line_xml['data']['name']."', cfg_ver = '".$family_line_xml['data']['version']."', config_files='".$family_line_xml['data']['configuration_files']."', hidden = '0' WHERE id = '".$brand_id.$family_line_xml['data']['id']."'";
                    } else {
                        $sql = "INSERT INTO endpointman_product_list (`id`, `brand`, `short_name`, `long_name`, `cfg_dir`, `cfg_ver`, `config_files`, `hidden`) VALUES ('".$brand_id.$family_line_xml['data']['id']."', '".$brand_id."', '".$short_name."', '".$family_line_xml['data']['name']."', '".$family_line_xml['data']['directory']."', '".$family_line_xml['data']['version']."','".$family_line_xml['data']['configuration_files']."', '0')";
                    }

                    $endpoint->db->query($sql);
                    $family_line_xml['data']['model_list'] = $endpoint->fix_single_array_keys($family_line_xml['data']['model_list']);
                    echo "--Updating Model Lines................<br/>";
                    foreach($family_line_xml['data']['model_list'] as $model_list) {
                        if(is_array($model_list['template_data']['files'])) {
                            $template_list = implode(",",$model_list['template_data']['files']);
                        } else {
                            $template_list = $model_list['template_data']['files'];
                        }
                        $m_data =& $endpoint->db->getOne("SELECT id FROM endpointman_model_list WHERE id='".$brand_id.$family_line_xml['data']['id'].$model_list['id']."'", array(), DB_FETCHMODE_ASSOC);
                        if($m_data) {
                            $sql = "UPDATE endpointman_model_list SET model = '".$model_list['model']."', template_list = '".$template_list."', enabled = '0', hidden = '0' WHERE id = '".$brand_id.$family_line_xml['data']['id']."'";
                        } else {
                            $sql = "INSERT INTO endpointman_model_list (`id`, `brand`, `model`, `product_id`, `template_list`, `enabled`, `hidden`) VALUES ('".$brand_id.$family_line_xml['data']['id'].$model_list['id']."', '".$brand_id."', '".$model_list['model']."', '".$brand_id.$family_line_xml['data']['id']."', '".$template_list."', '0', '0')";
                        }
                        $endpoint->db->query($sql);
                    }
                }

                $row =& $endpoint->db->getAll('SELECT * FROM  endpointman_brand_list WHERE id > 0', array(), DB_FETCHMODE_ASSOC);


                if(file_exists(PHONE_MODULES_PATH."endpoint/".$brand_directory."/brand_data.xml")) {
                    $temp = $endpoint->xml2array(PHONE_MODULES_PATH."endpoint/".$brand_directory."/brand_data.xml");

                    $temp = $temp['data']['brands'];

                    $temp['oui_list']['oui'] = $endpoint->fix_single_array_keys($temp['oui_list']['oui']);

                    foreach($temp['oui_list']['oui'] as $oui) {
                        $sql = "INSERT INTO endpointman_oui_list (`oui`, `brand`, `custom`) VALUES ('".$oui."', '".$temp['brand_id']."', '0')";
                        $endpoint->db->query($sql);

                    }

                    $brand_name = $temp['directory'];
                    $version[$brand_name] = $temp['last_modified'];

                    $last_mod = "";

                    $temp['family_list']['family'] = $endpoint->fix_single_array_keys($temp['family_list']['family']);

                    foreach($temp['family_list']['family'] as $list) {
                        $last_mod = max($last_mod, $list['last_modified']);
                    }
                    $last_mod = max($last_mod, $version[$brand_name]);

                    $version[$brand_name] = $last_mod;

                    if(!($endpoint->arraysearchrecursive($brand_name, $row, 'directory'))) {
                        //insert row
                        $sql = "INSERT INTO endpointman_brand_list (id, name, directory, cfg_ver, installed) VALUES ('".$temp['brand_id']."', '".$temp['name']."', '".$temp['directory']."', '".$version[$brand_name]."', 1)";
                        $endpoint->db->query($sql);
                    } else {
                        $brand_version = max($last_mod, $brand_version);
                        echo "Updating data..........";
                        $sql = "UPDATE endpointman_brand_list SET name = '".$temp['name']."', cfg_ver = '".$version[$brand_name]."', installed = 1, hidden = 0 WHERE id = ".$temp['brand_id'];
                        $endpoint->db->query($sql);
                        echo "Done!<br/>";
                    }
                }
            }
            break;
    }
}

echo "<hr>\n\t<a href=\"#\" onclick=\"parent.close_module_actions(true);\" style=\"text-decoration:none\" />"._("Return")."</a>";
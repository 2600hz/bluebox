<?PHP
/**
 * Endpoint Manager Devices Manager File
 *
 * @author Andrew Nagy
 * @license MPL / GPLv2 / LGPL
 * @package Provisioner
 */

//Set opened variables
$message = NULL;
$no_add = FALSE;
$final = NULL;
$button = NULL;
$searched = NULL;
$edit = NULL;
$mode = NULL;

$family_list =& $endpoint->db->getAll("SELECT * FROM endpointman_product_list WHERE id > 0",array(),DB_FETCHMODE_ASSOC);

$mac_list =& $endpoint->db->getAll("SELECT * FROM endpointman_mac_list",array(),DB_FETCHMODE_ASSOC);

if((!$family_list) && (!$mac_list)) {
    $message = _("Welcome to Endpoint Manager")."!<br />"._("You have no products (Modules) installed, click")." <a href=\"config.php?display=epm_config&type=$type\">"._("here")."</a> "._("to install some");
    $no_add = TRUE;
} elseif(!$family_list) {
    $message = "Thanks for upgrading to version 2.0! Please head on over to <a href=\"config.php?display=epm_config&type=$type\">\"Brand Configurations/Setup\"</a> to setup and install phone configurations";
    $no_add = TRUE;
} elseif($endpoint->global_cfg['srvip'] == "") {
    $message = "Your Global Variables are not set! Please head on over to <a href=\"config.php?type=$type&display=epm_advanced\">\"Advanced Settings\"</a> to setup your configuration";
    $no_add = TRUE;
}

if(isset($_REQUEST['button_add'])) {
    if($mac = $endpoint->mac_check_clean($_REQUEST['mac'])) {
        $sql = 'SELECT `name` FROM `users` WHERE `extension` = '.$_REQUEST['ext_list'];
        $name =& $endpoint->db->getOne($sql);

        $endpoint->sync_model($_REQUEST['model_list']);

        $sql = 'SELECT endpointman_product_list. * , endpointman_model_list.template_data, endpointman_brand_list.directory FROM endpointman_model_list, endpointman_brand_list, endpointman_product_list WHERE endpointman_model_list.id =  \''.$_REQUEST['model_list'].'\' AND endpointman_model_list.brand = endpointman_brand_list.id AND endpointman_model_list.product_id = endpointman_product_list.id';
        
        $row =& $endpoint->db->getRow($sql,array(),DB_FETCHMODE_ASSOC);

        $cfg_data = unserialize($row['template_data']);

        $count = count($cfg_data);
        $i = 0;
        while($i < $count) {
            if(array_key_exists('variable',$cfg_data[$i])) {
                $temping = str_replace('$','',$cfg_data[$i]['variable']);
                if(is_array($cfg_data[$i]['default_value'])) {
                    $custom_cfg_data[$temping]['value'] = '';
                } else {
                    $custom_cfg_data[$temping]['value'] = $cfg_data[$i]['default_value'];
                }
            }
            $i++;
        }
        if(is_array($custom_cfg_data)) {
            $custom_cfg_data = serialize($custom_cfg_data);
        } else {
            $temp = array();
            $custom_cfg_data = serialize($temp);
        }

        $sql = "INSERT INTO `endpointman_mac_list` (`mac`, `model`, `ext`, `description`, `custom_cfg_data`, `custom_cfg_template`) VALUES ('".$mac."', '".$_REQUEST['model_list']."', '".$_REQUEST['ext_list']."', '".$name."', '".$custom_cfg_data."', '".$_REQUEST['template_list']."')";
        $endpoint->db->query($sql);

        $sql = 'SELECT id FROM endpointman_mac_list WHERE ext ='. $_REQUEST['ext_list'];
        $ext_id =& $endpoint->db->getOne($sql);

        $mac_id = $endpoint->get_phone_info($ext_id);

        $endpoint->prepare_configs($mac_id);

        $message = _("Saved")."!";
    } else {
        $message = _("Invalid MAC Address")."!";
    }
} elseif(isset($_REQUEST['editready'])) {
    $edit_row=$endpoint->get_phone_info($_REQUEST['id']);
    $edit_row['id'] = $_REQUEST['id'];
    $mode = "EDIT";
} elseif(isset($_REQUEST['button_edit'])) {

    if(empty($_REQUEST['edit_id'])) {
        $message = _("No Device Selected to Edit!")."!";
    } else {
        $template_editor = TRUE;
        $sql = "UPDATE  endpointman_mac_list SET  model =  '".$_REQUEST['model_list']."' WHERE  id =".$_REQUEST['edit_id'];
        $endpoint->db->query($sql);
        if ($_REQUEST['template_list'] == 0) {
            $endpoint->edit_template_display($_REQUEST['edit_id'],1);
        } else {
            $endpoint->edit_template_display($_REQUEST['template_list'],0);
        }
    }
} elseif(isset($_REQUEST['button_save'])) {
    $sql = "SELECT name FROM users WHERE extension = ".$_REQUEST['ext_list'];
    $name=$endpoint->db->getOne($sql);

    $sql = "UPDATE endpointman_mac_list SET custom_cfg_template = '".$_REQUEST['template_list']."', model = '".$_REQUEST['model_list']."', ext = ".$_REQUEST['ext_list'].", description = '".$name."' WHERE id =  ". $_REQUEST['edit_id'];
    $endpoint->db->query($sql);

    $row = $endpoint->get_phone_info($_REQUEST['edit_id']);

    $endpoint->prepare_configs($row);

    $message = _("Saved")."!";
} elseif(isset($_REQUEST['deleteready'])) {
    $row = $endpoint->get_phone_info($_REQUEST['id']);

    $sql = "DELETE FROM endpointman_mac_list WHERE id=".$_REQUEST['id'];
    $endpoint->db->query($sql);
} elseif((isset($_REQUEST['button_add_selected_phones'])) AND (isset($_REQUEST['add']))) {
    $list_num = count($_REQUEST['add']);
    for($i=0;$i<$list_num;$i++) {
        $num = $_REQUEST['add'][$i];
        if($mac = $endpoint->mac_check_clean($_REQUEST['mac_'.$num])) {
            $sql = "SELECT * FROM endpointman_mac_list WHERE ext = ".$_REQUEST['ext_list_'.$num];
            $res =& $endpoint->db->query($sql);
            $count_macs = $res->numRows();
            if(!$count_macs) {
                $sql = 'SELECT `name` FROM `users` WHERE `extension` = '.$_REQUEST['ext_list_'.$num];
                $name =& $endpoint->db->getOne($sql,array(),DB_FETCHMODE_ASSOC);


                $sql = 'SELECT endpointman_product_list. * , endpointman_brand_list.directory FROM endpointman_model_list, endpointman_brand_list, endpointman_product_list WHERE endpointman_model_list.id =  \''.$_REQUEST['model_list_'.$num].'\' AND endpointman_model_list.brand = endpointman_brand_list.id AND endpointman_model_list.product_id = endpointman_product_list.id';
                $row =& $endpoint->db->getAll($sql,array(),DB_FETCHMODE_ASSOC);


                $sql = "INSERT INTO `endpointman_mac_list` (`mac`, `model`, `ext`, `description`) VALUES ('".$mac."', '".$_REQUEST['model_list_'.$num]."', '".$_REQUEST['ext_list_'.$num]."', '".$name."')";
                $endpoint->db->query($sql);

                $sql = 'SELECT id FROM endpointman_mac_list WHERE ext ='. $_REQUEST['ext_list_'.$num];
                $mac_new_id =& $endpoint->db->getOne($sql,array(),DB_FETCHMODE_ASSOC);

                $row_new = $endpoint->get_phone_info($mac_new_id);
                $endpoint->prepare_configs($row_new);
 
                $message = _("Saved")."!";
            } else {
                $message = _("One or more selected phones had the same extension as a phone already added, these devices were not added");
            }

        } else {
            $message = _("Invalid MAC Address");
        }
    }
} elseif((isset($_REQUEST['button_update_phones'])) AND (isset($_REQUEST['selected']))) {
    if(($_REQUEST['brand_list_selected'] > 0) AND ($_REQUEST['model_list_selected'] > 0)) {

        foreach($_REQUEST['selected'] as $key => $data) {
            $sql = "UPDATE endpointman_mac_list SET custom_cfg_data = '', custom_cfg_template = '', user_cfg_data = '', config_files_override = '', model = '".$_REQUEST['model_list_selected']."' WHERE id =  ". $_REQUEST['selected'][$key];
            $endpoint->db->query($sql);

            $phone_info = $endpoint->get_phone_info($_REQUEST['selected'][$key]);
            $endpoint->prepare_configs($phone_info);

            $message = _("Saved")."!";

        }
    } else {
        $message = _("Please select a Brand and/or Model");
    }
} elseif(isset($_REQUEST['button_delete_selected_phones'])) {
    if(isset($_REQUEST['selected'])) {
        foreach($_REQUEST['selected'] as $key => $data) {
            $sql = "DELETE FROM endpointman_mac_list WHERE id=".$_REQUEST['selected'][$key];
            $endpoint->db->query($sql);
        }
    } else {
        $message = _("No Phones Selected")."!";
    }
} elseif(isset($_REQUEST['button_go'])) {
    if ($_REQUEST['nmap'] == 1) {
        $temp = $endpoint->discover_new($_REQUEST['netmask']);
    } else {
        $temp = $endpoint->discover_new($_REQUEST['netmask'], FALSE);
    }
    $count = count($temp);
    $i = 0;
    while($i < $count) {
        if (!$temp[$i]['endpoint_managed']) {
            if ($temp[$i]['brand']) {
                $final[$i] = $temp[$i];
                $final[$i]['id'] = $i;
                $sqln = "SELECT * FROM endpointman_model_list WHERE enabled = 1 AND brand =".$temp[$i]['brand_id'];
                $model_list =& $endpoint->db->getAll($sqln,array(),DB_FETCHMODE_ASSOC);
                $j = 0;
                foreach($model_list as $row) {
                    $final[$i]['list'][$j] = $row;
                    $j++;
                }
            }
        }
        $i++;
    }
    if($final) {
        $final = array_values($final);
    } else {
        $final = NULL;
        $message = _("No Devices Found");
    }
    $searched = 1;
} elseif(isset($_REQUEST['button_rebuild_configs_for_all_phones'])) {
    $sql = "SELECT endpointman_mac_list.id FROM endpointman_mac_list, endpointman_brand_list, endpointman_product_list, endpointman_model_list WHERE endpointman_brand_list.id = endpointman_product_list.brand AND endpointman_product_list.id = endpointman_model_list.product_id AND endpointman_mac_list.model = endpointman_model_list.id ORDER BY endpointman_product_list.cfg_dir ASC";
    $mac_list =& $endpoint->db->getAll($sql,array(),DB_FETCHMODE_ASSOC);
    foreach($mac_list as $data) {
        $phone_info = $endpoint->get_phone_info($data['id']);
        $endpoint->prepare_configs($phone_info);
    }
} elseif(isset($_REQUEST['button_rebuild_reboot'])) {
    if($_REQUEST['product_select'] == "") {
        $message = _("Please select a product");
    } elseif($_REQUEST['template_selector'] == "") {
        $message = _("Please select a template");
    } else {
        $sql = "SELECT endpointman_mac_list.id FROM endpointman_mac_list, endpointman_brand_list, endpointman_product_list, endpointman_model_list WHERE endpointman_brand_list.id = endpointman_product_list.brand AND endpointman_product_list.id = endpointman_model_list.product_id AND endpointman_mac_list.model = endpointman_model_list.id AND endpointman_product_list.id = '".$_REQUEST['product_select']."'";
        $data =& $endpoint->db->getAll($sql,array(),DB_FETCHMODE_ASSOC);
        foreach($data as $row) {
            $sql = "UPDATE endpointman_mac_list SET custom_cfg_template = '".$_REQUEST['template_selector']."' WHERE id =  ". $row['id'];
            $endpoint->db->query($sql);
            $phone_info = $endpoint->get_phone_info($row['id']);
            $endpoint->prepare_configs($phone_info);
        }
    }
} elseif(isset($_REQUEST['button_rebuild_selected'])) {
    if(isset($_REQUEST['selected'])) {
        foreach($_REQUEST['selected'] as $key => $data) {
            $phone_info = $endpoint->get_phone_info($_REQUEST['selected'][$key]);
            $endpoint->prepare_configs($phone_info);
        }
    } else {
        $message = _("No Phones Selected")."!";
    }
} elseif(isset($_REQUEST['button_reboot_this_brand'])) {
    if($_REQUEST['rb_brand'] != "") {
        $sql = 'SELECT endpointman_mac_list.id FROM endpointman_mac_list , endpointman_model_list , endpointman_brand_list , endpointman_product_list WHERE endpointman_brand_list.id = endpointman_model_list.brand AND endpointman_model_list.id = endpointman_mac_list.model AND endpointman_model_list.product_id = endpointman_product_list.id AND endpointman_brand_list.id = '.$_REQUEST['rb_brand'].' ORDER BY endpointman_product_list.cfg_dir ASC';
        $data =& $endpoint->db->getAll($sql,array(),DB_FETCHMODE_ASSOC);
        foreach($data as $row) {
            if(!class_exists('ProvisionerConfig')) {
                require(PHONE_MODULES_PATH.'setup.php');
            }
            $phone_info = $endpoint->get_phone_info($row['id']);
            if(is_array($phone_info['template_data'])) {
                $template_data = unserialize($phone_info['template_data']['custom_cfg_data']);
            } else {
                $template_data = unserialize($phone_info['custom_cfg_data']);
            }

            $new_template_data = array();
            if(is_array($template_data)) {
                foreach($template_data as $key => $data) {
                    $new_template_data = array_merge($new_template_data, array($key => $data['value']));
                }
            }

            $class = "endpoint_" . $phone_info['directory'] . "_" . $phone_info['cfg_dir'] . '_phone';

            $provisioner_lib = new $class();

            $provisioner_lib->root_dir = PHONE_MODULES_PATH;

            $provisioner_lib->engine = 'asterisk';
            $provisioner_lib->system = 'unix';

            $provisioner_lib->options = $new_template_data;

            //have to because of versions less than php5.3
            $provisioner_lib->brand_name = $phone_info['directory'];
            $provisioner_lib->family_line = $phone_info['cfg_dir'];

            $provisioner_lib->lines[1] = array('ext' => $phone_info['ext']);
            $provisioner_lib->reboot();
            unset($provisioner_lib);
        }
    } else {
        $message = _("No Brand Selected for Reboot");
    }
}

$sql = 'SELECT endpointman_mac_list.id , endpointman_mac_list.mac , endpointman_model_list.model , endpointman_mac_list.ext , endpointman_mac_list.description , endpointman_brand_list.name, endpointman_mac_list.custom_cfg_data, endpointman_mac_list.custom_cfg_template FROM endpointman_mac_list , endpointman_model_list , endpointman_brand_list WHERE ( endpointman_model_list.id = endpointman_mac_list.model ) AND ( endpointman_model_list.brand = endpointman_brand_list.id )';

$devices_list =& $endpoint->db->getAll($sql,array(),DB_FETCHMODE_ASSOC);


$i = 0;
$list = array();
foreach($devices_list as $row) {	#Displays the phones in the database with edit and delete buttons
    $list[$i] = $row;
    if (($row['custom_cfg_template'] == 0) && (isset($row['custom_cfg_data'])) ) {
        $list[$i]['template_name'] = "Custom-".$row['mac'];
    } elseif((!isset($row['custom_cfg_data'])) && ($row['custom_cfg_template'] == 0)) {
        $list[$i]['template_name'] = "N/A";
    } else {
        $sql = "SELECT name FROM endpointman_template_list WHERE id =".$row['custom_cfg_template'];
        $row2 =& $endpoint->db->getOne($sql);
        $list[$i]['template_name'] = $row2;
    }
    $i++;
}		# End while


$sql = 'SELECT * FROM  endpointman_mac_list WHERE model = 0';

$unknown_list =& $endpoint->db->getAll($sql,array(),DB_FETCHMODE_ASSOC);

foreach($unknown_list as $row) {	#Displays unknown phones in the database with edit and delete buttons
    $list[$i] = $row;

    $brand_info = $endpoint->get_brand_from_mac($row['mac']);

    $list[$i]['name'] = $brand_info['name'];
    $list[$i]['template_name'] = "N/A";
    $list[$i]['model'] = _("Unknown");
    $i++;
}

$amp_send['AMPDBUSER'] = $amp_conf['AMPDBUSER'];
$amp_send['AMPDBPASS'] = $amp_conf['AMPDBPASS'];
$amp_send['AMPDBNAME'] = $amp_conf['AMPDBNAME'];

$sql = "SELECT DISTINCT endpointman_product_list.* FROM endpointman_product_list, endpointman_model_list WHERE endpointman_product_list.id = endpointman_model_list.product_id AND endpointman_model_list.hidden = 0 AND endpointman_model_list.enabled = 1 AND endpointman_product_list.hidden != 1 AND endpointman_product_list.cfg_dir !=  ''";

$template_list =& $db->getAll($sql, array(), DB_FETCHMODE_ASSOC);
$i = 1;
$product_list = array();
$product_list[0]['value'] = 0;
$product_list[0]['text'] = "";
foreach($template_list as $row) {
        $product_list[$i]['value'] = $row['id'];
        $product_list[$i]['text'] = $row['short_name'];
        $i++;
}

//initialize a Rain TPL object
if (isset($template_editor)) {

} else {
    $tpl = new RainTPL( LOCAL_PATH.'templates' );
    $tpl->assign("list", $list);
    $tpl->assign("error", "");
    $tpl->assign("srvip", $_SERVER["SERVER_ADDR"]);
    $tpl->assign("web_var", "?type=$type");
    if($ma = $endpoint->modelsAvailable()) {
        $tpl->assign("models_ava", $ma);
    }
    $tpl->assign("web_path", WEB_PATH);
    $tpl->assign("product_list", $product_list);
    $tpl->assign("display_ext", $endpoint->displayExtension());
    $tpl->assign("brand_ava", $endpoint->brandAvailable());
    $tpl->assign("unmanaged", $final);
    $tpl->assign("button", $button);
    $tpl->assign("searched", $searched);
    $tpl->assign("edit", $edit);
    $tpl->assign("amp_conf_serial", base64_encode(serialize($amp_send)));
    $tpl->assign("no_add", $no_add);
    $tpl->assign("mode", $mode);

    if(isset($final)) {
        $tpl->assign("cached_devices", base64_encode(serialize($final)));
    }

    if (isset($mode) && ($mode == "EDIT")) {
        if($ma = $endpoint->modelsAvailable($edit_row['model_id'],NULL,$edit_row['brand_id'])) {
            $tpl->assign("mac", $edit_row['mac']);
            $tpl->assign("name", $edit_row['name']);
            $tpl->assign("brand_id", $edit_row['brand_id']);
            $tpl->assign("models_ava", $ma);
            $tpl->assign("display_ext", $endpoint->displayExtension($edit_row['ext']));
            $tpl->assign("display_templates", $endpoint->display_templates($edit_row['product_id'],$edit_row['custom_cfg_template']));
            $tpl->assign("edit_id", $edit_row['id']);
        } else {
            $message = _("You have disabled/removed all models that correspond to this brand. Please enable them in 'Brand Configurations/Setup' before trying to edit this phone");
            $tpl->assign("mode", NULL);
        }
    }


    $tpl->assign("message", $message);
    $tpl->assign("debug", $debug);

    //draw the template
    echo $tpl->draw( 'devices_manager' );
}
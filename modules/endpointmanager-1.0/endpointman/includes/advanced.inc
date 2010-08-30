<?PHP
/**
 * Endpoint Manager Advanced File
 *
 * @author Andrew Nagy
 * @license MPL / GPLv2 / LGPL
 * @package Provisioner
 */

$tpl = new RainTPL( LOCAL_PATH.'templates' );
//Set the message variable to nothing, however we can set it later and it will show up on the page
$message = "";
$tpl->assign("message", $message);
$tpl->assign("web_vars", "?type=$type");

echo $tpl->draw( 'global_header' );
echo $tpl->draw( 'advanced_subheader' );

if(!isset($_REQUEST['subpage'])) {
    $_REQUEST['subpage'] = "";
}

switch($_REQUEST['subpage']) {
    case "iedl":
        $amp_serial = base64_encode(serialize($amp_conf));
        $amp_serial = base64_encode(serialize($amp_conf));
        $temp_amp['amp_serial'] = $amp_serial;
        $temp_amp = base64_encode(serialize($temp_amp));

        $tpl->assign("exporter_address", "config.php?type=tool&amp;display=epm_config&amp;quietmode=1&amp;handler=file&amp;file=export.html.php&amp;module=endpointman&amp;rand=".rand());
        //Dave B's Q&D file upload security code (http://us2.php.net/manual/en/features.file-upload.php)
        if((isset($_REQUEST['action'])) AND (isset($_REQUEST['button_import'])) AND ($_REQUEST['action'] == "import")) {
            $allowedExtensions = array("csv","txt");
            foreach ($_FILES as $file) {
                if ($file['tmp_name'] > '') {
                    if (!in_array(end(explode(".", strtolower($file['name']))), $allowedExtensions)) {
                        $tpl->assign("message", "We support only CVS and TXT files");
                    } else {
                        $uploaddir = LOCAL_PATH;
                        $uploadfile = $uploaddir . basename($_FILES['import_csv']['name']);
                        if (move_uploaded_file($_FILES['import_csv']['tmp_name'], $uploadfile)) {
                            //Parse the uploaded file
                            $handle = fopen(LOCAL_PATH.$_FILES['import_csv']['name'], "r");
                            $i = 1;
                            while (($device = fgetcsv($handle, filesize(LOCAL_PATH.$_FILES['import_csv']['name']))) !== FALSE) {
                                if($device[0] != "") {
                                    if($mac = $endpoint->mac_check_clean($device[0])) {
                                        $sql = "SELECT id FROM endpointman_brand_list WHERE name LIKE '%".$device[1]."%' LIMIT 1";
                                        $res =& $db->query($sql);
                                        if($res->numRows()) {
                                            $brand_id = $endpoint->db->getOne($sql);
                                            $brand_id = $brand_id[0];
                                        } else {
                                            $brand_id = 0;
                                        }
                                        $sql_model = "SELECT id FROM endpointman_model_list WHERE brand = ".$brand_id." AND model LIKE '%".$device[2]."%' LIMIT 1";
                                        $sql_ext = "SELECT extension, name FROM users WHERE extension LIKE '%".$device[3]."%' LIMIT 1";

                                        $res_model =& $db->query($sql_model);
                                        if($res_model->numRows()) {
                                            $model_id =& $db->getRow($sql_model,array(), DB_FETCHMODE_ASSOC);
                                            $model_id = $model_id['id'];
                                        } else {
                                            $model_id = 0;
                                        }
                                        $res_ext =& $db->query($sql_ext);
                                        if($res_ext->numRows()) {
                                            $ext =& $db->getRow($sql_ext,array(), DB_FETCHMODE_ASSOC);
                                            $description = $ext['name'];
                                            $ext = $ext['extension'];
                                        } else {
                                            $ext = '';
                                            $description = '';
                                        }
                                        $sql = "INSERT INTO  endpointman_mac_list (mac, model, ext, description) VALUES ('".$mac."','".$model_id."','".$ext."','".$description."')";
                                        $endpoint->db->query($sql);
                                        $message .= "Inserted Mac Address ".$mac."<br/>";
                                    } else {
                                        $message .= "Invalid Mac on line ". $i. "<br />";
                                    }
                                }
                                $i++;
                            }
                            fclose($handle);
                            unlink(LOCAL_PATH.$_FILES['import_csv']['name']);
                            $message .= "Please reboot & rebuild all imported phones<br />";
                            $tpl->assign("message", $message);
                        } else {
                            $tpl->assign("message", "Possible file upload attack!");

                        }
                    }
                }
            }
        }
        echo $tpl->draw( 'advanced_settings_iedl' );
        break;
    case "manual_upload":
        $sql = "SELECT value FROM endpointman_global_vars WHERE var_name LIKE 'endpoint_vers'";
        $provisioner_ver = $endpoint->db->getOne($sql);
        $provisioner_ver = date("n-j-y",$provisioner_ver) . " at " . date("g:ia",$provisioner_ver);
        $tpl->assign("provisioner_ver", $provisioner_ver);

        //$provisioner_ver = filemtime(PHONE_MODULES_PATH."master.xml");
        //$provisioner_ver = date("n-j-y",$provisioner_ver) . " at " . date("g:ia",$provisioner_ver);
        $provisioner_ver = "";
        $tpl->assign("master_ver", $provisioner_ver);
        $uploads_dir = PHONE_MODULES_PATH."temp";

        if(isset($_REQUEST['upload_provisioner'])) {

            $extension = pathinfo($_FILES["package"]["name"],PATHINFO_EXTENSION);
            if($extension == "tgz") {
                if ($_FILES['package']['error'] == UPLOAD_ERR_OK) {
                    $tmp_name = $_FILES["package"]["tmp_name"];
                    $name = $_FILES["package"]["name"];
                    move_uploaded_file($tmp_name, "$uploads_dir/$name");
                    $tpl->assign("show_installer", 1);
                    $tpl->assign("package", $name);
                    $tpl->assign("type", "upload_provisioner");
                    $tpl->assign("xml", 0);
                } else {
                    $message = $endpoint->file_upload_error_message($_FILES['package']['error']);
                }
            } else {
                $message = "Invalid Extension";
            }
            

        } elseif(isset($_REQUEST['upload_master_xml'])) {
            $extension = pathinfo($_FILES["xml"]["name"],PATHINFO_EXTENSION);
            if($extension == "xml") {
                if ($_FILES['xml']['error'] == UPLOAD_ERR_OK) {
                    $tmp_name = $_FILES['xml']["tmp_name"];
                    $name = $_FILES['xml']["name"];
                    move_uploaded_file($tmp_name, "$uploads_dir/$name");
                    $tpl->assign("show_installer", 1);
                    $tpl->assign("package", 0);
                    $tpl->assign("type", "upload_master_xml");
                    $tpl->assign("xml", $name);
                } else {
                    $message = $endpoint->file_upload_error_message($_FILES['xml']['error']);
                }
            } else {
                $message = "Invalid Extension";
            }
        } elseif(isset($_REQUEST['upload_brand'])) {
            $error = FALSE;
            $files_list = array();
            $i = 0;
            foreach($_FILES as $files) {
                $extension = pathinfo($files["name"],PATHINFO_EXTENSION);
                if(($extension == "xml") OR ($extension == "tgz")) {
                    if ($files['error'] == UPLOAD_ERR_OK) {
                        $tmp_name = $files["tmp_name"];
                        $name = $files["name"];
                        move_uploaded_file($tmp_name, "$uploads_dir/$name");
                        $files_list[$i] = $name;
                        $i++;
                    } else {
                        $error_message = $endpoint->file_upload_error_message($files['error']);
                        $error = TRUE;
                    }
                } else {
                    $error_message = "Invalid Extension";
                    $error = TRUE;
                }
            }
            if(!$error){
                $tpl->assign("show_installer", 1);
                $tpl->assign("package", $files_list[1]);
                $tpl->assign("type", "upload_brand");
                $tpl->assign("xml", $files_list[0]);
            } else {
                $message = $error_message;
            }

        }
        $tpl->assign("message", $message);

        echo $tpl->draw( 'advanced_settings_manual_upload' );
        break;
    case "sh_manager":
        if(isset($_REQUEST['button_hide'])) {
            if(isset($_REQUEST['model'])) {
                $sql = "UPDATE endpointman_model_list SET hidden = 1 WHERE id = '".$_REQUEST['model']."'";
            } elseif(isset($_REQUEST['brand'])) {
                $sql = "UPDATE endpointman_brand_list SET hidden = 1 WHERE id = ".$_REQUEST['brand'];
            } elseif(isset($_REQUEST['product'])) {
                $sql = "UPDATE endpointman_product_list SET hidden = 1 WHERE id = '".$_REQUEST['product']."'";
            }
            $endpoint->db->query($sql);
        }elseif(isset($_REQUEST['button_show'])) {
            if(isset($_REQUEST['model'])) {
                $sql = "UPDATE endpointman_model_list SET hidden = 0 WHERE id = '".$_REQUEST['model']."'";
            } elseif(isset($_REQUEST['brand'])) {
                $sql = "UPDATE endpointman_brand_list SET hidden = 0 WHERE id = ".$_REQUEST['brand'];
            } elseif(isset($_REQUEST['product'])) {
                $sql = "UPDATE endpointman_product_list SET hidden = 0 WHERE id = '".$_REQUEST['product']."'";
            }
            $endpoint->db->query($sql);
        }
        $sql="SELECT * from endpointman_brand_list WHERE id > 0 ORDER BY id ASC ";
        $result =& $endpoint->db->getAll($sql, array(), DB_FETCHMODE_ASSOC);
        $i = 0;
        foreach($result as $row) {
            $row_out[$i] = $row;
            $row_out[$i]['count'] = $i;
            if($row['installed']) {
                $j = 0;
                $sql = 'SELECT * FROM endpointman_product_list WHERE brand = '.$row['id'].' ORDER BY long_name ASC';
                $result2 =& $endpoint->db->getAll($sql, array(), DB_FETCHMODE_ASSOC);
                $xml_data = "";
                foreach($result2 as $row2) {
                    $row_out[$i]['products'][$j] = $row2;
                    $sql = 'SELECT * FROM endpointman_model_list WHERE product_id = '.$row2['id'];
                    $result3 =& $endpoint->db->getAll($sql, array(), DB_FETCHMODE_ASSOC);
                    $k = 0;
                    foreach($result3 as $row3) {
                        $row_out[$i]['products'][$j]['models'][$k] = $row3;
                        $k++;
                    }
                    $j++;
                }
            }
            $i++;
        }
        $tpl->assign("brand2_list", $row_out);
        echo $tpl->draw( 'advanced_settings_sh_manager' );
        break;
    case "oui_manager":
        if((isset($_REQUEST['oui_sub'])) AND ($_REQUEST['rb_brand'] > 0) AND ($_REQUEST['oui'] != "")) {
            $sql = "INSERT INTO  endpointman_oui_list (oui, brand, custom) VALUES ('".$_REQUEST['oui']."',  '".$_REQUEST['rb_brand']."',  '1')";
            $endpoint->db->query($sql);
        }
        if((isset($_REQUEST['delete'])) AND ($_REQUEST['id'] > 0)) {
            $sql = "DELETE FROM endpointman_oui_list WHERE id = ". $_REQUEST['id'];
            $endpoint->db->query($sql);
        }
        $sql = 'SELECT endpointman_oui_list.id, endpointman_oui_list.oui , endpointman_brand_list.name FROM endpointman_oui_list , endpointman_brand_list WHERE endpointman_oui_list.brand = endpointman_brand_list.id AND endpointman_oui_list.custom = 0';
        $data =& $endpoint->db->getAll($sql, array(), DB_FETCHMODE_ASSOC);
        $i = 0;
        $row_out = array();
        foreach($data as $row) {
            $row_out[$i] = $row;
            $i++;
        }
        $tpl->assign("oui_list", $row_out);
        $sql = 'SELECT endpointman_oui_list.id, endpointman_oui_list.oui , endpointman_brand_list.name FROM endpointman_oui_list , endpointman_brand_list WHERE endpointman_oui_list.brand = endpointman_brand_list.id AND endpointman_oui_list.custom = 1';
        $data =& $endpoint->db->getAll($sql, array(), DB_FETCHMODE_ASSOC);
        $i = 0;
        $row_out_custom = array();
        foreach($data as $row) {
            $row_out_custom[$i] = $row;
            $i++;
        }
        $tpl->assign("oui_list_custom", $row_out_custom);
        $tpl->assign("brand_ava", $endpoint->brandAvailable());
        echo $tpl->draw( 'advanced_settings_oui' );
        break;
    case "poce":
        $sql = 'SELECT * FROM `endpointman_product_list` WHERE `hidden` = 0 AND `id` > 0';
        $data =& $endpoint->db->getAll($sql, array(), DB_FETCHMODE_ASSOC);
        $i = 0;
        foreach($data as $row) {
            $product_list[$i]['value'] = $row['id'];
            $product_list[$i]['text'] = $row['long_name'];
            if((isset($_REQUEST['product_select'])) AND ($_REQUEST['product_select'] == $row['id'])) {
                $product_list[$i]['selected'] = 1;
            }
            $i++;
        }
        if(isset($_REQUEST['delete'])) {
            $sql = "DELETE FROM endpointman_custom_configs WHERE id =". $_REQUEST['sql'];
            $endpoint->db->query($sql);
        }
        if(isset($_REQUEST['temp_file'])) {
            $file = $_REQUEST['temp_file'];

            $sql = "SELECT cfg_dir,directory,config_files FROM endpointman_product_list,endpointman_brand_list WHERE endpointman_product_list.brand = endpointman_brand_list.id AND endpointman_product_list.id = '". $_REQUEST['product_select'] ."'";
            $row =& $endpoint->db->getRow($sql, array(), DB_FETCHMODE_ASSOC);

            $file=PHONE_MODULES_PATH.'endpoint/'.$row['directory']."/".$row['cfg_dir']."/".$file;

            if(isset($_REQUEST['config_text'])) {
                if(isset($_REQUEST['button_save'])) {
                    $wfh=fopen($file,'w');
                    fwrite($wfh,$_REQUEST['config_text']);
                    fclose($wfh);
                }
            }

            if(!file_exists($file)) {
                $fp = fopen($file, 'w');
                fwrite($fp, '
<!--<template_data>
    <item>
            <description>General Settings</description>
            <type>group</type>
    </item>
    <item>
            <variable>$sip_digit_timeout</variable>
            <default_value>4</default_value>
            <description>SIP Digit Timeout</description>
            <type>input</type>
    </item>
</template_data>-->
                            ');
                fclose($fp);
            }
            $handle = fopen($file, "rb");
            $contents = fread($handle, filesize($file));
            fclose($handle);
            $tpl->assign("config_data", $contents);
        }
        if(isset($_REQUEST['file'])) {
            $sql = "SELECT cfg_dir,directory,config_files FROM endpointman_product_list,endpointman_brand_list WHERE endpointman_product_list.brand = endpointman_brand_list.id AND endpointman_product_list.id = '". $_REQUEST['product_select'] ."'";
            $row =& $endpoint->db->getRow($sql, array(), DB_FETCHMODE_ASSOC);

            $config_files = explode(",",$row['config_files']);
            $file=PHONE_MODULES_PATH.'endpoint/'.$row['directory']."/".$row['cfg_dir']."/".$config_files[$_REQUEST['file']];
            if(isset($_REQUEST['config_text'])) {
                if(isset($_REQUEST['button_save'])) {
                    $wfh=fopen($file,'w');
                    fwrite($wfh,$_REQUEST['config_text']);
                    fclose($wfh);
                }elseif(isset($_REQUEST['button_save_as'])) {
                    $sql = 'INSERT INTO endpointman_custom_configs (name, original_name, product_id, data) VALUES ("'.addslashes($_REQUEST['save_as_name']).'","'.addslashes($config_files[$_REQUEST['file']]).'","'.$_REQUEST['product_select'].'","'.addslashes($_REQUEST['config_text']).'")';
                    $endpoint->db->query($sql);
                }
            }
            $handle = fopen($file, "rb");
            $contents = fread($handle, filesize($file));
            fclose($handle);
            $tpl->assign("save_as_name_value", $config_files[$_REQUEST['file']]);
            $tpl->assign("config_data", $contents);
        } elseif(isset($_REQUEST['sql'])) {
            if(isset($_REQUEST['config_text'])) {
                if(isset($_REQUEST['button_save'])) {
                    $sql = "UPDATE endpointman_custom_configs SET data = '".addslashes($_REQUEST['config_text'])."' WHERE id = ".$_REQUEST['sql'];
                    $endpoint->db->query($sql);
                }elseif(isset($_REQUEST['button_save_as'])) {
                    $sql = 'SELECT original_name FROM endpointman_custom_configs WHERE id = '.$_REQUEST['sql'];
                    $file_name = $endpoint->db->getOne($sql);

                    $sql = "INSERT INTO endpointman_custom_configs (name, original_name, product_id, data) VALUES ('".addslashes($_REQUEST['save_as_name'])."','".addslashes($file_name)."','".$_REQUEST['product_select']."','".addslashes($_REQUEST['config_text'])."')";
                    $endpoint->db->query($sql);
                }
            }
            $sql = 'SELECT * FROM endpointman_custom_configs WHERE id =' . $_REQUEST['sql'];
            $row =& $endpoint->db->getRow($sql, array(), DB_FETCHMODE_ASSOC);
            $tpl->assign("save_as_name_value", $row['name']);
            $tpl->assign("config_data", $row['data']);
        }
        if(isset($_REQUEST['product_select'])) {
            $sql = "SELECT cfg_dir,directory,config_files FROM endpointman_product_list,endpointman_brand_list WHERE endpointman_product_list.brand = endpointman_brand_list.id AND endpointman_product_list.id ='" . $_REQUEST['product_select'] . "'";
            
            $row =& $endpoint->db->getRow($sql, array(), DB_FETCHMODE_ASSOC);
            $config_files = explode(",",$row['config_files']);
            $i = 0;
            foreach($config_files as $config_files_data) {
                $file_list[$i]['value'] = $i;
                $file_list[$i]['text'] = $config_files_data;
                $i++;
            }
            $sql = "SELECT * FROM endpointman_custom_configs WHERE product_id = '". $_REQUEST['product_select'] . "'";
            $res =& $endpoint->db->query($sql);
            $i = 0;
            if($res->numRows()) {
                $data =& $endpoint->db->getAll($sql, array(), DB_FETCHMODE_ASSOC);
                foreach($data as $row2) {
                    $sql_file_list[$i]['value'] = $row2['id'];
                    $sql_file_list[$i]['text'] = $row2['name'];
                    $sql_file_list[$i]['ref'] = $row2['original_name'];
                    $i++;
                }
            } else {
                $sql_file_list = NULL;
            }

            require(PHONE_MODULES_PATH.'setup.php');

            $class = "endpoint_" . $row['directory'] . "_" . $row['cfg_dir'] . '_phone';

            $phone_config = new $class();

            if((method_exists($phone_config,'display_options')) AND (method_exists($phone_config,'process_options'))) {
                if(isset($_REQUEST['phone_options'])) {
                    $tpl->assign("options", $phone_config->process_options());
                } else {
                    $tpl->assign("options", $phone_config->display_options());
                }
            }



            $template_file_list[0]['value'] = "template_data_custom.xml";
            $template_file_list[0]['text'] = "template_data_custom.xml";

            $sql = 'SELECT model FROM `endpointman_model_list` WHERE `product_id` LIKE CONVERT(_utf8 \'1-2\' USING latin1) COLLATE latin1_swedish_ci AND `enabled` = 1 AND `hidden` = 0';
            $data =& $endpoint->db->getAll($sql, array(), DB_FETCHMODE_ASSOC);
            $i = 1;
            foreach($data as $list) {
                $template_file_list[$i]['value'] = "template_data_" . $list['model'] . "_custom.xml";
                $template_file_list[$i]['text'] = "template_data_" . $list['model'] . "_custom.xml";
            }

            $tpl->assign("template_file_list",$template_file_list);
            if(isset($_REQUEST['temp_file'])) {
                $tpl->assign("temp_file",1);
            } else {
                $tpl->assign("temp_file",NULL);
            }

            $tpl->assign("file_list", $file_list);
            $tpl->assign("sql_file_list", $sql_file_list);
            $tpl->assign("product_selected", $_REQUEST['product_select']);
        }
        $tpl->assign("product_list", $product_list);
        echo $tpl->draw( 'advanced_settings_poce' );
        break;
    case "settings":
    default:
        $message = "";
        if(isset($_REQUEST['button_update_globals'])) {
            $_POST['srvip'] = trim($_POST['srvip']);  #trim whitespace from IP address

            $_POST['config_loc'] = trim($_POST['config_loc']);  #trim whitespace from Config Location

            $sql="UPDATE endpointman_global_vars SET value='" . $_POST['srvip'] . "' WHERE var_name='srvip'";
            $endpoint->db->query($sql);
            $sql="UPDATE endpointman_global_vars SET value='" . $_POST['tz'] . "' WHERE var_name='tz'";
            $endpoint->db->query($sql);

            //No trailing slash. Help the user out and add one :-)
            if($_POST['config_loc'][strlen($_POST['config_loc'])-1] != "/") {
                $_POST['config_loc'] = $_POST['config_loc'] ."/";
            }

            if((isset($_POST['config_loc'])) AND ($_POST['config_loc'] != "")) {
                if((file_exists($_POST['config_loc'])) AND (is_dir($_POST['config_loc']))) {
                    if(is_writable($_POST['config_loc'])) {
                        $sql="UPDATE endpointman_global_vars SET value='" . $_POST['config_loc'] . "' WHERE var_name='config_location'";
                        $endpoint->db->query($sql);
                    } else {
                        $message = "<h3><center><font color='red'>Directory Not Writable!</font></center></h3>";
                    }
                } else {
                    $message = "<h3><center><font color='red'>Not a Vaild Directory</font></center></h3>";
                }
            } else {
                $message = "<h3><center><font color='red'>No Configuration Location Defined!</font></center></h3>";
            }

            if((isset($_POST['enable_ari'])) AND ($_POST['enable_ari'] == "on")) {
                $_POST['enable_ari'] = 1;
            } else {
                $_POST['enable_ari'] = 0;
            }
            if((isset($_POST['enable_debug'])) AND ($_POST['enable_debug'] == "on")) {
                $_POST['enable_debug'] = 1;
            } else {
                $_POST['enable_debug'] = 0;
            }
            if((isset($_POST['enable_updates'])) AND ($_POST['enable_updates'] == "on")) {
                $_POST['enable_updates'] = 1;
                $sql = "INSERT INTO cronmanager (module, id, time, freq, lasttime, command) VALUES ('endpointman', 'UPDATES', '23', '24', '0', 'php ".$amp_conf['AMPWEBROOT']."/admin/modules/endpointman/includes/update_check.php')";
                $endpoint->db->query($sql);
            } else {
                $_POST['enable_updates'] = 0;
                $sql = "DELETE FROM `cronmanager` WHERE CONVERT(`cronmanager`.`module` USING utf8) = 'endpointman' AND CONVERT(`cronmanager`.`id` USING utf8) = 'UPDATES' LIMIT 1";
                $endpoint->db->query($sql);
            }

            $sql="UPDATE endpointman_global_vars SET value='" . $_POST['enable_ari'] . "' WHERE var_name='enable_ari'";
            $endpoint->db->query($sql);

            $sql="UPDATE endpointman_global_vars SET value='" . $_POST['enable_debug'] . "' WHERE var_name='debug'";
            $endpoint->db->query($sql);

            $sql="SELECT gmtoff,gmthr FROM endpointman_time_zones WHERE tz='" . $_POST['tz'] . "'";
            $row =& $db->getRow($sql, array(), DB_FETCHMODE_ASSOC);

            $sql="UPDATE endpointman_global_vars SET value='" . $row['gmtoff'] . "' where var_name='gmtoff'";
            $endpoint->db->query($sql);
            $sql="UPDATE endpointman_global_vars SET value='" . $row['gmthr'] . "' where var_name='gmthr'";
            $endpoint->db->query($sql);

            $sql="UPDATE endpointman_global_vars SET value='" . $_POST['asterisk_loc'] . "' WHERE var_name='asterisk_location'";
            $endpoint->db->query($sql);

            $sql="UPDATE endpointman_global_vars SET value='" . $_POST['enable_updates'] . "' WHERE var_name='check_updates'";
            $endpoint->db->query($sql);

            $sql="UPDATE endpointman_global_vars SET value='" . $_POST['nmap_loc'] . "' WHERE var_name='nmap_location'";
            $endpoint->db->query($sql);

            $sql="UPDATE endpointman_global_vars SET value='" . $_POST['arp_loc'] . "' WHERE var_name='arp_location'";
            $endpoint->db->query($sql);

            $message .= "<h3><center>Updated!</center></h3>";
        }
        //Because we are working with global variables we probably updated them, so lets refresh those variables
        $endpoint->global_cfg =& $db->getAssoc("SELECT var_name, value FROM endpointman_global_vars");


        if($endpoint->global_cfg['enable_ari']) {
            $ari_selected = "checked";
        } else {
            $ari_selected = "";
        }

        if($endpoint->global_cfg['disable_htaccess']) {
            $htaccess_selected = "checked";
        } else {
            $htaccess_selected = "";
        }

        if($endpoint->global_cfg['check_updates']) {
            $updates_selected = "checked";
        } else {
            $updates_selected = "";
        }
        if($endpoint->global_cfg['debug']) {
            $debug_selected = "checked";
            global $debug;
            $debug = $debug . print_r($_REQUEST,true);
            $tpl->assign("debug", $debug);
        } else {
            $debug_selected = "";
        }
        $tpl->assign("htaccess_selected", $htaccess_selected);
        $tpl->assign("ari_selected", $ari_selected);
        $tpl->assign("updates_selected", $updates_selected);
        $tpl->assign("debug_selected", $debug_selected);
        $tpl->assign("message", $message);
        $tpl->assign("ip", $_SERVER["SERVER_ADDR"]);
        $tpl->assign("srvip", $endpoint->global_cfg['srvip']);
        $tpl->assign("arp_location", $endpoint->global_cfg['arp_location']);
        $tpl->assign("nmap_location", $endpoint->global_cfg['nmap_location']);
        $tpl->assign("asterisk_location", $endpoint->global_cfg['asterisk_location']);

        $tpl->assign("config_location", $endpoint->global_cfg['config_location']);
        $tpl->assign("list_tz", $endpoint->listTZ($endpoint->global_cfg['tz']));
        $tpl->assign("brand_list", $endpoint->brandAvailable());
        echo $tpl->draw( 'advanced_settings_settings' );
        break;
}
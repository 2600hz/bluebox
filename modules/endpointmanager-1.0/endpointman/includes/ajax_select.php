<?php
/**
 * Ajax Select File
 *
 * @author Andrew Nagy
 * @license MPL / GPLv2 / LGPL
 * @package Provisioner
 */
include 'jsonwrapper.php';

if(($_REQUEST['id'] == "") OR ($_REQUEST['id'] == "0")) {
	$out[0]['optionValue'] = "";
	$out[0]['optionDisplay'] = "";
	echo json_encode($out);
	die();
}

if($_REQUEST['atype'] == "model") {
	$sql = "SELECT * FROM endpointman_model_list WHERE enabled = 1 AND brand =". $_GET['id'];
} elseif ($_REQUEST['atype'] == "template") {
	$sql = "SELECT id, name as model FROM  endpointman_template_list WHERE  product_id = '". $_GET['id']."'";
} elseif ($_REQUEST['atype'] == "template2") {
	$sql = "SELECT DISTINCT endpointman_template_list.id, endpointman_template_list.name as model FROM endpointman_template_list, endpointman_model_list, endpointman_product_list WHERE endpointman_template_list.product_id = endpointman_model_list.product_id AND endpointman_model_list.product_id = endpointman_product_list.id AND endpointman_model_list.id = '". $_GET['id']."'";
} elseif ($_REQUEST['atype'] == "model_clone") {
        $sql = "SELECT endpointman_model_list.id, endpointman_model_list.model as model FROM endpointman_model_list, endpointman_product_list WHERE endpointman_product_list.id = endpointman_model_list.product_id AND endpointman_model_list.enabled = 1 AND endpointman_model_list.hidden = 0 AND product_id = '". $_GET['id']."'";
}

if (($_REQUEST['atype'] == "template") OR ($_REQUEST['atype'] == "template2")) {
	$out[0]['optionValue'] = 0;
	$out[0]['optionDisplay'] = "Custom...";
	$i=1;
} elseif ($_REQUEST['atype'] == "model") {
	$out[0]['optionValue'] = 0;
	$out[0]['optionDisplay'] = "";
	$i=1;
} else {
	$i=0;
}

$result = $db->getAll($sql,array(), DB_FETCHMODE_ASSOC);

foreach($result as $row) {
	$out[$i]['optionValue'] = $row['id'];
	$out[$i]['optionDisplay'] = $row['model'];
	$i++;
}

echo json_encode($out);
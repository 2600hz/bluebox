<?php
/**
 * Endpoint Manager Export File
 *
 * @author Andrew Nagy
 * @license MPL / GPLv2 / LGPL
 * @package Provisioner
 */

require 'functions.inc';

$endpoint = new endpointmanager();


header("Content-type: text/csv");
header('Content-Disposition: attachment; filename="devices_list.csv"');

$outstream = fopen("php://output",'w');

$sql = 'SELECT endpointman_mac_list.mac, endpointman_brand_list.name, endpointman_model_list.model, endpointman_mac_list.ext FROM endpointman_mac_list, endpointman_model_list, endpointman_brand_list WHERE endpointman_model_list.id = endpointman_mac_list.model AND endpointman_model_list.brand = endpointman_brand_list.id';

$result = $endpoint->db->getAll($sql,array(),DB_FETCHMODE_ASSOC);

foreach($result as $row) {
	fputcsv($outstream, $row);    
}
fclose($outstream);
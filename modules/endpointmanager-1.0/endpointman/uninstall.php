<?PHP
/*
Endpoint Manager V2
Copyright (C) 2009-2010  Ed Macri, John Mullinix and Andrew Nagy 

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.

http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/
require dirname($_SERVER["SCRIPT_FILENAME"]). "/modules/endpointman/includes/functions.inc";

global $endpoint;

$endpoint = new endpointmanager();

global $db;

if (! function_exists("out")) {
	function out($text) {
		echo $text."<br />";
	}
}

if (! function_exists("outn")) {
	function outn($text) {
		echo $text;
	}
}

out("Removing Phone Modules Directory");
$endpoint->deltree(PHONE_MODULES_PATH);
exec("rm -R ". PHONE_MODULES_PATH);

out("Dropping all relevant tables");
$sql = "DROP TABLE `endpointman_brand_list`";
$result = $db->query($sql);

$sql = "DROP TABLE `endpointman_global_vars`";
$result = $db->query($sql);

$sql = "DROP TABLE `endpointman_mac_list`";
$result = $db->query($sql);

$sql = "DROP TABLE `endpointman_model_list`";
$result = $db->query($sql);

$sql = "DROP TABLE `endpointman_oui_list`";
$result = $db->query($sql);

$sql = "DROP TABLE `endpointman_product_list`";
$result = $db->query($sql);

$sql = "DROP TABLE `endpointman_template_list`";
$result = $db->query($sql);

$sql = "DROP TABLE `endpointman_time_zones`";
$result = $db->query($sql);

$sql = "DROP TABLE `endpointman_custom_configs`";
$result = $db->query($sql);

out("Removing ARI Module");		
unlink($amp_conf['AMPWEBROOT']."/recordings/modules/phonesettings.module");

?>
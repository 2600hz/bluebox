<?PHP
/**
 * Ajax Calls File
 *
 * @author Andrew Nagy
 * @license MPL / GPLv2 / LGPL
 * @package Provisioner
 */
include LOCAL_PATH . 'includes/jsonwrapper.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);
$sql = "SELECT * FROM endpointman_model_list WHERE enabled = 1 AND brand =". $_GET['id'];
$result = mysql_query($sql);
;
$i=0;
while ($row = mysql_fetch_assoc($result)) {
	$out[$i]['optionValue'] = $row['id'];
	$out[$i]['optionDisplay'] = $row['model'];
	$i++;
}
echo json_encode($out);
<?php
include('MongoCdr.php');

$mongo = new MongoCdr();


$page = $_REQUEST['page']; // get the requested page 
$limit = $_REQUEST['rows']; // get how many rows we want to have into the grid 
$sidx = $_REQUEST['sidx']; // get index row - i.e. user click to sort 
$sord = $_REQUEST['sord']; // get the direction 

$sord = ($sord == 'asc') ? 1 : -1;

if(!$sidx) $sidx =1; 

//$connection->dropDB('sip_mphill_com');
//192_168_50_229.1160
$db = $mongo->selectDB('cdr');

$collection = $db->selectCollection('record');

$domain = 'sip_mphill_com';
//search = array('domain' => $domain )
$count=$collection->find()->count();//

if( $count >0 ) 
{ 
	$total_pages = @ceil($count/$limit); 
} else { 
	$total_pages = 0; 
} 

if ($page > $total_pages) 
{
	$page=$total_pages; 
}

$start = $limit * $page - $limit; // do not put $limit*($page - 1) 

if(($start + $limit) > $count)
{
	$limit = $count = $start; //strangeness if the limit exceeds the size of our data
}
// find - > array('domain' => 'urban_voicebus_net' )
//array('domain' => $domain )


$cursor = $collection->find()->sort(array($sidx => $sord))->skip($start)->limit($limit);

$response->page = $page; 
$response->total = $total_pages; 
$response->records = $count; 

foreach($cursor as $id => $cdr)
{
	$response->rows[] = array('cell' => 
				array($id, 
					$cdr['direction'], 
					$cdr['destination_number'], 
					$cdr['caller_id_name'], 
					$cdr['caller_id_number'],
					date("m/d/y g:i a", $cdr['start']->sec),
					sec2hms($cdr['billsec'], TRUE), 
					$cdr['hangup_cause'],
					$cdr['billsec'] * 0.01)
				);
}

$json = json_encode($response);
echo $json;
//echo $collection->count();


function sec2hms ($sec, $padHours = false) 
{

    $hms = "";
    
    // there are 3600 seconds in an hour, so if we
    // divide total seconds by 3600 and throw away
    // the remainder, we've got the number of hours
    $hours = intval(intval($sec) / 3600); 

    // add to $hms, with a leading 0 if asked for
    $hms .= ($padHours) 
          ? str_pad($hours, 2, "0", STR_PAD_LEFT). ':'
          : $hours. ':';
     
    // dividing the total seconds by 60 will give us
    // the number of minutes, but we're interested in 
    // minutes past the hour: to get that, we need to 
    // divide by 60 again and keep the remainder
    $minutes = intval(($sec / 60) % 60); 

    // then add to $hms (with a leading 0 if needed)
    $hms .= str_pad($minutes, 2, "0", STR_PAD_LEFT). ':';

    // seconds are simple - just divide the total
    // seconds by 60 and keep the remainder
    $seconds = intval($sec % 60); 

    // add to $hms, again with a leading 0 if needed
    $hms .= str_pad($seconds, 2, "0", STR_PAD_LEFT);

    return $hms;
}

?>

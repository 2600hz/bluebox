<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * Config options for the Calls module
 */

$config['scandir'] = '/usr/local/freeswitch/log/cdr-csv';
$config['defaulttemplate'] = '"uuid","${uuid}","accountcode","${accountcode}","caller_id_number","${caller_id_number}","destination_number","${destination_number}","context","${context}","duration","${duration}","start_stamp","${start_stamp}","answer_stamp","${answer_stamp}","end_stamp","${end_stamp}","billsec","${billsec}","hangup_cause","${hangup_cause}","channel_name","${channel_name}","bridge_channel","${bridge_channel}","caller_id_name","${caller_id_name}","bleg_uuid","${bleg_uuid}","read_codec","${read_codec}","write_codec","${write_codec}"';


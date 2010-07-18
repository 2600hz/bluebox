<?php
/**
 * Library for CDR log processing. Imports logs from FS into database.
 * Run this externally via cron or as a background event.
 *
 * @author dman
 */
class ProcessLog {


    public function importLogs() {
        // Scan for new logs that haven't been processed before and import them into the log table
        // Make sure not to get dupes, even if there's a crash (how?)

       $coreFields = array(
            'uuid' => 1, 
            'accountcode' => 1,
            'caller_id_number' => 1,
            'destination_number' => 1,
            'context' => 1,
            'duration' => 1,
            'start_stamp' => 1,
            'answer_stamp' => 1,
            'end_stamp' => 1,
            'billsec' => 1,
            'hangup_cause' => 1,
            'channel_name' => 1,
            'bridge_channel' => 1
        );

        $recordCount = 0;

        Kohana::log('debug', 'scandir: ' . Kohana::config('calls.scandir'));

        $callFiles = glob(rtrim(Kohana::config('calls.scandir'), '/') . '/' . 'Master.csv.*', GLOB_MARK);

        foreach( $callFiles as $callFile ) {
            if (($callFileHandle = fopen($callFile, "r")) !== FALSE) {
                while (($callRecord = fgetcsv($callFileHandle, 2000, ",")) !== FALSE) {

                    $corecdr = array();
                    $extracdr = array();

                    $numFields = count($callRecord);
                    $recordCount++;

                    $insertCall = new Calls;

                    Kohana::log('debug', "$numFields fields in record $recordCount");

                    for ($field = 0; $field < $numFields; $field = $field+2) {
                        if(isset($coreFields[$callRecord[$field]])) {
                            $corecdr[$callRecord[$field]] = $callRecord[$field+1];
                       } else {
                            $extracdr[$callRecord[$field]] = $callRecord[$field+1];
                       }
                    }
                    Kohana::log('debug', 'Call Log: ' . print_r($corecdr,true));
                    Kohana::log('debug', 'Call Log: ' . print_r($extracdr,true));
                    
                }
                fclose($callFileHandle);
            }
        }
    }
}

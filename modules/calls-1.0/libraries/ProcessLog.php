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

        Kohana::log('debug', 'scandir: ' . Kohana::config('calls.scandir'));

        $callFiles = glob(rtrim(Kohana::config('calls.scandir'), '/') . '/' . 'Master.csv.*', GLOB_MARK);

        foreach( $callFiles as $callFile ) {

            $recordCount = 0;
            $recordsInserted = 0;
            $recordsErrored = 0;
            $recordsDup = 0;

            $callFileHistory = new CallsFiles;
            $callFileHistory->filename = $callFile;

            // Save before we start so we can see if we crash. Also lets us calc how long it took.
            $callFileHistory->save();

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
                    $corecdr['registry'] = $extracdr;
                    $insertCall->synchronizeWithArray($corecdr);

                    try {
                        $insertCall->save();
                        $recordsInserted++;
                    } catch (Exception $e) {
                        if(preg_match('/Duplicate entry/',$e->getMessage())) {
                            $recordsDup++;
                        } else {
                            $recordsErrored++;
                        }
                        Kohana::log('debug', 'Insert of Call Log Failed. UUID: ' . $corecdr['uuid'] . 'Message: ' . $e->getMessage());
                    }


                    Kohana::log('debug', 'Call Log: ' . print_r($corecdr,true));
                    Kohana::log('debug', 'Call Log: ' . print_r($extracdr,true));
                    
                }

                fclose($callFileHandle);
            }

            // Update the history adter we have processed the file.	
            $callFileHistory->records_processed = $recordCount;
            $callFileHistory->records_inserted = $recordsInserted;
            $callFileHistory->records_errored = $recordsErrored;
            $callFileHistory->records_dup = $recordsDup;

            $callFileHistory->save(); 
        }
    }
}

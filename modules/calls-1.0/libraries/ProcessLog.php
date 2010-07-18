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

        $recordCount = 0;

        $callFiles = glob(rtrim(Kohana::config('calls.scandir'), '/') . '/' . 'Master.csv.*', GLOB_MARK);
        Kohana::log('debug', 'scandir: ' . Kohana::config('calls.scandir'));
        foreach( $callFiles as $callFile ) {
            if (($callFileHandle = fopen($callFile, "r")) !== FALSE) {
                while (($callRecord = fgetcsv($callFileHandle, 2000, ",")) !== FALSE) {
                    $numFields = count($callRecord);
                    $recordCount++;
                    Kohana::log('debug'. "$numFields fields in record $recordCount");
                    $cdr=array();
                    for ($field=0; $field < $numFields; $field=$field+2) {
                        $cdr[$callRecord[$field]]=$callRecord[$field+1];
                    }
                    Kohana::log('debug', 'Call Log: ' . print_r($cdr,true));
                }
                fclose($callFileHandle);
            }
        }
    }
}

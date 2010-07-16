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
    }
}

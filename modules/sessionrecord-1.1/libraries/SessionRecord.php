<?php defined('SYSPATH') or die('No direct access allowed.');

class SessionRecord
{

    private function getBasePath() {
        return '/usr/local/freeswitch/recordings/';
    }

    private function getRecordingExtension() {
        return '.wav';
    }

    public function getFile($uuid) {
        return self::getBasePath() . $uuid . self::getRecordingExtension();
    }


}

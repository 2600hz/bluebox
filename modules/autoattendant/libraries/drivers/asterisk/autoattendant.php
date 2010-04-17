<?php

class Asterisk_AutoAttendant_Driver extends Asterisk_Base_Driver {
    /**
     * Indicate we support Asterisk for Auto Attendants
     */
    public static function set($obj)
    {
    }

    public static function delete($obj)
    {
    }

    public static function dialplan($obj)
    {
        $doc = Telephony::getDriver()->doc;

        $doc->setCurrentNumber('i');
        $doc->add('Return', 1);

        $doc->setCurrentNumber('t');
        $doc->add('Return', 1);

        $doc->setCurrentNumber('h');
        $doc->add('Return', 1);

        foreach ($obj->AutoAttendant->AutoAttendantKey as $key) {
            $doc->setCurrentNumber($key->auto_attendant_key);
            $doc->add('Goto(number_' . $key->number_id . ',' .$key->Number->number .',1)',1);
        }

        // Don't forget to set the position back to where it was before
        $doc->setCurrentNumber('_X.');
        
        $doc->add('Answer', 1);
        $doc->add('Set(TIMEOUT(digit)=' . $obj->AutoAttendant->digit_timeout . ')'); //TODO
        $doc->add('Set(TIMEOUT(response)=' . $obj->AutoAttendant->timeout . ')'); //TODO

        $doc->add('Set(f=3)');  // Initialize counter
        // TODO: implement "say" command in Asterisk land
        if ($obj->AutoAttendant->file_id) {
            $doc->add('Background("' . FileManager::getFilePath($obj->AutoAttendant->file_id) . '")', 'REPEAT');
        } else {
            $doc->add('WaitExten(20)', 'REPEAT');
        }
        $doc->add('WaitExten(5)');
        $doc->add('Set(f=$[${f} - 1])');
        $doc->add('GotoIf($[${f} > 0]?REPEAT)');

    }
}

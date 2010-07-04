<?php defined('SYSPATH') or die('No direct access allowed.');

class TimeOfDay_Plugin extends Bluebox_Plugin {
    private $updateNumbers = FALSE;

    public function checkChanges() {
        // get the base ring group
        $base = $this->timeOfDay;

        // if that failed get out of here!
        if (!$base || empty($base->time_of_day_id))
            return FALSE;

        // get a list of the modified fields
        $modified = $base->getModified();

        // if any of the fields that exist in the dialplan where modified then
        // set the flag to dirty any numbers associated with this ringgroup
        if (!empty($modified)) {
            kohana::log('debug', 'Flagging time-of-day as needing a diaplan rebuild.');
            $this->updateNumbers = TRUE;
        }
    }

    public function dirtyNumbers()
    {
        if (!$this->updateNumbers) return;

        // get the base ring group
        $base = $this->timeOfDay;

        // if that failed get out of here!
        if (!$base || empty($base->time_of_day_id))
            return FALSE;

        // get all the number records for this ring group
        $query = Doctrine_Query::create()
            ->select('number_id, number')
            ->from('Number')
            ->where('foreign_id = ?', $base->time_of_day_id)
            ->andWhere('class_type = ?', 'TimeOfDayNumber');

        // dirty each record we got so the dialplan will be re-generated
        $results = $query->execute();
        foreach ($results as $result) {
            $result->markModified('number');
            $result->save();
        }
    }
}
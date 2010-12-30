<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * @author Your Name <your@email.org>
 * @license Your License
 * @package _Skeleton
 */
class CallRecord_Plugin extends Bluebox_Plugin
{
    protected $name = 'callrecord';

    public function recordings()
    {
     	$this->subview = new View('callrecord/recordings');

        $this->subview->tab = 'main';

        $this->subview->section = 'xmlcdr';

        $details = '';
     	$base = $this->getBaseModelObject();

        if ($base instanceof Xmlcdr) {
            if(file_exists(CallRecord::getFile($base->uuid))) {
                $details .= 'A-leg recording: ' . $this->playLink($base->uuid) . '<br>';
            } 
            if(file_exists(CallRecord::getFile($base->bleg_uuid))) {
                $details .= 'B-leg recording: ' . $this->playLink($base->bleg_uuid) . '<br>';
            }
            // In case blue.box finds nothing:
            if($details == '') {
                $details = 'No recordings found!';
            }
        }

        $this->subview->details = $details;

        if (!$this->addSubView())
        {
            return FALSE;
        }

        return TRUE;
    }

    public function playLink($uuid) {

        return sprintf('<audio src="%s" type="audio/wav" controls="controls">No audio tag support</audio>',  url::site('/callrecord/listen/' . $uuid));

    }

}
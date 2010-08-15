<?php defined('SYSPATH') or die('No direct access allowed.');

class SessionRecord_Plugin extends Bluebox_Plugin
{

    protected $name = 'sessionrecord';

    public function viewSetup()
    {
     	$this->subview = new View('sessionrecord/update');

        $this->subview->tab = 'main';

        $this->subview->section = 'general';

        return TRUE;
    }

    public function update()
    {
     	if (!$this->viewSetup())
        {
            return FALSE;
        }

        $details = '';

#        $details = '<h3>Listen</h3>';

     	$base = $this->getBaseModelObject();
    
        if ($base instanceof Xmlcdr) {
            if(file_exists(SessionRecord::getFile($base->uuid))) {
                $details .= $this->playLink($base->uuid);
            } else {
                $details .= 'No file found';
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
          
        return sprintf('<audio src="%s" type="audio/wav" controls="controls">No audio tag suport</audio>',  url::site('/sessionrecord/listen/' . $uuid));
                
    }

}

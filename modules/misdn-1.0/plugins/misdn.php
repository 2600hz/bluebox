<?php


class Misdn_Plugin extends Bluebox_Plugin
{
    public function index()
    {
        //        $this->grid->add('Misdn/provider', 'ISDN Provider', array(
        //            'width' => '80',
        //            'align' => 'center'
        //        ));
    }

    public function view()
    {
        if($this->getBaseModelObject() instanceof Trunk)
        {
            $this->supportedTypes['MisdnTrunk'] = 'mISDN Trunk';
        }
        
        if(!($this->getBaseModelObject() instanceof MisdnTrunk))
        return;

        $subview = new View('misdn/update');
        $subview->section = 'general';
        $subview->tab = 'main';

        // What are we working with here?
        $base = $this->getBaseModelObject();

        // While the field may be defined by an app or another module, it may not be populated! Double-check
        if (!$base)
        return FALSE; // Nothing to do here.

        // If we don't have a Sip object, create a dummy one.
        // NOTE: This inherently lets other modules know that this module is installed while providing blank/default entries for our view.
        // Because this is created on the view page and NOT on a save page, this will NOT result in an empty record being saved.
        if (!$base->Misdn) {
            $base->Misdn = new Misdn();
        }

        // Populate form data with whatever our database record has
        $subview->misdn = $base->Misdn->toArray();

        // Pass errors from the controller to our view
        $subview->errors = $this->errors();

        // If we are coming from a previous form field/post, we want to repopulate the previous field entries again on this page so
        // that errors/etc. can be corrected, rather then lost.
        if (isset($this->repopulateForm) && isset($this->repopulateForm['misdn'])) {
            $subview->misdn = arr::overwrite($subview->misdn, $this->repopulateForm['misdn']);
        }

        // Add our view to the main application
        $this->views[] = $subview;
    }


    public function save()
    {
        if(!($this->getBaseModelObject() instanceof MisdnTrunk))
        return;

        // What are we working with here?
        $base = $this->getBaseModelObject();

        // While the field may be defined by an app or another module, it may not be populated! Double-check
        if (!$base)
        return FALSE; // Nothing to do here.

        if (!$base->Misdn) {
            $base->Misdn = new Misdn();
        }

        $form = $this->input->post('misdn');
        $fieldNames = array('provider');

        foreach ($fieldNames as $fieldName) {
            if (isset($form[$fieldName]))
            $base->Misdn->$fieldName = $form[$fieldName];
        }
    }

}
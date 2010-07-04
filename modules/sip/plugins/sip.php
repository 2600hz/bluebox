<?php defined('SYSPATH') or die('No direct access allowed.');
/*
 * Bluebox Modular Telephony Software Library / Application
 *
 * The contents of this file are subject to the Mozilla Public License Version 1.1 (the 'License');
 * you may not use this file except in compliance with the License. You may obtain a copy of the License at
 * http://www.mozilla.org/MPL/.
 *
 * Software distributed under the License is distributed on an 'AS IS' basis, WITHOUT WARRANTY OF ANY KIND, either
 * express or implied. See the License for the specific language governing rights and limitations under the License.
 *
 * The Original Code is Bluebox Telephony Configuration API and GUI Framework.
 * The Original Developer is the Initial Developer.
 * The Initial Developer of the Original Code is Darren Schreiber
 * All portions of the code written by the Initial Developer and Bandwidth, Inc. are Copyright © 2008-2009. All Rights Reserved.
 *
 * Contributor(s):
 *
 *
 */

/**
 * sip.php - SIP support - adds support for devices, trunks, etc. that use SIP.
 * This module provides features relevant for connecting SIP-based PBXes, SIP phones and other SIP devices and trunks.
 *
 * @author Darren Schreiber <d@d-man.org>
 * @license MPL
 * @package Bluebox
 * @subpackage Sip
 */

class Sip_Plugin extends Bluebox_Plugin
{
    //protected $preloadModels = array('Sip', 'SipDevice', 'SipTrunk');

    // Whether or not to restrict username changes in Sip devices
    public static $lockusername = FALSE;

    public function index()
    {
        $this->grid->add('Sip/username', 'SIP Username', array(
						'width' => '80',
						'align' => 'center'
//        /*						'link' => array(
//         'link' => Router::$controller . '/edit',
//         'arguments' => 'device_id'
//         )*/
        ));
    }

    public function view()
    {
        // check if we're dealing with Trunk and add SIP as trunk type
        if($this->getBaseModelObject() instanceof Trunk)
        {
            $this->supportedTypes['SipTrunk'] = 'SIP Trunk';
        }
        
        // What are we working with here?
        $base = $this->getBaseModelObject();

        // check if we have a sip trunk and return if not
        if(!($base instanceof SipTrunk) and !($base instanceof SipDevice) and !($base instanceof Device)) {
                return;
        }
          
        $subview = new View('sip/update');
        $subview->section = 'general';
        $subview->tab = 'main';

        // While the field may be defined by an app or another module, it may not be populated! Double-check
        if (!$base)
        return FALSE;	// Nothing to do here.

        // If we don't have a Sip object, create a dummy one.
        // NOTE: This inherently lets other modules know that this module is installed while providing blank/default entries for our view.
        // Because this is created on the view page and NOT on a save page, this will NOT result in an empty record being saved.
        if (!$base->Sip) {
            $base->Sip = new Sip();
        }

        // Populate form data with whatever our database record has
        $subview->sip = $base->Sip->toArray();

        // Pass errors from the controller to our view
        $subview->errors = $this->errors();

        // If we are coming from a previous form field/post, we want to repopulate the previous field entries again on this page so
        // that errors/etc. can be corrected, rather then lost.
        if (isset($this->repopulateForm) && isset($this->repopulateForm['sip'])) {
            $subview->sip = arr::overwrite($subview->sip, $this->repopulateForm['sip']);
        }

        $subview->lockusername = self::$lockusername;

        // Add our view to the main application
        $this->views[] = $subview;
    }

    public function save()
    {
        // check if we have a sip trunk and return if not
        if(!($this->getBaseModelObject() instanceof SipTrunk) and !($this->getBaseModelObject() instanceof SipDevice))
          return;
        
        // What are we working with here?
        $base = $this->getBaseModelObject();

        // While the field may be defined by an app or another module, it may not be populated! Double-check
        if (!$base)
        return FALSE;	// Nothing to do here.

        if (!$base->Sip) {
            $base->Sip = new Sip();
        }

        $form = $this->input->post('sip');
        $fieldNames = array('password', 'cid_format', 'sip_invite_format');

        // Are we allowed to set the username?
        if (self::$lockusername != TRUE) {
            $fieldNames[] = 'username';
        }

        // TODO: Move this to the model
        //Bluebox_Controller::$validation->add_callbacks('sip', array($this, '_strong_pwd'));

        foreach ($fieldNames as $fieldName) {
            if (isset($form[$fieldName]))
            $base->Sip->$fieldName = $form[$fieldName];
        }
    }

    public function delete()
    {
        // What are we working with here?
        $base = Event::$data;

        // While the field may be defined by an app or another module, it may not be populated! Double-check
        if (!$base)
            return FALSE;	// Nothing to do here.

        if ($base->hasRelation('Sip')) {
            $base->Sip->delete();
        }
        
        if ($base->hasRelation('SipInterfaceTrunk')) {
            $base->SipInterfaceTrunk->delete();
        }
    }

    // TODO: Move this to the model
    public function _strong_pwd(Validation $array, $field)
    {
        if (!preg_match('`[0-9]`',$array['sip']['password'])) { // at least one digit
            $array->add_error('sip[password]', 'nodigits');
        }
        if (!preg_match('`[A-Za-z]`',$array['sip']['password'])) { // at least one character
            $array->add_error('sip[password]', 'noalpha');
        }
    }

}

<?php defined('SYSPATH') or die('No direct access allowed.');
/*
 * FreePBX Modular Telephony Software Library / Application
 *
 * The contents of this file are subject to the Mozilla Public License Version 1.1 (the 'License');
 * you may not use this file except in compliance with the License. You may obtain a copy of the License at
 * http://www.mozilla.org/MPL/.
 *
 * Software distributed under the License is distributed on an 'AS IS' basis, WITHOUT WARRANTY OF ANY KIND, either
 * express or implied. See the License for the specific language governing rights and limitations under the License.
 *
 * The Original Code is FreePBX Telephony Configuration API and GUI Framework.
 * The Original Developer is the Initial Developer.
 * The Initial Developer of the Original Code is Darren Schreiber
 * All portions of the code written by the Initial Developer and Bandwidth, Inc. are Copyright © 2008-2009. All Rights Reserved.
 *
 * Contributor(s):
 *
 * Michael Phillips
 *
 * @license MPL
 * @package FreePBX3
 * @subpackage VoicemailToEmail
 */

class Voicemail_Plugin extends FreePbx_Plugin
{
    protected $preloadModels = array('VoicemailPrefs', 'DeviceVoicemail', 'UserVoicemail');

    public function selector() {
        $subview = new View('voicemail/selector');
        $subview->section = 'voicemail';

        $boxes = Doctrine::getTable('Voicemail')->findByAccountId(users::$user->Location->account_id);

        $voicemailBoxes = array('check_vm' => 'Check VM');
        foreach($boxes as $box) {
            $voicemailBoxes[$box->voicemail_id] = $box->name;
        }
        $subview->voicemailBoxes = $voicemailBoxes;

        // Add our view to the main application
        $this->views[] = $subview;

    }

    public function listVoicemailBoxes() {
        $view = new View('voicemail/listVoicemailBoxes');
        $view->tab = 'main';
        $view->section = 'features';

        // What are we working with here?
        $base = $this->getBaseModelObject();

        // While the field may be defined by an app or another module, it may not be populated! Double-check
        if (!$base)
            return FALSE;	// Nothing to do here.
        if (!$base->Voicemail) {
            $base->Voicemail = new Voicemail();
        }
        $view->voicemail = $base->Voicemail->toArray();


        $boxes = Doctrine::getTable('Voicemail')->findByAccountId(users::$user->Location->account_id);
        $voicemailBoxes = array('0' => 'None');
        foreach($boxes as $box) {
            $voicemailBoxes[$box->voicemail_id] = $box->mailbox;
        }
        $view->voicemailBoxes = $voicemailBoxes;

        // Add our view to the main application
        $this->views[] = $view;
    }

    public function save()
    {
        // What are we working with here?
        $base = $this->getBaseModelObject();

        // While the field may be defined by an app or another module, it may not be populated! Double-check
        if (!$base)
            return FALSE;	// Nothing to do here.

        // get the voicemail_id from the dropdown
        $id = &$_POST['voicemail']['voicemail_id'];
        if (empty($id)) {
            // try to remove these voicemail settings from the base
            if (!empty($base->Voicemail->foreign_id) and ($base->Voicemail->voicemail_id > 0)) {
                //$base->Voicemail->unlink('Device');
                // Regenerate the numbers formerly attached to this
                /*$this->_dirtyNumbers = array('type' => 'VoicemailNumber', 'id' => $base->Voicemail->foreign_id);*/

                $base->Voicemail->class_type = NULL;
                $base->Voicemail->foreign_id = 0;
            } else {
                unset ($base->Voicemail);
            }
        } else {
            // try to get the voicemail settings and attach them to this base
            $box = Doctrine::getTable('Voicemail')->findOneByVoicemailId($id);

            // if we failed to find this box return
            if (!$box) {
                return FALSE;
            }

            if (($base instanceof Device) and ($base->class_type == 'SipDevice')) {
                $box->class_type = 'DeviceVoicemail';
            }

            $base->Voicemail = $box;
            /*$this->_dirtyNumbers = array($box->class_type, 'id' => $base->Voicemail->foreign_id);*/
            //$this->dirtyNumbers($base);
        }
    }

    /*private function dirtyNumbers($obj) {
        if ($this->input->post('containsAssigned')) //hidden input to let us know the page needs to have assigned numbers processed
        {
            $primaryKeyCol = $obj->_table->getIdentifier(); //get the column name, like confernce_id
            $foreign_id = $obj->$primaryKeyCol; //get the keuy value

            if (get_parent_class($obj) == 'FreePbx_Record') $class_type = get_class($obj) . 'Number'; //transform to class name
            else $class_type = get_parent_class($obj) . 'Number'; //transform to original parent's class name

            $results = Doctrine_Query::create()
                ->select('number_id')
                ->from('Number')
                ->where('foreign_id = ?', $foreign_id)
                ->andWhere('class_type = ?', $class_type)
                ->execute();

            foreach ($results as $result) {
                Telephony::set($result);
            }
        }
    }*/
}


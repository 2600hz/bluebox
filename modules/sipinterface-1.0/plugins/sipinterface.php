<?php defined('SYSPATH') or die('No direct access allowed.');
/* 
 *  Bluebox Modular Telephony Software Library / Application
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
 * All portions of the code written by the Initial Developer are Copyright © 2008-2009. All Rights Reserved.
 * 
 * Contributor(s):
 * 
 */

/**
 * sipinterface.php - sipinterface class
 * Created on Aug 22, 2009
 *
 * @author Darren Schreiber <d@d-man.org>
 * @license MPL
 */
class SipInterface_Plugin extends Bluebox_Plugin
{
    protected $preloadModels = array('SipInterfaceTrunk');

    public function index()
    {
/*		$this->grid->add('Sip/username', 'SIP Username', array(
						'width' => '80',
						'align' => 'center'
					));
 */
    }

    public function view()
    {
        $subview = new View('sipinterface/select');
        $subview->section = 'routing';
        $subview->tab = 'main';

        // What are we working with here?
        $base = $this->getBaseModelObject();

        // While the field may be defined by an app or another module, it may not be populated! Double-check
        if (!$base)
            return FALSE;	// Nothing to do here.

        $subview->options = array();
        $result = Doctrine::getTable('SipInterface')->findAll(Doctrine::HYDRATE_ARRAY);
        foreach ($result as $row) {
            $subview->options[$row['sipinterface_id']] = $row['name'];
        }

        $subview->sipinterface = $base->SipInterfaceTrunk->SipInterface;

        // Add our view to the main application
        $this->views[] = $subview;
    }

    public function save()
    {
        // What are we working with here?
        $base = $this->getBaseModelObject();

        // While the field may be defined by an app or another module, it may not be populated! Double-check
        if (!$base) {
            return FALSE;	// Nothing to do here.
        }

        // Is there an actual mapping to save?
        if (!isset($_POST['sipinterface']['sipinterface_id'])) {
            return FALSE;       // Nothing to do here.
        }

        // Prep the relation
        if (!$base->SipInterfaceTrunk) {
            $base->SipInterfaceTrunk = new SipInterfaceTrunk();
            $base->SipInterfaceTrunk->Trunk = $base;
        }

        $base->SipInterfaceTrunk->sipinterface_id = $_POST['sipinterface']['sipinterface_id'];
    }
}

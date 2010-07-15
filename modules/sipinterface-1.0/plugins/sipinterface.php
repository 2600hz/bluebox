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
    protected $name = 'sipinterface';

    protected function viewSetup()
    {
        $this->subview = new View('sipinterface/select');

        $this->subview->tab = 'main';

        $this->subview->section = 'general';

        $subview->options = array();

        $result = Doctrine::getTable('SipInterface')->findAll(Doctrine::HYDRATE_ARRAY);

        foreach ($result as $row)
        {
            $subview->options[$row['sipinterface_id']] = $row['name'];
        }

        return TRUE;
    }
}

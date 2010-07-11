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
* All portions of the code written by the Initial Developer are Copyright © 2008-2009. All Rights Reserved.
*
* Contributor(s):
* K Anderson
*
*/
/**
 * sipinterface.php - Sip Interface Management Controller Class
 *
 * This class manages sip profiles in FreeSWITCH. It is a FreeSWITCH specific module.
 *
 * @author Darren Schreiber <d@d-man.org>
 * @license MPL
 * @package Bluebox
 * @subpackage SipInterface
 */
class SipInterface_Controller extends Bluebox_Controller
{
    protected $baseModel = 'SipInterface';
    protected $writable = array(
        'name',
        'ip_address',
        'port',
        'ext_ip_address',
        'auth',
        'multiple',
        'nat_type',
        'nat_net_list_id',
        'inbound_net_list_id',
        'register_net_list_id',
        'context_id'
    );
    public function index()
    {
        $this->template->content = new View('generic/grid');
        // Build a grid with a hidden sipinterface_id, sipinterface info, and add an option for the user to select the display columns
        $this->grid = jgrid::grid($this->baseModel, array(
            'caption' => 'SIP Interfaces',
            'multiselect' => true
        ))->add('sipinterface_id', 'ID', array(
            'hidden' => true,
            'key' => true
        ))->add('name', 'SIP Interface Name')->add('ip_address', 'IP Address')->add('port', 'Port')->add('Context/name as context', 'Default Context', array(
            'colModel' => 'context'
        ))->navButtonAdd('Columns', array(
            'onClickButton' => 'function () {  $(\'#{table_id}\').setColumns(); }',
            'buttonimg' => url::base() . 'assets/css/jqGrid/table_insert_column.png',
            'title' => 'Show/Hide Columns',
            'noCaption' => true,
            'position' => 'first'
        ))->addAction('sipinterface/edit', 'Edit SIP Interface', array(
            'arguments' => 'sipinterface_id',
            'width' => '120'
        ))->addAction('sipinterface/delete', 'Delete SIP Interface', array(
            'arguments' => 'sipinterface_id',
            'width' => '200'
        ));
        // dont foget to let the plugins add to the grid!
        plugins::views($this);
        // Produces the grid markup or JSON, respectively
        $this->view->grid = $this->grid->produce();
    }
    public function add()
    {
        // Use the edit view here, too
        $this->template->content = new View(Router::$controller . '/update');
        $this->view->title = 'Add SIP Interface';

        $this->sipInterface = new SipInterface();
        $this->sipInterface['nat_net_list_id'] = netlists::getSystemListId('nat.auto');
        // Are we supposed to be saving stuff? (received a form post?)
        if ($this->submitted()) {
            if (!isset($_POST['sipinterface']['auth'])) {
                $this->sipInterface->auth = FALSE;
            }
            if (!isset($_POST['behind_nat']) or ($_POST['behind_nat'] == 0)) {
                $this->sipInterface->nat_type = 0;
                unset ($_POST['sipinterface']['nat_type']);
            }
            if ($this->formSave($this->sipInterface)) {
                url::redirect(Router_Core::$controller);
            }
        }
        // Allow our sip interface object to be seen by the view
        $this->view->sipinterface = $this->sipInterface;
        // Execute plugin hooks here, after we've loaded the core data sets
        plugins::views($this);
    }
    public function edit($id = NULL)
    {
        $this->template->content = new View(Router::$controller . '/update');
        $this->view->title = 'Edit SIP Interface';
        
        $this->sipInterface = Doctrine::getTable('SipInterface')->find($id);
        // Was anything retrieved? If no, this may be an invalid request
        if (!$this->sipInterface) {
            // Send any errors back to the index
            $error = i18n('Unable to locate SIP Interface id %1$d!', $id)->sprintf()->s();
            message::set($error, array(
                'translate' => false,
                'redirect' => Router::$controller . '/index'
            ));
            return true;
        }
        // Are we supposed to be saving stuff? (received a form post?)
        if ($this->submitted()) {
            if (!isset($_POST['sipinterface']['auth'])) {
                $this->sipInterface->auth = FALSE;
            }
            if (!isset($_POST['behind_nat']) or ($_POST['behind_nat'] == 0)) {
                $this->sipInterface->nat_type = 0;
                unset ($_POST['sipinterface']['nat_type']);
            }
            if ($this->formSave($this->sipInterface)) {
                url::redirect(Router_Core::$controller);
            }
        }

        // Manually check "behind_nat" if necessary
        $this->view->behind_nat = ($this->sipInterface->nat_type > 0 ? TRUE : FALSE);

        // dont foget to let the plugins add to the grid!
        plugins::views($this);
        // Allow our sipinterface object to be seen by the view
        $this->view->sipinterface = $this->sipInterface;
    }
    public function delete($id = NULL)
    {
        $this->stdDelete($id);
    }
}

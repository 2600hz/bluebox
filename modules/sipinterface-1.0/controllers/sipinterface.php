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
    
    public function index()
    {
        $this->template->content = new View('generic/grid');

        // Setup the base grid object
        $grid = jgrid::grid($this->baseModel, array(
                'caption' => 'SIP Interfaces',
            )
        );

        // Add the base model columns to the grid
        $grid->add('sipinterface_id', 'ID', array(
                'hidden' => true,
                'key' => true
            )
        );
        $grid->add('name', 'Interface Name');
        $grid->add('ip_address', 'IP Address', array(
                'callback' => array($this, '_showIp')
            )
        );
        $grid->add('port', 'Port');
        //$grid->add('Context/name', 'Default Context');
        $grid->add('auth', 'Authentication', array(
                'callback' => array($this, '_showAuth')
            )
        );

        // Add the actions to the grid
        $grid->addAction('sipinterface/edit', 'Edit', array(
                'arguments' => 'sipinterface_id'
            )
        );
        $grid->addAction('sipinterface/delete', 'Delete', array(
                'arguments' => 'sipinterface_id'
            )
        );

        // Let plugins populate the grid as well
        $this->grid = $grid;
        plugins::views($this);

        // Produce a grid in the view
        $this->view->grid = $this->grid->produce();
    }

    public function prepareUpdateView()
    {
        $this->view->behind_nat = FALSE;

        if (!empty($this->sipinterface['nat_type']))
        {
            $this->view->behind_nat = TRUE;
        }

        // This is not the best way to to do this but I am mimicing the
        // behavior that was already here
        if (Router::$method == 'add')
        {
            $this->sipinterface['nat_net_list_id']
                    = netlists::getSystemListId('nat.auto');
        }

        parent::prepareUpdateView();
    }

    public function _showIp($ip)
    {
        if(empty($ip)) {
            return 'Auto Detect';
        } else {
            return $ip;
        }
    }

    public function _showAuth($auth)
    {
        if ($auth) {
            return 'Required';
        } else {
            return 'None';
        }
    }

    public function save_prepare(&$object)
    {
        if (!isset($_POST['sipinterface']['auth']))
        {
            $object['auth'] = FALSE;
        }

        if (!isset($_POST['behind_nat']) or ($_POST['behind_nat'] == 0))
        {
            $object['nat_type'] = 0;
        }
    }
}

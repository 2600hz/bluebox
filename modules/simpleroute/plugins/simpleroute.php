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
 *
 */

/**
 * simpleroute.php - A simple routing engine for FreeSWITCH outbound trunks
 *
 * @author Darren Schreiber <d@d-man.org>
 * @license MPL
 * @package FreePBX3
 * @subpackage SimpelRoute
 */

class SimpleRoute_Plugin extends FreePbx_Plugin
{
    protected $preloadModels = array('SimpleRouteContext');

    public function index()
    {
        
    }

    public function update()
    {
        $subview = new View('simpleroute/update');
        $subview->tab = 'main';
        $subview->section = 'routing';

        // What are we working with here?
        Doctrine::initializeModels('SimpleRouteContext');
        $base = $this->getBaseModelObject();

        // While the field may be defined by an app or another module, it may not be populated! Double-check
        if (!$base)
            return FALSE;	// Nothing to do here.

        // If we don't have a SimpleRoute object, create a dummy one.
        // NOTE: This inherently lets other modules know that this module is installed while providing blank/default entries for our view.
        // Because this is created on the view page and NOT on a save page, this will NOT result in an empty record being saved.
        if (!$base->SimpleRoute) {
            $base->SimpleRoute = new SimpleRoute();
        }

        // Populate form data with whatever our database record has
        $subview->simpleroute = $base->SimpleRoute->toArray();

        // Display available contexts
        $subview->contexts = Doctrine::getTable('Context')->findAll();
        $subview->checkedSimpleRouteContext = array();
        if ($base->SimpleRoute->SimpleRouteContext) {
            foreach ($base->SimpleRoute->SimpleRouteContext as $tmp) {
                $subview->checkedSimpleRouteContext[$tmp['context_id']] = TRUE;
            }
        }

//        $subview->checkedSimpleRouteContext = arr::overwrite($subview->checkedSimpleRouteContext, $this->repopulateForm['checkedSimpleRouteContext']);

        // If we are coming from a previous form field/post, we want to repopulate the previous field entries again on this page so
        // that errors/etc. can be corrected, rather then lost.
        if (isset($this->repopulateForm) and isset($this->repopulateForm['simpleroute'])) {
            $subview->simpleroute = arr::overwrite($subview->simpleroute, $this->repopulateForm['simpleroute']);
        }

        // Add our view to the main application
        $this->views[] = $subview;
    }

    public function save()
    {
        // What are we working with here?
        $base = $this->getBaseModelObject();
        
        // While the field may be defined by an app or another module, it may not be populated! Double-check
        if (!$base)
            return FALSE;	// Nothing to do here.

        if ((!$base->SimpleRoute) or (!$base->SimpleRoute->simple_route_id)) {
            $base->SimpleRoute = new SimpleRoute();
        }

        // get the values from the form
        $form = $this->input->post('simpleroute');

        if (!isset($form['SimpleRouteContext'])) {
            $form['SimpleRouteContext'] = array();
        } else {
            // remove any contexts that are 0 (unchecked)
            foreach($form['SimpleRouteContext'] as $key => $simpleRouteContext) {
                if (empty($simpleRouteContext['context_id'])) {
                    unset($form['SimpleRouteContext'][$key]);
                }
            }
        }

        // sync this up
        $base->SimpleRoute->SimpleRouteContext->synchronizeWithArray(array());
        $base->SimpleRoute->synchronizeWithArray($form);
    }

    public function delete()
    {
        $base = Event::$data;

        if (!$base)
            return FALSE;

        if ($base->hasRelation('SimpleRoute')) {
            $base->SimpleRoute->delete();
        }
    }
}

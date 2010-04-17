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
* K Anderson
*
*/
/**
 * numbermanager.php - Number Management Controller Class
 *
 * @author Darren Schreiber <d@d-man.org>
 * @license MPL
 * @package FreePBX3
 * @subpackage NumberManager
 */
class NumberManager_Controller extends FreePbx_Controller
{
    protected $writable = array(
        'number',
        'class_type',
        'foreign_id'
    );
    
    protected $baseModel = 'Number';

    public function index()
    {
        $this->template->content = new View('generic/grid');
        // Build a grid with a hidden number_id, phone number, and add an option for the user to select the display columns
        $this->grid = jgrid::grid($this->baseModel, array(
            'caption' => 'Number Routing',
            'multiselect' => TRUE
        ))->add('number_id', 'ID', array(
            'hidden' => true,
            'key' => true
        ))->add('number', 'Number', array(
            'callback' => array(
                $this,
                'formatNumber'
            )
        ))->add('number_route', 'Routes to', array(
            'callback' => array(
                'arguments' => 'number_id',
                'function' => array(
                    $this,
                    'lookupModuleDescription'
                )
            )
/*        ))->add('context_id', 'Context', array(
            'callback' => array(
                $this,
                'lookupContext'
            )*/
        ))->add('Location/name', 'Location', array(
            'width' => '150',
            'search' => false,
        ))->navButtonAdd('Columns', array(
            'onClickButton' => 'function () {  $(\'#{table_id}\').setColumns(); }',
            'buttonimg' => url::base() . 'assets/css/jqGrid/table_insert_column.png',
            'title' => 'Show/Hide Columns',
            'noCaption' => true,
            'position' => 'first'
        ))->addAction('numbermanager/edit', 'Edit', array(
            'arguments' => 'number_id',
            'width' => '120'
        ))->addAction('numbermanager/delete', 'Delete', array(
            'arguments' => 'number_id',
            'width' => '120'
        ))->navGrid(array(
            'del' => true
        ));
        // dont foget to let the plugins add to the grid!
        plugins::views($this);
        // Produces the grid markup or JSON, respectively
        $this->view->grid = $this->grid->produce();
    }

    public function formatNumber($cell = '')
    {
        switch (strlen($cell)) {
        case 10:
            return '(' . substr($cell, 0, 3) . ') ' . substr($cell, 3, 3) . '-' . substr($cell, 6, 4);
        default:
            return $cell;
        }
    }
    
    public function lookupModuleDescription($null, $cell)
    {
        // TODO: We need to optimize this with a DQL query if possible
        $number = Doctrine::getTable('Number')->find($cell);
        if (($number['class_type'] == '') and ($number['foreign_id'] == 0)) return '';
        $stuff = Doctrine::getTable(str_replace('Number', '', $number['class_type']))->find($number['foreign_id']);
        $base = substr($number['class_type'], 0, strlen($number['class_type']) - 6) . ' ' . $number['foreign_id'];
        $base = __($base);
        if (isset($stuff->name)) return $base . ' (' . $stuff->name . ')';
        else return $base;
    }

    public function lookupContext($null, $cell) {
        echo 'GOT HERE';
        return $null . $cell;
    }

    public function add()
    {
        // Overload the update view
        $this->template->content = new View(Router::$controller . '/update');
        $this->view->title = 'Add a Number';
        $this->number = new Number();
        // Are we supposed to be saving stuff? (received a form post?)
        if ($this->submitted()) {
            // TODO: This is horribly ugly. My lord. Please fix.
            if (isset($_POST['destination'])) {
                parse_str($_POST['destination']['destination_1'], $destination);
                $_POST['number'] = array_merge($_POST['number'], $destination['number']);
            }
            
            $contexts = $_POST['number']['NumberContext'];
            foreach ($contexts as $key => $context) {
                if (empty($context['context_id'])) {
                    unset($_POST['number']['NumberContext'][$key]);
                }
            }
            $pools = $_POST['number']['NumberPool'];
            foreach ($pools as $key => $pool) {
                if (empty($pool['number_type_id'])) {
                    unset($_POST['number']['NumberPool'][$key]);
                }
            }
            $this->number->synchronizeWithArray($_POST['number']);
            // Allow empty destinations
            if (!isset($_POST['number']['foreign_id'])) {
                $_POST['number']['class_type'] = NULL;
                $_POST['number']['foreign_id'] = 0;
            }
            if ($this->formSave($this->number)) {
                url::redirect(Router::$controller);
            }
        }
        // Allow our number object to be seen by the view
        $this->view->number = $this->number;
        // Display available contexts
        $this->view->contexts = Doctrine::getTable('Context')->findAll();
        $this->view->checkedNumberContext = array();
        foreach($this->number->NumberContext as $tmp) {
            $this->view->checkedNumberContext[$tmp['context_id']] = TRUE;
        }
        // Display available number types, and pre-populated checkboxes
        $this->view->numberTypes = Doctrine::getTable('NumberType')->findAll();
        $this->view->checkedNumberTypes = array();
        foreach($this->number->NumberPool as $tmp) {
            $this->view->checkedNumberTypes[$tmp['number_type_id']] = TRUE;
        }
        // Execute plugin hooks here, after we've loaded the core data sets
        plugins::views($this);
    }

    public function edit($id = NULL)
    {
        // Overload the update view
        $this->template->content = new View(Router::$controller . '/update');
        $this->view->title = 'Edit a Number';
        // We must ensure all number models are pre-loaded so the relationships are setup before loading
        // TODO: This should be done automagically by polymorphic behavior
        $results = Doctrine::getTable('NumberType')->findAll(Doctrine::HYDRATE_ARRAY);
        foreach($results as $result) {
            $initModels[] = $result['class'];
        }
        Doctrine::initializeModels($initModels);
        $this->view->title = 'Edit a Number';
        $this->number = Doctrine::getTable('Number')->find($id);
        // Was anything retrieved? If no, this may be an invalid request
        if (!$this->number) {
            // Send any errors back to the index
            $error = i18n('Unable to locate number id %1$d!', $id)->sprintf()->s();
            message::set($error, array(
                'translate' => false,
                'redirect' => Router::$controller . '/index'
            ));
            return true;
        }
        
        // Are we supposed to be saving stuff? (received a form post?)
        if ($this->submitted()) {
            // TODO: This is horribly ugly. My lord. Please fix.
            if (isset($_POST['destination'])) {
                parse_str($_POST['destination']['destination_1'], $destination);
                $_POST['number'] = array_merge($_POST['number'], $destination['number']);
            }

            $contexts = $_POST['number']['NumberContext'];
            foreach ($contexts as $key => $context) {
                if (empty($context['context_id'])) {
                    unset($_POST['number']['NumberContext'][$key]);
                }
            }
            $pools = $_POST['number']['NumberPool'];
            foreach ($pools as $key => $pool) {
                if (empty($pool['number_type_id'])) {
                    unset($_POST['number']['NumberPool'][$key]);
                }
            }

            $this->number->NumberContext->synchronizeWithArray(array());
            $this->number->NumberPool->synchronizeWithArray(array());
            $this->number->synchronizeWithArray($_POST['number']);
            if (!isset($_POST['number']['foreign_id']) || !$_POST['number']['foreign_id']) {
                $_POST['number']['class_type'] = NULL;
                $_POST['number']['foreign_id'] = 0;
            }
            if ($this->formSave($this->number)) {
                url::redirect(Router::$controller);
            }
        }
        // Allow our number object to be seen by the view
        $this->view->number = $this->number;
        // Display available contexts
        $this->view->contexts = Doctrine::getTable('Context')->findAll();
        $this->view->checkedNumberContext = array();
        foreach($this->number->NumberContext as $tmp) {
            $this->view->checkedNumberContext[$tmp['context_id']] = TRUE;
        }
        // Display available number types, and pre-populated checkboxes
        $this->view->numberTypes = Doctrine::getTable('NumberType')->findAll();
        $this->view->checkedNumberTypes = array();
        foreach($this->number->NumberPool as $tmp) {
            $this->view->checkedNumberTypes[$tmp['number_type_id']] = TRUE;
        }
        // Execute plugin hooks here, after we've loaded the core data sets
        plugins::views($this);
    }

    public function bulkadd()
    {
        // Use the edit view here, too
        $this->view->title = 'Add Multiple Numbers';
        // Are we supposed to be saving stuff? (received a form post?)
        if ($this->submitted()) {
            $errors = '';
            $contexts = $_POST['number']['NumberContext'];
            foreach ($contexts as $key => $context) {
                if (empty($context['context_id'])) {
                    unset($_POST['number']['NumberContext'][$key]);
                }
            }
            $pools = $_POST['number']['NumberPool'];
            foreach ($pools as $key => $pool) {
                if (empty($pool['number_type_id'])) {
                    unset($_POST['number']['NumberPool'][$key]);
                }
            }
            // Allow empty destinations
            if (!$_POST['number']['foreign_id']) {
                $_POST['number']['class_type'] = NULL;
                $_POST['number']['foreign_id'] = 0;
            }
            if (($_POST['end_number'] > $_POST['start_number']) and !empty($_POST['start_number'])) {
                if (($_POST['end_number'] - $_POST['start_number']) < 10000) {
                    for ($numberIterator = $_POST['start_number']; $numberIterator <= $_POST['end_number']; $numberIterator++) {
                        $this->number = new Number();
                        $this->number->synchronizeWithArray($_POST['number']);
                        $this->number->number = number_format($numberIterator, 0, '.', '');
                        try {
                            $this->number->save();
                        }
                        catch(Exception $e) {
                            // TODO: Display numbers that could not be added more elegantly! It also needs to be processed by i18n!
                            $errors.= 'Unable to add ' . $numberIterator . "! " . $e->getMessage() . "<BR>\n";
                        }
                    }
                    if ($errors) {
                        message::set('Add completed with errors:<BR>' . $errors);
                    } else {
                        message::set('Completed adding numbers!', array(
                            'type' => 'success'
                        ));
                    }
                } else {
                    message::set('You can add only a maximum of 10,000 numbers at a time.');
                }
                url::redirect(Router::$controller);
            }
            message::set('Start Number must be less than End number');
        }
        // Display available contexts
        $this->view->contexts = Doctrine::getTable('Context')->findAll();
        // Display available number types, and pre-populated checkboxes
        $this->view->numberTypes = Doctrine::getTable('NumberType')->findAll();
        // Allow our number object to be seen by the view
        $this->view->start_number = '';
        $this->view->end_number = '';
        $this->view->context = '';
        // Execute plugin hooks here, after we've loaded the core data sets
        plugins::views($this);
    }

    public function delete($id = NULL)
    {
        $this->stdDelete($id, NULL, 'number');
    }
}

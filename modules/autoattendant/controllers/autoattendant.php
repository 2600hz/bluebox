<?php
/**
 * AutoAttendant_Controller.php - AutoAttendant Controller
 *
 * Handles AutoAttendant creation, editing and management
 *
 * @author Michael Phillips
 * @package FreePBX3
 * @subpackage AutoAttendant
 */
class AutoAttendant_Controller extends FreePbx_Controller
{
    protected $baseModel = 'AutoAttendant';
    protected $writable = array(
        'name',
        'type',
        'timeout',
        'digit_timeout', 
        'description',
        'auto_attendant',
        'tts_string',
        'audio_file',
        'file_id',
        'extension_context_id',
        'extension_digits'
    );

    public function __construct()
    {
        parent::__construct();
        stylesheet::add('autoattendant', 50);
    }

    public function index()
    {
        $this->template->content = new View('generic/grid');
        // Buidl a grid with a hidden device_id, device_type, and add an option for the user to select the display columns
        $this->grid = jgrid::grid($this->baseModel, array(
            'caption' => 'My Auto Attendants',
            'multiselect' => true
        ))->add('auto_attendant_id', 'ID', array(
            'hidden' => true,
            'key' => true
        ))->add('name', 'Name', array(
            'width' => '200',
            'search' => false,
        ))->add('description', 'Description', array(
            'width' => '300',
            'search' => false,
        ))->navButtonAdd('Columns', array(
            'onClickButton' => 'function () {  $(\'#{table_id}\').setColumns(); }',
            'buttonimg' => url::base() . 'assets/css/jqGrid/table_insert_column.png',
            'title' => 'Show/Hide Columns',
            'noCaption' => true,
            'position' => 'first'
        ))->addAction('autoattendant/edit', 'Edit', array(
            'arguments' => 'auto_attendant_id',
            'width' => '120'
        ))->addAction('autoattendant/delete', 'Delete', array(
            'arguments' => 'auto_attendant_id',
            'width' => '120'
        ))->navGrid(array(
            'del' => true
        ));
        // dont foget to let the plugins add to the grid!
        plugins::views($this);
        // Produces the grid markup or JSON
        $this->view->grid = $this->grid->produce();
    }
    
    public function add()
    {
        $this->template->content = new View(Router::$controller . '/update');
        $this->view->title = ucfirst(Router::$method) .' Auto Attendant';
        $this->autoAttendant = new AutoAttendant();

        // Are we supposed to be saving stuff? (received a form post?)
        if ($this->submitted()) {
            // make each key a subarray with auto_attendant_key and number_id keys
            $keys = $this->input->post('keys', array());
            $autoAttendantKeys = array();
            foreach($keys as $key) {
                // if the number id is 0 then skip
                if (empty($key['number_id'])) continue;

                $destination = $key['number_id'];

                $numberParts = explode('_', $key['number_id']);
                if (count($numberParts) > 1) {
                    $key['number_id'] = $numberParts[0];
                }

                $autoAttendantKeys[] = array (
                    'auto_attendant_key' => $key['digits'],
                    //'destination' => $destination,
                    'number_id' => $key['number_id']
                );                
            }

            // sync the group members with the group
            $autoAttendantKeys = array('AutoAttendantKey' => $autoAttendantKeys);
            $this->autoAttendant->synchronizeWithArray($autoAttendantKeys);

            if ($this->formSave($this->autoAttendant)) {
                url::redirect(Router_Core::$controller);
            }
        }
        // Allow our device object to be seen by the view
        $this->view->autoattendant = $this->autoAttendant;
        $this->view->promptArray = array('' => '', 'tts' => 'Text to Speech','audio' => 'Audio File');

        // populate the keys
        $keys = array();
        foreach ($this->autoAttendant->AutoAttendantKey as $key) {
            $keys[$key['auto_attendant_key']] = $key['number_id'];
            //$keys[$key['auto_attendant_key']] = $key['destination'];
        }
        $this->view->keys = $keys;

        // Execute plugin hooks here, after we've loaded the core data sets
        plugins::views($this);
    }

    private function populateKeys(&$autoAttendant)
    {
        $autoAttendant->auto_attendant_key = 3;
        $autoAttendant->number_id = 3;
    }

    public function edit($id = NULL)
    {
        $this->template->content = new View(Router::$controller . '/update');
        $this->view->title = ucfirst(Router::$method) .' Auto Attendant';
        $this->autoAttendant = Doctrine::getTable('AutoAttendant')->find($id);

        // Was anything retrieved? If no, this may be an invalid request
        if (!$this->autoAttendant) {
            // Send any errors back to the index
            $error = i18n('Unable to locate Auto Attendant id %1$d!', $id)->sprintf()->s();
            message::set($error, array(
                'translate' => false,
                'redirect' => Router::$controller . '/index'
            ));
            return true;
        }
        
        if ($this->submitted()) {
            // make each key a subarray with auto_attendant_key and number_id keys
            $keys = $this->input->post('keys', array());
            $autoAttendantKeys = array();
            foreach($keys as $key) {
                // if the number id is 0 then skip
                if (empty($key['number_id'])) continue;

                $destination = $key['number_id'];

                $numberParts = explode('_', $key['number_id']);
                if (count($numberParts) > 1) {
                    $key['number_id'] = $numberParts[0];
                }

                $autoAttendantKeys[] = array (
                    'auto_attendant_key' => $key['digits'],
                    //'destination' => $destination,
                    'number_id' => $key['number_id']
                );
            }

            // sync the group members with the group
            $this->autoAttendant->AutoAttendantKey->synchronizeWithArray(array());
            $this->autoAttendant->AutoAttendantKey->synchronizeWithArray($autoAttendantKeys);

            // Force rebuild of dialplan for these numbers
            $this->autoAttendant->_dirtyNumbers = array('type' => 'AutoAttendantNumber', 'id' => 'auto_attendant_id');

            if ($this->formSave($this->autoAttendant)) {
                url::redirect(Router_Core::$controller);
            }
        }

        // Allow our device object to be seen by the view
        $this->view->autoattendant = $this->autoAttendant;
        $this->view->promptArray = array('' => '', 'tts' => 'Text to Speech','audio' => 'Audio File');

        // populate the keys
        $keys = array();
        foreach ($this->autoAttendant->AutoAttendantKey as $key) {
            $keys[$key['auto_attendant_key']] = $key['number_id'];
            //$keys[$key['auto_attendant_key']] = $key['destination'];
        }
        $this->view->keys = $keys;

        // Execute plugin hooks here, after we've loaded the core data sets
        plugins::views($this);
    }

    public function delete($id)
    {
        $this->stdDelete($id);
    }
}

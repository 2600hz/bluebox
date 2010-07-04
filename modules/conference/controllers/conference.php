<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * conferencemanager.php - ConferenceManagement Controller Class
 *
 * @author Karl Anderson
 * @license MPL
 * @package Bluebox
 * @subpackage ConferenceManager
 */
class Conference_Controller extends Bluebox_Controller
{
    protected $writable = array('name', 'room_pin', 'record', 'record_location', 'comfort_noise', 'moh_type', 'moh_file', 'conference_soundmap_id');

    protected $baseModel = 'Conference';

    public function index()
    {
        $this->template->content = new View('generic/grid');

        // Build a grid with a hidden conference_id, class_type, and add an option for the user to select the display columns
        $this->grid = jgrid::grid($this->baseModel, array('caption' => 'Conferences', 'multiselect' => true))
            ->add('conference_id', 'ID', array('hidden' => true, 'key' => true))
            ->add('name', 'Name')
            ->add('record', 'Record?', array(
            'callback' => array($this, '_showRecord')
            ))
            ->navButtonAdd('Columns', array(
            'onClickButton' => 'function () {  $(\'#{table_id}\').setColumns(); }',
            'buttonimg' => url::base() . 'assets/css/jqGrid/table_insert_column.png',
            'title' => 'Show/Hide Columns',
            'noCaption' => true,
            'position' => 'first'
            ))
            ->addAction('conference/edit', 'Edit', array(
            'arguments' => 'conference_id',
            'width' => '120'
            ))
            ->addAction('conference/delete', 'Delete', array(
            'arguments' => 'conference_id',
            'width' => '20'
            ))
            ->navGrid(array('del' => true));

        // dont foget to let the plugins add to the grid!
        plugins::views($this);

        // Produces the grid markup or JSON, respectively
        $this->view->grid = $this->grid->produce();
    }

    public function edit($id = NULL)
    {
    // Overload the update view
        $this->template->content = new View(Router::$controller . '/update');
        $this->view->title = ucfirst(Router::$method) .' Conference';

        $this->conference = Doctrine::getTable('Conference')->find($id);

        // Was anything retrieved? If no, this may be an invalid request
        if (!$this->conference) {
        // Send any errors back to the index
            $error = i18n('Unable to locate conference id %1$d!', $id)->sprintf()->s();
            message::set($error, array('translate' => false, 'redirect' => Router::$controller));
            return true;
        }

        // Are we supposed to be saving stuff? (received a form post?)
        if ($this->submitted()) {

            switch($_POST['conference']['moh_type']) {
                case 'silence':
                    $_POST['conference']['moh_type'] = 0;
                    $_POST['conference']['moh_file'] = '';
                    break;
                case 'moh':
                    $_POST['conference']['moh_type'] = 1;
                    $_POST['conference']['moh_file'] = '';
                    break;
                default:
                    $_POST['conference']['moh_file'] = $_POST['conference']['moh_type'];
                    $_POST['conference']['moh_type'] = 2;
            }

            // TODO: Get rid of this required default
            $this->conference->conference_soundmap_id = 1;

            // Force regeneration of dialplan after this change for all associated numbers
            $this->dirtyNumbers($this->conference);


            if ($this->formSave($this->conference)) {
                url::redirect(Router::$controller);
            }
        }

        switch ($this->conference['moh_type']) {
            case 0:
                $this->conference['moh_type'] = 'silence';
                break;
            case 1:
                $this->conference['moh_type'] = 'moh';
                break;
            default:
                $this->conference['moh_type'] = $this->conference['moh_file'];
        }

        // Allow our conference object to be seen by the view
        $this->view->conference = $this->conference;

        $mohTypeOptions = array (
            'silence' => 'Silence / No Music',
            'moh' => 'Use Caller / Global Defaults',
        );

        if (class_exists('FileManager')) {
            $files = FileManager::ls(array('audio'));
            foreach ($files as $file) {
                $mohTypeOptions[$file['file_id']] = $file['name'];
            }
        }

        $this->view->mohTypeOptions = $mohTypeOptions;


        // TODO: The grid system below doesn't work yet.
        /*$this->grid = jgrid::grid('ConferencePins', array(
            'caption' => 'Conference Pins',
            'height' => 100,
            'width' => '100%',
            'autowidth' => true
            ))
            ->add('conference_pin_id', 'ID', array('hidden' => true, 'key' => true))
            ->add('name', 'Name')
            ->add('pin', 'Pin')
            ->add('type', 'Pin Type', array(
            'callback' => array($this, '_showPinType')
            ))
            ->navButtonAdd('Add', array(
            'onClickButton' => '/test/',
            'buttonimg' => url::base() . 'assets/css/jqGrid/table_insert_column.png',
            'title' => 'Add Pin',
            'noCaption' => true,
            'position' => 'first'
            ))
            ->addAction('conference/pin/edit', 'Edit Pin', array(
            'arguments' => 'conference_pin_id',
            'width' => '120'
            ))
            ->addAction('conference/pin/delete', 'Delete Pin', array(
            'arguments' => 'conference_pin_id',
            'width' => '120'
        ));*/

        // dont foget to let the plugins add to the grid!
        plugins::views($this);

        // Produces the grid markup or JSON, respectively
        //$this->view->grid = $this->grid->produce();
    }

    public function add()
    {
        // Overload the update view
        $this->template->content = new View(Router::$controller . '/update');
        $this->view->title = ucfirst(Router::$method) .' Conference';
        
        $this->conference = new Conference();
        $this->conference['moh_type'] = 'moh';

        // Are we supposed to be saving stuff? (received a form post?)
        if ($this->submitted()) {

            switch($_POST['conference']['moh_type']) {
                case 'silence':
                    $_POST['conference']['moh_type'] = 0;
                    $_POST['conference']['moh_file'] = '';
                    break;
                case 'moh':
                    $_POST['conference']['moh_type'] = 1;
                    $_POST['conference']['moh_file'] = '';
                    break;
                default:
                    $_POST['conference']['moh_file'] = $_POST['conference']['moh_type'];
                    $_POST['conference']['moh_type'] = 2;
            }

            // TODO: Get rid of this required default
            $this->conference->conference_soundmap_id = 1;

            if ($this->formSave($this->conference)) {
                url::redirect(Router::$controller);
            }
        }

        // Allow our conference object to be seen by the view
        $this->view->conference = $this->conference;

        $mohTypeOptions = array (
            'silence' => 'Silence / No Music',
            'moh' => 'Use Caller / Global Defaults',
        );

        if (class_exists('FileManager')) {
            $files = FileManager::ls(array('audio'));
            foreach ($files as $file) {
                $mohTypeOptions[$file['file_id']] = $file['name'];
            }
        }

        $this->view->mohTypeOptions = $mohTypeOptions;

        // Execute plugin hooks here, after we've loaded the core data sets
        plugins::views($this);
    }

    public function delete($id = NULL)
    {
        $this->stdDelete($id);
    }

    public function _showRecord($cell) {
        return ($cell ? __('Yes') : __('No'));
    }

    public function _showPinType($cell) {
        return (ConferencePins::TYPE_MODERATOR ? __('Moderator') : __('Member'));
    }

    private function dirtyNumbers($obj) {
        if ($this->input->post('containsAssigned')) //hidden input to let us know the page needs to have assigned numbers processed
        {
            $primaryKeyCol = $obj->_table->getIdentifier(); //get the column name, like confernce_id
            $foreign_id = $obj->$primaryKeyCol; //get the keuy value

            if (get_parent_class($obj) == 'Bluebox_Record') $class_type = get_class($obj) . 'Number'; //transform to class name
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
    }
}

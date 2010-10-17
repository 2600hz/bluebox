<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * Provisioner.php - This class is the controller for the provisioner module
 *
 * @author Karl Anderson
 * @author Michael Phillips
 * @license MPL
 * @package Provisioner
 */
class Provisioner_Controller extends Bluebox_Controller
{
    public $writable = array(
        'mac',
        'endpoint_model_id',
        'provision_path',
        'chroot'
    );
    protected $baseModel = 'Endpoint';
    protected $noAuth = array();
    public function index()
    {
        $this->template->content = new View('generic/grid');
        $this->grid = jgrid::grid('Endpoint', array(
            'caption' => 'My Endpoints',
            'multiselect' => true
        ))->add('endpoint_id', 'ID', array(
            'hidden' => true,
            'key' => true
        ))->add('endpoint_model_id', 'Model', array(
            'callback' => array(
                'EndpointManager',
                'getModelName'
            )
        ))->add('mac', 'MAC Address', array(
            'width' => '80',
            'search' => true
        ))->navButtonAdd('Columns', array(
            'onClickButton' => 'function () {  $(\'#{table_id}\').setColumns(); }',
            'buttonimg' => url::base() . 'assets/css/jqGrid/table_insert_column.png',
            'title' => 'Show/Hide Columns',
            'noCaption' => true,
            'position' => 'first'
        ))->addAction('provisioner/edit', 'Edit Endpoint', array(
            'arguments' => 'mac',
            'width' => '120'
        ))->addAction('provisioner/files', 'Show Files', array(
            'arguments' => 'mac',
            'width' => '120'
        ))->navGrid(array('del' => TRUE));
        plugins::views($this);
        $this->view->grid = $this->grid->produce();
    }
    public function vendors()
    {
        $this->template->content = new View('generic/grid');
        $this->grid = jgrid::grid('EndpointVendor', array(
            'caption' => 'Supported Vendors'
        ))->add('endpoint_vendor_id', 'ID', array(
            'hidden' => true,
            'key' => true
        ))->add('vendor', 'Vendor')->add('oui', 'OUI', array(
            'width' => '80',
            'search' => true,
        ))->add('description', 'Description', array(
            'width' => '80',
            'search' => true,
        ))->navButtonAdd('Columns', array(
            'onClickButton' => 'function () {  $(\'#{table_id}\').setColumns(); }',
            'buttonimg' => url::base() . 'assets/css/jqGrid/table_insert_column.png',
            'title' => 'Show/Hide Columns',
            'noCaption' => true,
            'position' => 'first'
        ));
        plugins::views($this);
        $this->view->grid = $this->grid->produce();
    }
    public function models()
    {
        $this->template->content = new View('generic/grid');
        $this->grid = jgrid::grid('EndpointModel', array(
            'caption' => 'Supported Models'
        ))->add('endpoint_model_id', 'ID', array(
            'hidden' => true,
            'key' => true
        ))->add('description', 'Description', array(
            'width' => '80',
            'search' => true,
        ))->add('EndpointVendor/driver', 'Driver', array(
            'width' => '80',
            'search' => true,
        ))->navButtonAdd('Columns', array(
            'onClickButton' => 'function () {  $(\'#{table_id}\').setColumns(); }',
            'buttonimg' => url::base() . 'assets/css/jqGrid/table_insert_column.png',
            'title' => 'Show/Hide Columns',
            'noCaption' => true,
            'position' => 'first'
        ));
        plugins::views($this);
        $this->view->grid = $this->grid->produce();
    }
    public function scan()
    {
        $this->cache = Cache::instance();
        if ($this->input->post()) {
            $devices = DeviceScanner::getInstance();
            $cidr = sprintf('%s/%s', $this->input->post('ip') , $this->input->post('cidr'));
            $result = $devices->setCIDR($cidr)->scanDevices()->toArray();
            $this->view->devices = $result;
            $this->cache->set('scan', $result, null, 600);
        } else {
            if (is_null($this->cache->get('scan'))) {
                $this->view->devices = array();
            } else {
                $this->view->devices = $this->cache->get('scan');
            }
        }
    }
    public function help()
    {
    }
    public function settings()
    {
        $this->view->title = 'Global Settings';
        $this->endpointsetting = Doctrine::getTable('EndpointSetting')->findAll();

        if ($this->endpointsetting[0]) {
            $this->endpointsetting = $this->endpointsetting[0];
        } else {
            $this->endpointsetting = new EndpointSetting();
        }

        if ($this->submitted()) {
            if (empty($_POST['write_to_disk'])) {
                $this->endpointsetting['provision_path'] = NULL;
            }

            if ($this->formSave($this->endpointsetting)) {
                url::redirect(Router_Core::$controller);
            }
        }

        if (!empty($this->endpointsetting['provision_path'])){
            $this->view->write_to_disk = TRUE;
        }

        $this->view->endpointsetting = $this->endpointsetting;
    }
    public function edit($mac = NULL)
    {
        $this->view->title = 'Edit Provision Settings';
        $this->driver = EndpointManager::getDriver($mac);
        $this->endpoint = $this->driver->getEndpoint();
        // Was anything retrieved? If no, this may be an invalid request
        if (!$this->endpoint) {
            // Send any errors back to the index
            $error = i18n('Unable to locate endpoint with mac %1$d!', $id)->sprintf()->s();
            message::set($error, array(
                'translate' => false,
                'redirect' => Router::$controller . '/index'
            ));
            return true;
        }
        // Are we supposed to be saving stuff? (received a form post?)
        if ($this->submitted()) {
            // Ensure the user can not change the mac, vendor or model
            if (isset($_POST['endpoint'])) {
                unset($_POST['endpoint']);
            }
            // cache the files/folders that belong to the old data
            $this->driver->deleteFiles(TRUE);

            // Add our own custom event to the list and call all plugin save events
            $driveEvents = array(
                Router::$controller . '.' . strtolower(get_class($this->driver))
            );
            $saveStatus = $this->formSave($this->endpoint, NULL, $driveEvents);

            // refresh the driver
            $this->driver->refresh();
            $this->endpoint = $this->driver->getEndpoint();

            // if the save was successfull then update our files
            if (!empty($saveStatus)) {
                $this->driver->createFiles();
                url::redirect(Router_Core::$controller);
            }
        }
        // Update the identification information
        $this->view->vendor = $this->driver->vendor();
        $this->view->model = $this->driver->model();
        $this->view->mac = $this->driver->mac(TRUE);
        // Add our own custom event to the list and call all plugin view events
        $driveEvents = array(
            Router::$controller . '.' . Router::$method . '.' . strtolower(get_class($this->driver))
        );
        plugins::views($this, $driveEvents);
        $this->view->endpoint = $this->endpoint;
    }
    public function add($mac = null, $ip = null)
    {
        $this->view->title = 'Provision New Device';
        $this->view->errors = array();
        $this->endpoint = new Endpoint();
        // If this is from the scanner then the mac is passed as a parameter
        if (!is_null($mac)) {
            $this->endpoint->mac = $mac;
        }
        // Are we supposed to be saving stuff? (received a form post?)
        if ($this->submitted()) {
            // First clean up the mac address of any non-valid chars
            $_POST['endpoint']['mac'] = preg_replace('/[^0-9a-fA-F]*/', '', $_POST['endpoint']['mac']);
            // Because this is a 'fake' var and not actually in db it will not get updated automaticly.
            //$this->endpoint->endpoint_vendor_id = $_POST['endpoint']['endpoint_vendor_id'];
            // Set the parameters and options to a placeholder for now
            $this->endpoint->parameters = $this->endpoint->options = array();
            try {
                // Attempt to get a driver for this model, this is
                // simply to catch any throws
                $this->driver = EndpointManager::createEndpoint($_POST['endpoint']['mac'], $_POST['endpoint']['endpoint_model_id']);
            }
            catch(Exception $e) {
                $driverError = $e->getMessage();
            }
            // Attempt to save this provision, we need to do this even if the
            // driver errored out to repopulate the form and give any plugins a chance
            if (empty($driverError) && $this->formSave($this->endpoint)) {
                url::redirect(Router_Core::$controller . '/edit/' . $this->endpoint->mac);
            }
            if (!empty($driverError)) message::set($driverError);
        }
        // Load a list of vendors for the view
        $this->view->vendors = EndpointManager::listVendors($this->endpoint->mac);
        $this->view->models = EndpointManager::listModels($this->endpoint->endpoint_vendor_id);
        // Make our MAC preaty again
        if (strlen($this->endpoint->mac) == 12) $this->endpoint->mac = preg_replace('/([0-9a-fA-F]{2})(?!$)/', '$1:', $this->endpoint->mac);
        // Execute plugin hooks here, after we've loaded the core data sets
        plugins::views($this);
        $this->view->endpoint = $this->endpoint;
    }
    public function get()
    {
        switch ($_REQUEST['type']) {
        case 'models':
            echo json_encode(EndpointManager::listModels($_REQUEST['id']));
            break;

        case 'file':
            echo EndpointManager::getDriver($_REQUEST['mac'])->getFile($_REQUEST['file'], TRUE);
            break;

        case 'binary':
            EndpointManager::getDriver($_REQUEST['mac'])->getFile($_REQUEST['file']);
            break;
        }
        die();
    }
    public function files($mac)
    {
        try {
            $driver = EndpointManager::getDriver($mac);
            $this->view->tree = $driver->getTree();
            $this->view->mac = $mac;
        }
        catch(Exception $e) {
            message::set($e->getMessage());
            url::redirect(Router_Core::$controller);
        }
    }
}
?>


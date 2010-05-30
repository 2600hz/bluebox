<?php
/**
 * ExternalXfer_Controller.php - ExternalXfer Controller
 *
 * Allows you to transfer calls to an external SIP URI or trunk/phone number combination
 *
 * @author Darren Schreiber
 * @license MPL
 * @package FreePBX3
 * @subpackage ExternalXfer
 */
class ExternalXfer_Controller extends FreePbx_Controller
{
    protected $baseModel = 'ExternalXfer';
    protected $writable = array(
        'name',
        'xml',
        'description'
    );

    public function index()
    {
        $this->template->content = new View('generic/grid');
        // Buidl a grid with a hidden device_id, device_type, and add an option for the user to select the display columns
        $this->grid = jgrid::grid($this->baseModel, array(
            'caption' => 'External Destination',
            'multiselect' => true
        ))->add('feautre_code_id', 'ID', array(
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
        ))->addAction('externalxfer/edit', 'Edit', array(
            'arguments' => 'external_xfer_id',
            'width' => '120'
        ))->addAction('externalxfer/delete', 'Delete', array(
            'arguments' => 'external_xfer_id',
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
        $this->view->title = ucfirst(Router::$method) .' External Destination';
        $this->externalXfer = new ExternalXfer();

        // Are we supposed to be saving stuff? (received a form post?)
        if ($this->submitted()) {
            if ($this->formSave($this->externalXfer)) {
                url::redirect(Router_Core::$controller);
            }
        }
        $tmp = Doctrine::getTable('Trunk')->findAll(Doctrine::HYDRATE_ARRAY);
        $this->view->trunks = array();
        foreach ($tmp as $trunk) {
            $this->view->trunks[$trunk['trunk_id']] = $trunk['name'];
        }

        if (class_exists('SipInterface', TRUE)) {
            $tmp = Doctrine::getTable('SipInterface')->findAll(Doctrine::HYDRATE_ARRAY);
            $this->view->interfaces = array();
            foreach ($tmp as $interface) {
                $this->view->interfaces[$interface['sipinterface_id']] = $interface['name'];
            }
        }

        // Allow our device object to be seen by the view
        $this->view->externalXfer = $this->externalXfer;

        // Execute plugin hooks here, after we've loaded the core data sets
        plugins::views($this);
    }

    public function edit($id = NULL)
    {
        $this->template->content = new View(Router::$controller . '/update');
        $this->view->title = ucfirst(Router::$method) .' External Destination';
        $this->externalXfer = Doctrine::getTable('ExternalXfer')->find($id);

        // Was anything retrieved? If no, this may be an invalid request
        if (!$this->externalXfer) {
            // Send any errors back to the index
            $error = i18n('Unable to locate External Destination id %1$d!', $id)->sprintf()->s();
            message::set($error, array(
                'translate' => false,
                'redirect' => Router::$controller . '/index'
            ));
            return true;
        }

        if ($this->submitted()) {
            // Force rebuild of dialplan for these numbers
            $this->externalXfer->_dirtyNumbers = array('type' => 'ExternalXferNumber', 'id' => 'external_xfer_id');

            if ($this->formSave($this->externalXfer)) {
                url::redirect(Router_Core::$controller);
            }
        }

        $tmp = Doctrine::getTable('Trunk')->findAll(Doctrine::HYDRATE_ARRAY);
        $this->view->trunks = array();
        foreach ($tmp as $trunk) {
            $this->view->trunks[$trunk['trunk_id']] = $trunk['name'];
        }

        if (class_exists('SipInterface', TRUE)) {
            $tmp = Doctrine::getTable('SipInterface')->findAll(Doctrine::HYDRATE_ARRAY);
            $this->view->interfaces = array();
            foreach ($tmp as $interface) {
                $this->view->interfaces[$interface['sipinterface_id']] = $interface['name'];
            }
        }

        // Allow our destination object to be seen by the view
        $this->view->externalxfer = $this->externalXfer;

        // Execute plugin hooks here, after we've loaded the core data sets
        plugins::views($this);
    }

    public function delete($id)
    {
        $this->stdDelete($id);
    }
}

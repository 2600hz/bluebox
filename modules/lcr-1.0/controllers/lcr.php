<?php
/**
 * This is the skeleton controller template.
 * Replace this text with a description of what your module or pages do.
 *
 * All public methods here that are not prefixed with _ are accessible via
 * /MyModule/methodname (where "MyModule" is the name of the
 * controller class and "methodname" is the name of the method you define).
 * As an example, if this class is named Voicemail_Controller with a method
 * definition of 'public function send()', you can access it via:
 *      http://myserver/frepbx/voicemail/send
 *
 * You can add your own custom routes by adding the
 * appropriate routing file per Kohana's specifications.
 *
 * Views are automatically rendered for each method unless you
 * override this functionality. The view file rendered, by default, is
 * /views/controllername/methodname
 * Using the above example, the view for
 * http://myserver/bluebox/voicemail/send would live in
 * /views/voicemail/send
 *
 * To set variables that will be accessible in the view, use the format:
 * $this->view->myvariable = $myvariable;
 * You can then reference those variables as $myvariable within the view itself.
 *
 *
 */
/**
 * lcr.php
 *
 * @author Raymond Chandler <intralanman@gmail.com>
 * @license BSD
 * @package Bluebox
 * @subpackage LCR
 */
class Lcr_Controller extends Bluebox_Controller
{
    /**
     * Base model for this controller
     * @var class
     */
    protected $baseModel = 'Lcr';
    /**
     * Writable fields for this model
     * @var array
     */
    protected $writable = array(
        'digits',
        'rate',
        'intrastate_rate',
        'intralata_rate',
        'carrier_id',
        'lead_strip',
        'trail_strip',
        'prefix',
        'suffix',
        'lcr_profile',
        'date_start',
        'date_end',
        'quality',
        'reliability',
        'cid',
        'enabled'
    );
    /**
     * Base index page
     * @return void
     */
    public function index()
    {
        $this->grid = jgrid::grid($this->baseModel, array(
            'caption' => 'LCR Table',
            'multiselect' => true
        ))->add('id', 'ID', array(
            'hidden' => true,
            'key' => true
        ))->add('digits', 'Pattern')->add('rate', 'Interstate Cost')->add('intrastate_rate', 'Intrastate Cost')->add('intralata_rate', 'Intralata Cost')->add('Provider/provider_name', 'Carrier')
        //->add('lcr_profile','LCR Group')
        ->navButtonAdd('Columns', array(
            'onClickButton' => 'function () {  $(\'#{table_id}\').setColumns(); }',
            'buttonimg' => url::base() . 'assets/css/jqGrid/table_insert_column.png',
            'title' => 'Show/Hide Columns',
            'noCaption' => true,
            'position' => 'first'
        ))->addAction('lcr/edit', 'Edit LCR Entry', array(
            'arguments' => 'id',
            'width' => '120'
        ))->addAction('lcr/delete', 'Delete LCR Entry', array(
            'arguments' => 'id',
            'width' => '200'
        ))->navGrid(array(
            'del' => true
        ));
        // dont forget to let the plugins add to the grid!
        plugins::views($this);
        // Produces the grid markup or JSON
        $this->view->grid = $this->grid->produce();
    }
    /**
     * Add page
     * @return void
     */
    public function add()
    {
        // Use the edit view here, too
        $this->template->content = new View(Router::$controller . '/edit');
        $this->view->errors = array();
        $newLcr = new Lcr();
        if (sizeof($this->input->post()) != 0) {
            if ($this->formSave($newLcr)) {
                url::redirect(Router_Core::$controller);
            }
        }
        // Allow our lcr object to be seen by the view
        $this->view->Lcr = $newLcr;
        $this->view->carriers = $this->getCarriers();
        // Execute plugin hooks here, after we've loaded the core data sets
        plugins::views($this);
    }
    /**
     * Edit page
     * @param integer $id
     * @return void
     */
    public function edit($id)
    {
        $model = $this->baseModel;
        $record = Doctrine::getTable($this->baseModel)->find($id);
        if (!$record) {
            Kohana::show_404();
        }
        if (sizeof($this->input->post()) != 0) {
            if ($this->formSave($record)) {
                url::redirect(Router_Core::$controller . "/edit/$id");
            }
        }
        $this->view->$model = $record;
        $this->view->carriers = $this->getCarriers();
    }
    /**
     * Search Page
     * @return void
     */
    public function search()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $lcr = $this->input->post('lcr');
        } else {
            $lcr = $this->input->get();
        }
        Kohana::log('debug', print_r($lcr, true));
        if (!empty($lcr['digits'])) {
            $this->grid = jgrid::grid($this->baseModel, array(
                'caption' => 'LCR Table',
                'postData' => $lcr
            ))->add('id', 'ID', array(
                'hidden' => true,
                'key' => true
            ))->add('digits', 'Pattern')->add('rate', 'Interstate Cost')->add('intrastate_rate', 'Intrastate Cost')->add('intralata_rate', 'Intralata Cost')->add('Carriers/carrier_name', 'Carrier')
            //->add('lcr_profile', 'LCR Group')
            ->navButtonAdd('Columns', array(
                'onClickButton' => 'function () {  $(\'#{table_id}\').setColumns(); }',
                'buttonimg' => url::base() . 'assets/css/jqGrid/table_insert_column.png',
                'title' => 'Show/Hide Columns',
                'noCaption' => true,
                'position' => 'first'
            ))->addAction('lcr/edit', 'Edit LCR Entry', array(
                'arguments' => 'id',
                'width' => '120'
            ))->addAction('lcr/delete', 'Delete LCR Entry', array(
                'arguments' => 'id',
                'width' => '200'
            ));
            $this->grid->where("digits", "LIKE '${lcr['digits']}%'");
            // dont forget to let the plugins add to the grid!
            plugins::views($this);
            if (!empty($lcr['lcr_profile'])) {
                Kohana::log('debug', 'lcr profile is not empty, adding to query');
                $this->grid->andWhere("lcr_profile", "= '${lcr['lcr_profile']}' OR lcr_profile IS NULL");
            } else {
                Kohana::log('debug', 'lcr profile is empty, not adding to query');
            }
            if (!empty($lcr['valid_date'])) {
                Kohana::log('debug', 'date is not empty, adding to query');
                $this->grid->andWhere("date_start", "< '${lcr['valid_date']}' OR date_start IS NULL");
                $this->grid->andWhere("date_end", "> '${lcr['valid_date']}' OR date_end IS NULL");
            } else {
                Kohana::log('debug', 'date is empty, not adding to query');
            }
            // Produces the grid markup or JSON
            $this->view->grid = $this->grid->produce();
        }
    }
    /**
     *
     * @param integer $id
     * @return void
     */
    public function delete($id)
    {
        $this->template->content = new View(Router::$controller . '/delete');
        $controller = 'Lcr';
        // If delete is called with no $id produce and error and stop
        if (is_null($id)) {
            message::set('Unable to process delete request, invalid entry point!', array(
                'redirect' => Router::$controller . '/index'
            ));
            return true;
        }
        // Wrap the doctrine query just incase it throws
        try {
            //Wow, really need to use conservative fetching here and also andWhere(user_id) for security
            $row = Doctrine::getTable($this->baseModel)->find($id);
            // If we where unable to find a row by this id produce an error and stop
            if (!$row) {
                $error = i18n('Unable to process delete request, can not locate id %1$s!', $id)->sprintf()->s();
                message::set($error, array(
                    'translate' => false,
                    'redirect' => Router::$controller . '/index'
                ));
                return true;
            }
        }
        catch(Exception $e) {
            $error = __('Error during database operation!');
            $error.= '<div><small>' . __($e->getMessage()) . '</small></div>';
            message::set($error, array(
                'translate' => false,
                'redirect' => Router::$controller . '/index'
            ));
            return true;
        }
        // Get the post vars
        if ($this->input->post()) {
            // Check if the confirm form was submitted with the confirm button
            if ($this->input->post('confirm')) {
                // Wrap the doctrine delete incase it throws
                try {
                    $row->delete();
                    message::set('Delete Succeeded', array(
                        'type' => 'success'
                    ));
                }
                catch(Exception $e) {
                    $error = '<div>' . __('Delete Failed!') . '</div><small>' . __($e->getMessage()) . '</small>';
                    message::set($error, array(
                        'translate' => false
                    ));
                }
            } else {
                // If the confirm form was submitted by any other method, ignore it
                message::set('Delete Cancelled', array(
                    'type' => 'info'
                ));
            }
            // Send any form submition back to the index on completion
            url::redirect(Router_Core::$controller . '/index');
        }
        // Share the name and id with the form
        $this->view->id = $id;
        if (!empty($row->name)) {
            $this->view->name = $row->name;
        } else {
            $this->view->name = '';
        }
    }
    /**
     * Method to change settings for the module
     */
    public function settings()
    {
        define('XPATH_DEBUG', true);
        $xpathAttr = '/settings/param[@name="odbc-dsn"]';
        $filemap = Kohana::config('freeswitch.filemap');
        Telephony::setDriver(Kohana::config('telephony.driver'));
        $xml = Telephony::getDriver()->xml;
        Telephony::load(new Lcr() , $filemap);
        $xml->setXmlRoot('//document/section[@name="configuration"]/configuration[@name="lcr.conf"]/');
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $lcrPost = $this->input->post('lcr');
            $newdsn = $lcrPost['odbcdsn'];
            $xml->update($xpathAttr . "{@value=\"$newdsn\"}");
            Telephony::save(new Lcr() , $filemap);
            url::redirect('/lcr/settings');
        } else {
            $this->view->dsn = $xml->getAttributeValue($xpathAttr);
        }
    }
    /**
     * Method to select all carriers from db for the dropdown menu(s)
     * @return void
     */
    public function getCarriers()
    {
        $carriers = Doctrine::getTable('Provider');
        $allCarriers = $carriers->findAll()->toArray();
        //Kohana::log('debug', print_r($allCarriers, true));
        $carriersOut[''] = __('Select A Provider');
        $carrierCount = count($allCarriers);
        for ($i = 0; $i < $carrierCount; $i++) {
            $carrierId = $allCarriers[$i]['provider_id'];
            $carrierName = $allCarriers[$i]['provider_name'];
            $carriersOut[$carrierId] = $carrierName;
        }
        return $carriersOut;
    }
}

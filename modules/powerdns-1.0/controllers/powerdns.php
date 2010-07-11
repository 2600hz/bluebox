<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * powerdns.php - PowerDNS Controller Class
 *
 * @author K Anderson
 */
class PowerDns_Controller extends Bluebox_Controller {
    public $writable = array('name', 'type', 'content', 'ttl', 'prio');

    protected $baseModel = 'PdnsDomain';

    protected $recordTypes = array(
        'A' => 'A',
        'AAAA' => 'AAAA',
        'NAPTR' => 'NAPTR',
        'SRV' => 'SRV',
        'PTR' => 'PTR',
        'NS' => 'NS',
        'MX' => 'MX',
        'CNAME' => 'CNAME',
        'SOA' => 'SOA',
        'TXT' => 'TXT',
        'LOC' => 'LOC',
        'DNSKEY' => 'DNSKEY',
        'DS' => 'DS',
        'NSEC' => 'NSEC',
        'KEY' => 'KEY',
        'CERT' => 'CERT',
        'RRSIG' => 'RRSIG',
        'RP' => 'RP',
        'SPF' => 'SPF',
        'SSHFP' => 'SSHFP',
        'HINFO' => 'HINFO',
        'AFSDB' => 'AFSDB'
    );

    public function __construct()
    {
        parent::__construct();
        stylesheet::add('powerdns', 50);
    }


    public function index()
    {
        $this->template->content = new View('generic/grid');

        // Setup the base grid object
        $this->grid = jgrid::grid($this->baseModel, array(
            'caption' => 'Domains'
        ))
        // Build a grid with a hidden location_id and add an option for the user to select the display columns
        ->add('id', 'ID', array(
            'hidden' => true,
            'key' => true
        ))->add('name', 'Domain Name/Realm')
        ->add('recordCount', 'Records', array(
            'search' => false,
            'align' => 'center',
            'callback' => array(
                'function' => array($this, 'countRecords'),
                'arguments' => array('id')
            )
        ))->navButtonAdd('Columns', array(
            'onClickButton' => 'function () {  $(\'#{table_id}\').setColumns(); }',
            'buttonimg' => url::base() . 'assets/css/jqGrid/table_insert_column.png',
            'title' => 'Show/Hide Columns',
            'noCaption' => true,
            'position' => 'first'
        ))->addAction('powerdns/edit', 'Edit', array(
            'arguments' => 'id',
            'width' => '120'
        ))->addAction('powerdns/delete', 'Delete', array(
            'arguments' => 'id',
            'width' => '20'
        ));
        // dont foget to let the plugins add to the grid!
        plugins::views($this);

        $this->view->grid = $this->grid->produce();
    }

    public function countRecords($cell, $domain_id) {
        $row = Doctrine::getTable('PdnsRecord')->findByDomainId($domain_id);
        return count($row->toArray());
    }

    public function add()
    {
        // Overload the update view
        $this->view->title = 'Add Domain';

        $this->pdnsDomain = new $this->baseModel();
        
        // Are we supposed to be saving stuff? (received a form post?)
        if ($this->submitted()) {

            if (!empty($_POST['pdnsdomain']['soa']['primary']) && !empty($_POST['pdnsdomain']['soa']['hostmaster'])) {
                $soaRecord = array();

                $soaRecord['name'] = $_POST['pdnsdomain']['name'];
                $soaRecord['type'] = 'SOA';
                $soaRecord['content'] = $_POST['pdnsdomain']['soa']['primary'] .' ' .$_POST['pdnsdomain']['soa']['hostmaster'] .' 0';

                $records = array('PdnsRecord' => array($soaRecord));
                $this->pdnsDomain->synchronizeWithArray($records);


            }

            if ($this->formSave($this->pdnsDomain)) {
                url::redirect(Router_Core::$controller);
            }
        }

        // Allow our location object to be seen by the view
        $this->view->pdnsdomain = $this->pdnsDomain;

        if (!empty($_POST['pdnsdomain']['soa']['primary'])) {
            $this->view->primary = $_POST['pdnsdomain']['soa']['primary'];
        } else {
             $this->view->primary = '';
        }
        if (!empty($_POST['pdnsdomain']['soa']['hostmaster'])) {
            $this->view->hostmaster = $_POST['pdnsdomain']['soa']['hostmaster'];
        } else {
            $this->view->hostmaster = '';
        }


        // Execute plugin hooks here, after we've loaded the core data sets
        plugins::views($this);
    }

    public function edit($id = NULL)
    {
        // Overload the update view
        $this->view->title = 'Edit Domain';

        $this->pdnsDomain = Doctrine::getTable($this->baseModel)->find($id);
        // Was anything retrieved? If no, this may be an invalid request
        if (!$this->pdnsDomain) {
            // Send any errors back to the index
            $error = i18n('Unable to locate domain id %1$d!', $id)->sprintf()->s();
            message::set($error, array('translate' => false, 'redirect' => Router::$controller));
            return true;
        }

        // Are we supposed to be saving stuff? (received a form post?)
        if ($this->submitted()) {
            $records = $this->input->post('pdnsrecord', array());

            foreach ($records as $key => $record) {
                if ($record['type'] == 'SOA') {
                    $records[$key]['name'] = $_POST['pdnsdomain']['name'];
                } else {
                    $records[$key]['name'] .= '.' .$_POST['pdnsdomain']['name'];
                }

                if (empty($record['prio'])) {
                    $records[$key]['prio'] = 0;
                }

                if (empty($record['ttl'])) {
                    $records[$key]['ttl'] = 7200;
                }
            }
            
            // sync the group members with the group
            $this->pdnsDomain->PdnsRecord->synchronizeWithArray($records);

            if ($this->formSave($this->pdnsDomain)) {
                url::redirect(Router_Core::$controller);
            }
        }

        // populate the keys
        $records = array();
        foreach ($this->pdnsDomain->PdnsRecord as $record) {
            $record = $record->toArray();
            $record['name'] = str_replace('.' .$this->pdnsDomain['name'], '', $record['name']);
            $records[$record['id']] = $record;
        }
        $this->view->records = $records;

        // Allow our location object to be seen by the view
        $this->view->pdnsdomain = $this->pdnsDomain;
        $this->view->recordTypes = $this->recordTypes;
        
        // Execute plugin hooks here, after we've loaded the core data sets
        plugins::views($this);
    }

    public function delete($id = NULL)
    {
        $this->stdDelete($id);
    }
}

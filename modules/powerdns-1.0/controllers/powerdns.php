<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * powerdns.php - PowerDNS Controller Class
 *
 * @author K Anderson
 */
class PowerDns_Controller extends Bluebox_Controller
{
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

    public function countRecords($cell, $domain_id)
    {
        $row = Doctrine::getTable('PdnsRecord')->findByDomainId($domain_id);
        
        return count($row->toArray());
    }

    protected function createView($baseModel = NULL, $forceDelete = NULL)
    {
        // Overload the update view
        if (($forceDelete) or (strcasecmp(Router::$method, 'delete') == 0 and $forceDelete !== FALSE))
        {
            $this->template->content = new View('generic/delete');
        }
        else if (strcasecmp(Router::$method, 'create') == 0)
        {
            $this->template->content = new View(Router::$controller . '/add');
        }
        else
        {
            $this->template->content = new View(Router::$controller . '/edit');
        }
        
        if (is_null($baseModel))
        {
            $baseModel = ucfirst($this->baseModel);
        }

        $this->view->title = ucfirst(Router::$method) .' ' .inflector::humanizeModelName($baseModel);

        Event::run('bluebox.create_view', $this->view);
    }

    protected function save_prepare(&$object)
    {
        if (!empty($_POST['pdnsdomain']['soa']['primary']) && !empty($_POST['pdnsdomain']['soa']['hostmaster']))
        {
            $soaRecord = array();

            $soaRecord['name'] = $_POST['pdnsdomain']['name'];

            $soaRecord['type'] = 'SOA';

            $soaRecord['content'] = $_POST['pdnsdomain']['soa']['primary'] .' ' .$_POST['pdnsdomain']['soa']['hostmaster'] .' 0';

            $records = array('PdnsRecord' => array($soaRecord));

            $object->synchronizeWithArray($records);
        }
        else
        {
            $records = $this->input->post('pdnsrecord', array());

            foreach ($records as $key => $record)
            {                
                if ($record['type'] == 'SOA')
                {
                    $records[$key]['name'] = $_POST['pdnsdomain']['name'];
                }
                else
                {
                    if (empty($record['name']))
                    {
                        unset($records[$key]);

                        continue;
                    }

                    $records[$key]['name'] .= '.' .$_POST['pdnsdomain']['name'];
                }

                if (empty($record['prio']))
                {
                    $records[$key]['prio'] = 0;
                }

                if (empty($record['ttl']))
                {
                    $records[$key]['ttl'] = 600;
                }
            }

            // sync the group members with the group
            $object->PdnsRecord->synchronizeWithArray($records);
        }

        parent::save_prepare($object);
    }

    protected function prepareUpdateView($baseModel = NULL)
    {
        if (!empty($_POST['pdnsdomain']['soa']['primary']))
        {
            $this->view->primary = $_POST['pdnsdomain']['soa']['primary'];
        }
        else
        {
             $this->view->primary = '';
        }

        if (!empty($_POST['pdnsdomain']['soa']['hostmaster']))
        {
            $this->view->hostmaster = $_POST['pdnsdomain']['soa']['hostmaster'];
        }
        else
        {
            $this->view->hostmaster = '';
        }

        // populate the keys
        $records = array();

        foreach ($this->pdnsdomain->PdnsRecord as $record)
        {
            $record = $record->toArray();

            $record['name'] = str_replace('.' .$this->pdnsdomain['name'], '', $record['name']);

            $records[$record['id']] = $record;
        }

        $this->view->records = $records;
        
        $this->view->recordTypes = $this->recordTypes;
        
        parent::prepareUpdateView($baseModel);
    }
}
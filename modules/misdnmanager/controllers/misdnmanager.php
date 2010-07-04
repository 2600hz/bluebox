<?php
/**
 * This is the skeleton controller template.
 * Replace this text with a description of what your module or pages do.
 *
 * All public methods here that are not prefixed with _ are accessible via /MyModule/methodname (where "MyModule" is the name of the
 * controller class and "methodname" is the name of the method you define).
 * As an example, if this class is named Voicemail_Controller with a method definition of 'public function send()', you can access it via:
 *      http://myserver/frepbx/voicemail/send
 *
 * You can add your own custom routes by adding the appropriate routing file per Kohana's specifications.
 *
 * Views are automatically rendered for each method unless you override this functionality. The view file rendered, by default, is
 *  /views/controllername/methodname
 * Using the above example, the view for http://myserver/bluebox/voicemail/send would live in
 *  /views/voicemail/send
 *
 * To set variables that will be accessible in the view, use the format:
 *  $this->view->myvariable = $myvariable;
 * You can then reference those variables as $myvariable within the view itself.
 *
 *
 * @author Darren Schreiber
 * @package _Skeleton
 */

/**
 * MisdnPort.php - Represents one card port
 *
 * @author reto.haile <reto.haile@selmoni.ch>
 * @license CPAL-1.0
 * @package Bluebox
 * @subpackage MisdnManager
 *
 */
class MisdnManager_Controller extends Bluebox_Controller {
    /**
     * You can override the system/user's default skin on a per-controller basis if you need to. Note that this is the highest
     * level override you can do - it will always override any user or system defaults.
     * @var string Skin name (as it exists in /skins/)
     */
    protected $skin;

    /**
     * By default, we automatically render the page. You can override this controller-wide, or within individual methods
     * @var boolean true/false as to whether we should be automatically rendering a view after the method finishes
     */
    public $auto_render = true;

    /**
     * By default, all public fields defined in this class are also available as JSON/XML as well. You can hide some fields from
     * being presented to requestors in XML/JSON format if you don't like this behavior. Setting this value to '*' hides all data fields.
     * @var array
     */
    public $hidden_fields = NULL;

    /**
     * An array of methods/actions that are accessible without the user needing to be authenticated
     * @var array
     */
    public $noAuth = NULL;

    /**
     * Keep track of any grid helpers when used, as it relates to the main page. We typically only have one.
     * Note that this variable is exposed to plug-ins, so that they can add additional information to grids.
     * If you use your own variable name, there's no guarantees plug-ins will be able to use it.
     * @var array Array of Grid_Helper objects
     */
    public $grids;

    /**
     * The order columns should appear. This is set in a simple array and is used by the grid to re-order columns.
     * This is useful for allowing individual users to have their own custom column ordering.
     * @var array
     */
    public $columnOrder;

    /**
     * Array of key/value pairs to filter on.
     * @var array
     */
    public $filters;


    protected $writable = array('dsp_debug', 'dsp_poll', 'dsp_dtmfthreshold', 'dsp_options', 'hfcmulti_debug',
                                'hfcmulti_pcm', 'hfcmulti_poll', 'devnode_user', 'devnode_group', 'devnode_mode',
                                'description', 'Ports', 'mode', 'link', 'masterclock');


    protected $baseModel = 'MisdnCard';


    /**
     * Base index page
     */
    public function index()
    {
        $this->grid = jgrid::grid('MisdnCard', array('caption' => 'Cards in this system'))
        ->add('card_id', 'ID', array('hidden' => true, 'key' => true))
        ->add('pci_address', 'PCI Address', array('width' => 60))
        ->add('MisdnCardModel/model', 'Model', array('width' => 100))
        ->add('description', 'Description')
        ->addAction(Router::$controller.'/edit', 'Edit', array('arguments' => 'card_id', 'width' => 60))
        ->addAction(Router::$controller.'/delete', 'Delete', array('arguments' => 'card_id', 'width' => 20));

        // let the plugins add themself to the grid
        plugins::views($this);

        // produce grid markup or JSON
        if ($json = $this->grid->getGridJson())
        {
            echo $json;
            $this->auto_render = false;
        } else {
            $this->view->grid = $this->grid->render();
        }
    }


    public function scan()
    {
        $scanner = pcicardscanner::getInstance();
        $scanner->scan();
        $cards = $scanner->toArray();

        $this->view->cards = $cards;
    }


    public function settings($id = 0)
    {
        $this->misdnsetting = Doctrine::getTable('MisdnSetting')->find($id);

        if(sizeof($this->input->post()) != 0)
        {
            $this->formSave($this->misdnsetting, 'Settings saved');
        }
        $this->view->misdnsetting = $this->misdnsetting;
    }


    public function vendors()
    {
        $this->grid = jgrid::grid('MisdnCardVendor', array('caption' => 'Supported Vendors'))
        ->add('card_vendor_id', 'ID', array('hidden' => true, 'key' => true))
        ->add('vendor', 'Vendor');

        // Produces the grid markup or JSON, respectively
        if ($json = $this->grid->getGridJson())
        {
            echo $json;
            $this->auto_render = false;
        } else {
            $this->view->grid = $this->grid->render();
        }
    }


    public function models()
    {
        $this->grid = jgrid::grid('MisdnCardModel', array('caption' => 'Supported Card Models', 'rowNum' => 25))
        ->add('card_model_id', 'ID', array('hidden' => true, 'key' => true))
        ->add('MisdnCardVendor/vendor', 'Vendor', array('width' => 80))
        ->add('model', 'Model')
        ->add('pci_subsys_id', 'PCI Subsystem ID', array('width' => 35, 'align' => 'center'));

        // Produces the grid markup or JSON, respectively
        if ($json = $this->grid->getGridJson())
        {
            echo $json;
            $this->auto_render = false;
        } else {
            $this->view->grid = $this->grid->render();
        }
    }


    /**
     * Add page
     */
    public function add($pciSubId, $pciAddr)
    {
        $this->view->title = 'Add new Card';

        $model = MisdnManager::getModel($pciSubId);

        $this->card = Doctrine::getTable('MisdnCard')->findOneByPciAddress($pciAddr);

        if(is_a($this->card, 'MisdnCard'))
        {
            message::set('Card at this PCI address has already been added to the system!',
            array('redirect' => Router::$controller.'/edit/'.$this->card->card_id));
            return true;
        }

        $this->card = new MisdnCard();
        $this->card->card_model_id = $model->card_model_id;
        $this->card->pci_address = $pciAddr;

        for($i=1; $i < $model->portcount; $i++)
        {
            $port = new MisdnPort();
            $port->number = $i;
        }

        if (sizeof($this->input->post()) != 0)
        {
            if(count($this->card->Ports) == 0)
            {
                for ($i=1; $i <= $this->card->MisdnCardModel->portcount; $i++)
                {
                    $p = new MisdnPort();
                    $p->number = $i;
                    $p->description = 'Port ' . $i;
                    $this->card->Ports[] = $p;
                }
            }

            if($this->formSave($this->card, 'Settings saved'))
            {
                //                echo "CARD ID NACH SAVE: " . $this->card->card_id;
                //                die;
                url::redirect(Router::$controller.'/edit/' . $this->card->card_id);
            }
        }

        $this->view->card = $this->card;
    }


    public function jsonmodels()
    {
        echo json_encode(MisdnManager::getModels($_REQUEST['id']));
        die();
    }

    /**
     * Edit page
     * @param integer $widgetId
     */
    public function edit($cardID)
    {
        $this->card = Doctrine::getTable('MisdnCard')->findOneByCardId($cardID);

        $this->grid = jgrid::grid('MisdnPort', array(
        'caption' => 'Ports for this card', 
        'width' => '600',
        'height' => '180'))
        ->add('port_id', 'ID', array('hidden' => true, 'key' => true))
        ->add('number', 'Port', array('width' => 20))
        ->add('description', 'Description')
        ->add('mode', 'Mode', array('width' => 20))
        ->add('link', 'Link', array('width' => 20))
        ->addAction(Router::$controller.'/ported', 'Edit', array('arguments' => 'port_id', 'width' => 40));

        // let the plugins add themself to the grid
        plugins::views($this);

        // produce grid markup or JSON
        if ($json = $this->grid->getGridJson())
        {
            echo $json;
            $this->auto_render = false;
        } else {
            $this->view->grid = $this->grid->render();
        }

         
        if (sizeof($this->input->post()) > 0)
        {
            $settings = $this->input->post('settings');

            foreach ($settings as $option => $value)
            {
                $setting = new MisdnCardSetting();
                $setting->card_id = $this->card->card_id;
                $setting->option = $option;
                $setting->value = strlen($value) > 0 ? "$value" : NULL;

                try
                {
                    $this->card->Settings[] = $setting;
                }
                catch (Exception $e)
                {
                    message::set("Unable to save card setting!", array('redirect' => Router::$controller.'/index'));
                    return false;
                }
            }

            if (!$this->formSave($this->card, 'Settings saved'))
            {
                message::set("Unable to save card!", array('redirect' => Router::$controller.'/index'));
                return false;
            }

        }

        $settings = array();
        if (count($this->card->Settings) > 0)
        {
            foreach ($this->card->Settings as $setting)
            {
                $settings[$setting->option] = $setting->value;
            }
        }

        $this->view->settings = $settings;
        $this->view->title = "Edit Card";
        $this->view->misdncard = $this->card;

        //        echo print_r($this->card->Settings);
        //        die();

    }


    public function ported($portID = NULL)
    {
        $this->port = Doctrine::getTable('MisdnPort')->findOneByPortId($portID);

        if (empty($this->port))
        {
            message::set('No such port!',
            array('type' => 'error', 'redirect' => Router::$controller .'/index'));
        }

        if (sizeof($this->input->post()) > 0)
        {
            if(!$this->formSave($this->port, 'Settings saved!'))
            {
                message::set("Unable to save port!", array('redirect' => Router::$controller.'/index'));
                return false;
            }
            else
            {
                url::redirect(Router::$controller . '/edit/' . $this->port->card_id);
            }
        }

        $this->view->title = "Edit Port " . $this->port->number;
        if (!empty($this->port->Card->description)) $this->view->title .= " on Card '" . $this->port->Card->description . "'";

        $this->view->misdnport = $this->port;
        //        $this->view->settings = $this->settings;
    }


    /**
     * Save page
     */
    public function save()
    {
        $this->view->title = 'Save Settings to Disk';
        if($this->input->post('confirm') == 'Yes')
        {
            if (MisdnManager::saveConfig())
            {
                message::set('The configuration has been successfully saved to disk.',
                array('type' => 'success', 'redirect' => Router::$controller .'/index'));
            }
            else
            {
                message::set("Couldn't write configuration to disk!",
                array('type' => 'error', 'redirect' => Router::$controller .'/index'));
            }
        }

    }

    /**
     * TODO: I need to write this example.
     */
    function delete($cardID = NULL)
    {
        $this->card = Doctrine::getTable('MisdnCard')->findOneByCardId($cardID);
        if (!is_a($this->card, 'MisdnCard'))
        {
            message::set('Unable to process delete request, no card ID supplied or invalid card ID!',
            array('redirect' => Router::$controller .'/index'));
            return true;
        }

        if (sizeof($this->input->post()) > 0)
        {
            if($this->input->post('confirm') == 'Yes')
            {
                if($this->card->delete())
                {
                    message::set('Card successfully removed!',
                    array('redirect' => Router::$controller .'/index'));
                }
            }
        }

        $this->view->title = 'Delete Card';
        $this->view->card = $this->card;
    }

}


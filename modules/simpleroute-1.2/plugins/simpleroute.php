<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * @package    Simpleroute
 * @author     K Anderson <bitbashing@gmail.com>
 * @license    Mozilla Public License (MPL)
 */
class SimpleRoute_Plugin extends Bluebox_Plugin
{
    protected $name = 'simpleroute';

    protected $preloadModels = array('SimpleRoute','Device');

    public function buildAccountRelationships()
    {
        return TRUE;
    }
    public function device_update()
    {
	$this->views[]=new View("simpleroute/device_update");
        return TRUE;
    }

    protected function viewSetup()
    {
        $this->subview = new View('simpleroute/apply');

        $this->subview->tab = 'main';

        $this->subview->section = 'general';
        return TRUE;
    }

    protected function loadFormData()
    {
        parent::loadFormData();

	if (array_key_exists('contexts',$this->pluginData)) {
		unset($this->pluginData['contexts']);
	}

        if (!empty($_POST['simpleroute']))
        {
            $this->pluginData = $_POST['simpleroute'];
        }

        return TRUE;
    }

    protected function addSubView()
    {
	if (array_key_exists ("simpleroute",$this->base->plugins)) {
		$this->subview->routes = $this->base->plugins['simpleroute'];
	} else {
		$this->subview->routes = array();
	}

        $this->subview->outboundPatterns = Doctrine::getTable('SimpleRoute')->findAll(Doctrine::HYDRATE_ARRAY);

	$this->subview->trunks = array();
	$this->subview->trunkoptions = "";
	foreach (Doctrine::getTable('Trunk')->findAll(Doctrine::HYDRATE_ARRAY) AS $trunk) {
		$this->subview->trunks[$trunk['trunk_id']]=$trunk;
		$this->subview->trunkoptions.="<option value='".$trunk["trunk_id"]."'>".$trunk["name"]."</option>";
	}
	
	$this->subview->destinations = array();
	$this->subview->destinationoptions="";
	foreach (Doctrine::getTable('SimpleRoute')->findAll(Doctrine::HYDRATE_ARRAY) AS $dest) {
		$this->subview->destinations[$dest['simple_route_id']]=$dest;
		$this->subview->destinationoptions.="<option value='".$dest["simple_route_id"]."'>".$dest["name"]."</option>";
	}
        parent::addSubView();
    }

    protected function save_succeeded(&$object)
    {
        parent::save_succeeded($object);
        
        // One of those nasty but functionaly things...
        $trunks = Doctrine::getTable('Trunk')->findAll();

        foreach ($trunks as $trunk)
        {
            kohana::log('debug', 'Rebuilding trunk ' .$trunk['trunk_id'] .' to apply the changes to context ' .$object['context_id']);

            $trunk->markModified('name');

            $trunk->save();
        }
    }
    protected function delete_succeeded(&$object)
    {
        parent::delete_succeeded($object);
        
        $identifier = $object->identifier();

        // One of those nasty but functionaly things...
        $trunks = Doctrine::getTable('Trunk')->findAll();

        foreach ($trunks as $trunk)
        {
/*
            if (!isset($trunk['plugins']['simpleroute']['patterns'][$identifier['simple_route_id']]))
            {
                $patterns = $trunk['plugins']['simpleroute']['patterns'];

                unset($patterns[$identifier['simple_route_id']]);

                $trunk['plugins']['simpleroute']['patterns'] = $patterns;
            }
*/
            kohana::log('debug', 'Rebuilding trunk ' .$trunk['trunk_id'] .' to remove simple route ' .$identifier['simple_route_id']);

            $trunk->save();
        }
    }

}

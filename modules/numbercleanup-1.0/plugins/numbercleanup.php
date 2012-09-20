<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * @package    Simpleroute
 * @author     K Anderson <bitbashing@gmail.com>
 * @license    Mozilla Public License (MPL)
 */
class NumberCleanup_Plugin extends Bluebox_Plugin
{
    protected $name = 'numbercleanup';

    protected function viewSetup()
    {
        $this->subview = new View('numbercleanup/update');

        $this->subview->tab = 'main';
        $this->subview->atitle = 'main';

        $this->subview->section = 'general';

        return TRUE;
    }

    protected function loadFormData()
    {
        parent::loadFormData();

        if (!empty($_POST['numberclean']))
        {
            $this->pluginData = array_values($_POST['numberclean']);
        }
        return TRUE;
    }

    protected function addSubView()
    {
	if (array_key_exists ("numbercleanup",$this->base->plugins)) {
		$this->subview->numberclean = json_encode($this->base->plugins['numbercleanup']);
	} else {
		$this->subview->numberclean = json_encode(array());
	}

        parent::addSubView();
    }

    protected function XXXsave_succeeded(&$object)
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
    protected function XXXdelete_succeeded(&$object)
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

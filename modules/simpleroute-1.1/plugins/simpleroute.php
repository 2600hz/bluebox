<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * @package    Simpleroute
 * @author     K Anderson <bitbashing@gmail.com>
 * @license    Mozilla Public License (MPL)
 */
class SimpleRoute_Plugin extends Bluebox_Plugin
{
    protected $name = 'simpleroute';

    protected $preloadModels = array('SimpleRoute');

    public function buildAccountRelationships()
    {
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

        $this->pluginData['contexts'] = array();

        if (!empty($_POST['simpleroute']['contexts']))
        {
            $this->pluginData['contexts'] = $_POST['simpleroute']['contexts'];
        }

        return TRUE;
    }

    protected function addSubView()
    {
        $this->subview->outboundPatterns = Doctrine::getTable('SimpleRoute')->findAll(Doctrine::HYDRATE_ARRAY);

        $this->subview->contexts = Doctrine::getTable('Context')->findAll(Doctrine::HYDRATE_ARRAY);

        parent::addSubView();
    }

    protected function validate($data, $validator)
    {
        if (isset($data['caller_id_number']))
        {
            if (preg_match('/[^0-9]/', $data['caller_id_number']))
            {
                $validator->add_error('simpleroute[caller_id_number]', 'Please provide only numbers');
            }
        }

        if (isset($data['area_code']))
        {
            if (preg_match('/[^0-9]/', $data['area_code']))
            {
                $validator->add_error('simpleroute[area_code]', 'Please provide only numbers');
            }
        }
    }
}
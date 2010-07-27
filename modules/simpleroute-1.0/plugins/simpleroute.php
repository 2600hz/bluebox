<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * @package    Simpleroute
 * @author     K Anderson <bitbashing@gmail.com>
 * @license    Mozilla Public License (MPL)
 */
class SimpleRoute_Plugin extends Bluebox_Plugin
{
    protected $name = 'simpleroute';

    protected function loadFormData()
    {
        parent::loadFormData();

        $this->pluginData['contexts'] = array();

        if (!empty($_POST['simpleroute']['contexts']))
        {
            $this->pluginData['contexts'] = $_POST['simpleroute']['contexts'];
        }

        kohana::log('debug', print_r($this->pluginData, true));

        return TRUE;
    }

    protected function addSubView()
    {
        $this->subview->outboundPatterns = kohana::config('simpleroute.outbound_patterns');

        $this->subview->contexts = Doctrine::getTable('Context')->findAll(Doctrine::HYDRATE_ARRAY);

        parent::addSubView();
    }
}
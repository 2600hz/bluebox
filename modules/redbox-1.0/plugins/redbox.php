<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * @package    Redbox
 * @author     Jon Blanton <jon@2600hz.com>
 * @license    MPL
 */
class Redbox_Plugin extends Bluebox_Plugin
{
    protected $name = 'redbox';

    protected $preloadModels = array('Redbox');

    public function buildAccountRelationships()
    {
        return TRUE;
    }

    protected function viewSetup()
    {
        $this->subview = new View('redbox/apply');

        $this->subview->tab = 'main';

        $this->subview->section = 'general';

        return TRUE;
    }

    protected function addSubView()
    {
        $this->subview->contexts = Doctrine::getTable('Context')->findAll(Doctrine::HYDRATE_ARRAY);

        parent::addSubView();
    }
}
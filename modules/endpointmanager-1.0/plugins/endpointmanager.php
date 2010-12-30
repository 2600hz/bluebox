<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * @author Your Name <your@email.org>
 * @license Your License
 * @package _Skeleton
 */
class EndpointManager_Plugin extends Bluebox_Plugin
{
    protected $name = 'endpointmanager';

    protected function viewSetup()
    {
        $this->subview = new View('endpointmanager/plugin');
        $this->subview->tab = 'main';
        $this->subview->section = 'general';

        /*$this->subview->mac_addresses = Doctrine_Query::create()
            ->select('e.endpoint_id, e.mac, e.')
            ->from('Endpoint e')
            ->execute(array(), Doctrine::HYDRATE_SCALAR);*/

        $this->subview->mac_addresses = Doctrine::getTable('Endpoint')->findAll(Doctrine::HYDRATE_SCALAR);

        return TRUE;
    }
}
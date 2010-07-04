<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * Sofia_Plugin.php - puts the phones registration status into device manager grid
 * @author K Anderson
 * @license LGPL
 * @package Bluebox
 */

class Sofia_Plugin extends Bluebox_Plugin
{
    public function index()
    {
        $this->grid->add('Sofia/status', 'Status', array(
           'align' => 'center',
           'search' => false,
           'sortable' => false,
            'callback' => array(
                'function' => array($this, 'status'),
                'arguments' => array('Sip/username', 'User/last_name')
            )                
        ));
    }
    
    public function status($cell, $username = NULL)
    {
        $row = Doctrine::getTable('SipRegistrations')->findOneBySipUser($username);
        if (!empty($row['status'])) {
            return $row['status'];
        } else {
            return 'Offline';
        }
    }
}
<?php defined('SYSPATH') or die('No direct access allowed.');

class QuickAdd_Controller extends Bluebox_Controller
{
    protected $baseModel = 'User';

    public function index()
    {
        unset($_POST['user'], $_POST['number'], $_POST['callerid']);

        url::redirect('quickadd/create');
    }

    protected function save_prepare(&$object)
    {
        $object['user_type'] = User::TYPE_NORMAL_USER;

        $object['location_id'] = $_POST['number']['location_id'];

        Doctrine_Manager::connection()->beginTransaction();

        parent::save_prepare($object);
    }

    protected function post_save(&$object)
    {
        $extension = $_POST['number']['number'];
        
        $context_id = $_POST['number']['context_id'];

        $location_id = $_POST['number']['location_id'];

        $external_cid = $_POST['callerid']['external_number'];

        $success = Bluebox_Tenant::createUserExtension($object['user_id'], $extension, $context_id, $location_id, array(
                'callerid_external_number' => $extension,
                'callerid_external_number' => $external_cid,
                'sip_password' => $_POST['user']['create_password']
            )
        );

        if (!$success)
        {
           Doctrine_Manager::connection()->rollback();           

           throw new Bluebox_Validation_Exception('Could not quick create!');
        }
        else
        {
            Doctrine_Manager::connection()->commit();
        }

        parent::post_save($object);
    }
}

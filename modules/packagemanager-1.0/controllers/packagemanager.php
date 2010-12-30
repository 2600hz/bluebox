<?php defined('SYSPATH') or die('No direct access allowed.');

class PackageManager_Controller extends Bluebox_Controller
{
    protected $baseModel = 'Package';

    protected $displayParameters = array(
        'version',
        'author',
        'vendor',
        'license',
        'summary',
        'description',
        'sourceURL'
    );
  
    public function index()
    {
        stylesheet::add('packagemanager');

        $messages = array('error' => NULL, 'warning' => NULL, 'notice' => NULL, 'ok' => NULL);

        $this->view->displayParameters = $this->displayParameters;

        $this->view->messages = $messages;

        $this->view->catalog = Package_Manager::getDisplayList();

        $messages = Package_Message::get();

        if (is_array($messages))
        {
            $this->view->messages = $messages;
        }
        else
        {
            $this->view->messages = array();
        }

        foreach (Package_Catalog::getCatalog() as $identifier => $package)
        {
            kohana::log('debug', $identifier .' => ' .$package['packageName'] .' version ' .$package['version']);
        }

        Package_Message::clear();
    }

    public function verify($identifier)
    {
        try
        {
            $transaction = Package_Transaction::beginTransaction();

            $transaction->verify($identifier);

            $transaction->commit();

            message::set('Verify operation completed', 'success');
        } 
        catch(Exception $e)
        {
            message::set($e->getMessage());
        }

        url::redirect(Router::$controller);
    }

    public function repair($identifier)
    {
        try
        {
            $transaction = Package_Transaction::beginTransaction();

            $transaction->repair($identifier);

            $transaction->commit();

            message::set('Repair operation completed', 'success');
        }
        catch(Exception $e)
        {
            message::set($e->getMessage());
        }

        url::redirect(Router::$controller);
    }

    public function install($identifier)
    {
        try
        {
            $transaction = Package_Transaction::beginTransaction();

            $transaction->install($identifier);

            $transaction->commit();

            message::set('Install operation completed', 'success');
        }
        catch(Exception $e)
        {
            message::set($e->getMessage());
        }

        url::redirect(Router::$controller);
    }

    public function uninstall($identifier)
    {
        try
        {
            $transaction = Package_Transaction::beginTransaction();

            $transaction->uninstall($identifier);

            $transaction->commit();

            message::set('Uninstall operation completed', 'success');
        }
        catch(Exception $e)
        {
            message::set($e->getMessage());
        }

        url::redirect(Router::$controller);
    }

    public function migrate($identifier = NULL)
    {
        if (!empty($_POST['migrate'][$identifier]))
        {
            $identifier = $_POST['migrate'][$identifier];
        }

        try
        {
            $transaction = Package_Transaction::beginTransaction();

            $transaction->migrate($identifier);

            $transaction->commit();

            message::set('Migrate operation completed', 'success');
        }
        catch(Exception $e)
        {
            message::set($e->getMessage());
        }

        url::redirect(Router::$controller);
    }

    public function repair_all()
    {
        $this->template->content = new View('generic/blank');

        $catalog = Package_Manager::getDisplayList();

        if (empty($catalog['Installed']))
        {
            return;
        }

        try
        {
            $transaction = Package_Transaction::beginTransaction();

            foreach($catalog['Installed'] as $package)
            {
                $transaction->repair($package['identifier']);
            }

            $transaction->commit();

            message::set('Repair all operation completed', 'success');
        }
        catch(Exception $e)
        {
            message::set($e->getMessage());
        }

        url::redirect(Router::$controller);
    }

    public function createRepo()
    {
        Package_Catalog_Remote::createRepo();
    }
}
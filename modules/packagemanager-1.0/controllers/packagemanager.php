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
    }

    public function verify($identifier)
    {
        try
        {
            Package_Operation::dispatch('verify', $identifier);

            $name = Package_Catalog::getPackageDisplayName($identifier);

            message::set('Verify of package ' .$name .' succeeded', 'success');
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
            Package_Operation::dispatch('repair', $identifier);

            $name = Package_Catalog::getPackageDisplayName($identifier);

            message::set('Repair of package ' .$name .' succeeded', 'success');
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
            Package_Operation::dispatch('install', $identifier);
            
            $name = Package_Catalog::getPackageDisplayName($identifier);

            message::set('Install of package ' .$name .' succeeded', 'success');
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
            Package_Operation::dispatch('uninstall', $identifier);

            $name = Package_Catalog::getPackageDisplayName($identifier);

            message::set('Uninstall of package ' .$name .' succeeded', 'success');
        }
        catch(Exception $e)
        {
            message::set($e->getMessage());
        }

        url::redirect(Router::$controller);
    }

    public function migrate($identifier)
    {
        try
        {
            Package_Operation::dispatch('migrate', $identifier);

            $name = Package_Catalog::getPackageDisplayName($identifier);

            message::set('Upgrade of package ' .$name .' succeeded', 'success');
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
    }
}
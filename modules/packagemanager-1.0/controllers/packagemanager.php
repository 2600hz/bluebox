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
        'description'
    );

    public function __construct()
    {
        parent::__construct();

        if (users::$user['user_type'] <= User::TYPE_ACCOUNT_ADMIN)
        {
            message::set('You are not authorized to manage packages!');

            $this->returnQtipAjaxForm(NULL);

            url::redirect('/');
        }
    }

    public function index()
    {
        stylesheet::add('packagemanager');

        $packageManager = Bluebox_PackageManager::instance();

        $messages = array('error' => NULL, 'warning' => NULL, 'notice' => NULL, 'ok' => NULL);

        if (!empty($_POST['operations'])) {

            foreach ($_POST['operations'] as $packageName => $operation) {

                switch($operation) {

                    case Bluebox_PackageManager::OPERATION_INSTALL:

                        $messages = $packageManager->install($packageName);

                        break;

                    case Bluebox_PackageManager::OPERATION_ENABLE:

                        $messages = $packageManager->enable($packageName);

                        break;

                    case Bluebox_PackageManager::OPERATION_DISABLE:

                        $messages = $packageManager->disable($packageName);

                        break;

                    case Bluebox_PackageManager::OPERATION_UNINSTALL:

                        $messages = $packageManager->uninstall($packageName);

                        break;

                    case Bluebox_PackageManager::OPERATION_REPAIR:

                        $messages = $packageManager->repair($packageName);

                        break;

                }

            }

            try {

                $messages = $packageManager->commit();

                unset($_POST['operations']);

            } catch(PackageManager_Dependency_Exception $e) {

                $this->view->error = 'Operation failed: ' .$e->getMessage();

            }

        }

        $this->view->displayParameters = $this->displayParameters;

        $this->view->messages = $messages;

        $this->view->catalog = $packageManager->getCatalog();

    }

    public function verify($packageName)
    {
        $this->template->content = new View('generic/blank');

        $packageManager = Bluebox_PackageManager::instance();

        $messages = array();

        try {

            $messages = $packageManager->verify($packageName);

        } catch(PackageManager_Catalog_Exception $e) {

            message::set($e->getMessage());

        }

        $this->clearMessages();

        foreach($messages as $type => $messageList) {

            if (empty($messageList[$packageName])) continue;
            
            $this->addMessage($packageName, $type, $messageList[$packageName]);

        }
        
        message::render(array(), array('growl' => TRUE, 'html' => FALSE));

    }

    public function repair_all()
    {
        $this->template->content = new View('generic/blank');
        
        $packageManager = Bluebox_PackageManager::instance();

        $catalog = $packageManager->getCatalog();

        foreach ($catalog as $packageName => $package) {

            if ($package['packageStatus'] != Bluebox_PackageManager::STATUS_INSTALLED) {

                continue;

            }

            $packageManager->repair($packageName);

        }

        $messages = array();

        try {

            $messages = $packageManager->commit();

        } catch (PackageManager_Dependency_Exception $e) {

            message::set($e->getMessage());

        }

        $this->clearMessages();

        foreach($messages as $type => $messageList) {

            if (!is_array($messageList)) continue;
            
            foreach ($messageList as $packageName => $message) {

                $this->addMessage($packageName, $type, $message);

            }

        }

        message::render(array(), array('growl' => TRUE, 'html' => FALSE));

    }

    protected function clearMessages()
    {

        jquery::addQuery('.error_message')->hide();

        jquery::addQuery('.warning_message')->hide();

        jquery::addQuery('.notice_message')->hide();

        jquery::addQuery('.ok_message')->hide();

    }

    protected function addMessage($packageName, $type, $messages)
    {
        if (!is_array($messages)) return;

        $html  = $type == 'ok' ? 'Complete' : ucfirst($type);

        $html .= '<ul class="' .$type .'_list packagemanager index module">';

        foreach ($messages as $message) {

            $html .= '<li>' .$message .'</li>';

        }

        $html .= '</ul>';
        
        jquery::addQuery('#' .$packageName .'_' .$type)->html($html)->show();
        
    }
}
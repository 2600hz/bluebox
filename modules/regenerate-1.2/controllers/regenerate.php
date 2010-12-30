<?php defined('SYSPATH') or die('No direct access allowed.');

class Regenerate_Controller extends Bluebox_Controller
{
    public function account($account_id = NULL)
    {
        if ($account_id && users::masqueradeAccount($account_id))
        {
            $masquerade_account = TRUE;
        }
        
        $loadedModels = Doctrine::getLoadedModels();

        $driver = Telephony::getDriver();

        $driverName = get_class($driver);

        if (!$driver)
        {
            message::set('Can not rebuild configuration, no telephony driver active');
        }
        else
        {
            try
            {
                foreach( $loadedModels as $model )
                {
                    $modelDriverName = $driverName .'_' .$model .'_Driver';

                    if ( ! class_exists($modelDriverName, TRUE))
                    {
                        continue;
                    }

                    $outputRows = Doctrine::getTable($model)->findAll();

                    foreach( $outputRows as $outputRow )
                    {
                        Telephony::set($outputRow, $outputRow->identifier());
                    }
                }

                Telephony::save();
                Telephony::commit();

                $account = Doctrine::getTable('Account')->find(users::getAttr('account_id'));

                parent::save_succeeded($account);

                message::set(users::getAttr('Account', 'name') .' configuration rebuild complete!', 'success');
            }
            catch (Exception $e)
            {
                message::set($e->getMessage());
            }
        }

        if (!empty($masquerade_account))
        {
            users::restoreAccount();
        }

        $this->returnQtipAjaxForm();

        url::redirect(Router_Core::$controller);
    }

    public function context($context_id)
    {
        $this->loadBaseModel($context_id, 'Context');

        foreach($this->context['NumberContext'] as $key => &$numberContext)
        {
            $number = &$numberContext['Number'];

            $number->markModified('number');
        }

        try
        {
            $this->context->save();

            message::set('Context "' .$this->context['name'] .'" rebuild complete!', 'success');
        }
        catch (Exception $e)
        {
            message::set($e->getMessage());
        }

        $this->returnQtipAjaxForm();

        url::redirect(Router_Core::$controller);
    }

    public function number($number_id)
    {
        $this->loadBaseModel($number_id, 'Number');

        $this->number->markModified('number');

        try
        {
            $this->number->save();

            message::set('Number "' .$this->number['number'] .'" rebuild complete!', 'success');

            parent::save_succeeded($this->number);
        }
        catch (Exception $e)
        {
            message::set($e->getMessage());
        }

        $this->returnQtipAjaxForm();

        url::redirect(Router_Core::$controller);
    }
}
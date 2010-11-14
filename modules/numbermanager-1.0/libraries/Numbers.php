<?php defined('SYSPATH') or die('No direct access allowed.');

class Numbers
{
    public static function dynamicNumberPlugin()
    {
        $view = Event::$data;

        if (!($view instanceof View))
        {
            return TRUE;
        }

        if (!isset($view->base))
        {
            return TRUE;
        }

        $base = $view->base;

        if (!isset($view->$base))
        {
            return TRUE;
        }

        $checkFor = get_class($view->$base) .'Number';

        if (!class_exists($checkFor))
        {
            return TRUE;
        }

        kohana::log('debug', 'Base model `' .$base .'` found reference to number, registering NumberManager_Plugin::numberInventory()');

        plugins::register(Router::$controller .'/' .Router::$method, 'view', array('NumberManager_Plugin', 'numberInventory'));
    }

   public static function updateOnPageObjects()
    {
        $update = Event::$data;

        if (empty($update['numbermanager']))
        {
            return;
        }

        $update = $update['numbermanager'];

        $objectTemplate = new View('numbermanager/targetObject.mus', $update);

        $objectTemplate->mustache_escape_apostrophes = TRUE;

        javascript::codeBlock("$('.number_target_objects.${update['short_name']}').append('$objectTemplate');");

        javascript::codeBlock("selectDestination('${update['object_number_type']}', ${update['object_id']});");
    }

    public static function disassociateNumbers()
    {
        $base = Event::$data;

        if (!isset($_POST['numbers']))
        {
            return TRUE;
        }

        $numbers = $base['Number']->toArray();

        foreach ($numbers as $key => $number)
        {
            if (empty($number['number_id']))
            {
                continue;
            }
            
            if (empty($_POST['numbers']['assigned'][$number['number_id']]))
            {
                Kohana::log('debug', 'Disassociate number ' .$number['number'] .' (' .$number['number_id'] .') from ' .str_replace('Number', '', $base['Number'][$key]['class_type']) .' ' .$base['Number'][$key]['foreign_id']);

                $base['Number'][$key]['foreign_id'] = 0;

                $base['Number'][$key]['class_type'] = NULL;
            }
            else
            {
                if (isset($_POST['number' .$number['number_id']]['registry']))
                {
                    //Kohana::log('debug', 'Updating registry for number ' .$number['number'] .' (' .$number['number_id'] .')');

                    $base['Number'][$key]['registry'] = arr::merge(
                            $number['registry'],
                            $_POST['number' .$number['number_id']]['registry']
                    );
                }

                if (isset($_POST['number' .$number['number_id']]['dialplan']))
                {
                    //Kohana::log('debug', 'Updating dialplan for number ' .$number['number'] .' (' .$number['number_id'] .')');

                    $base['Number'][$key]['dialplan'] = arr::merge(
                            $number['dialplan'],
                            $_POST['number' .$number['number_id']]['dialplan']
                    );
                }

                unset($_POST['numbers']['assigned'][$number['number_id']]);
            }
        }
    }

    public static function associateNumbers(&$base = NULL)
    {
        if (!$base)
        {
            $base = Event::$data;
        }
        
        if (empty($_POST['numbers']['assigned']))
        {
            return TRUE;
        }
        
        if (get_parent_class($base) == 'Bluebox_Record')
        {
            $class_type = get_class($base) .'Number';
        } 
        else
        {
            $class_type = get_parent_class($base) .'Number';
        }

        $identifiers = $base->identifier();

        if (!empty($identifiers))
        {
            $foreign_id = reset($identifiers);
        } 
        else
        {
            $foreign_id = 0;
        }

        foreach($_POST['numbers']['assigned'] as $number_id => $details)
        {
            $newNumber = Doctrine::getTable('Number')->find($number_id);

            Kohana::log('debug', 'Associate number ' .$newNumber['number'] .' (' .$number_id .') with ' .str_replace('Number', '', $class_type) .' ' .$foreign_id);

            $newNumber['class_type'] = $class_type;

            $newNumber['foreign_id'] = $foreign_id;

            if (isset($_POST['number' .$number_id]['registry']))
            {
                //Kohana::log('debug', 'Updating registry for number ' .$newNumber['number'] .' (' .$number_id .')');

                $newNumber['registry'] = arr::merge(
                        $newNumber['registry'],
                        $_POST['number' .$number_id]['registry']
                );
            }

            if (isset($_POST['number' .$number_id]['dialplan']))
            {
                //Kohana::log('debug', 'Updating dialplan for number ' .$newNumber['number'] .' (' .$number_id .')');

                $newNumber['dialplan'] = arr::merge(
                        $newNumber['dialplan'],
                        $_POST['number' .$number_id]['dialplan']
                );
            }

            $newNumber->save();
        }

        return TRUE;
    }

    public static function customValidation()
    {
        $validation = Bluebox_Controller::$validation;

        $base = Event::$data;

        if (!($base) or !($base instanceof Number))
        {
            return TRUE;
        }

        $base['number'] = trim($base['number']);

        foreach ($base['NumberPool'] as $key => $pool)
        {
            if (empty($pool['number_type_id']))
            {
                unset($base['NumberPool'][$key]);
            }
        }

        foreach ($base['NumberContext'] as $key => $context)
        {
            if (empty($context['context_id']))
            {
                unset($base->NumberContext[$key]);
            }
        }

        $numberContexts = $base->NumberContext->toArray();

        $numberPools = $base->NumberPool->toArray();

        $modified = $base->getModified();

        $matches = array();

        if (empty($numberContexts))
        {
            throw new Exception('Please assign this number to at least one context');
        }

        if (!empty($base['class_type']))
        {
            $poolTypes = array_flip(numbering::getPoolTypes());

            if (empty($poolTypes[$base['class_type']]))
            {
                kohana::log('error', $base['class_type'] .' submitted but there is no such pool type!');

                throw new Exception('Number does not belong to a pool for the new destination');
            }

            foreach ($numberPools as $pool)
            {
                if ($pool['number_type_id'] == $poolTypes[$base['class_type']])
                {
                    $found = TRUE;

                    break;
                }
            }

            if (empty($found))
            {
                $numberPools[] = array('number_type_id' => $poolTypes[$base['class_type']]);

                $base['NumberPool']->synchronizeWithArray($numberPools);
            }
        }

       if (empty($numberPools))
       {
            throw new Exception('Please assign this number to at least one number pool');
       }
    }

    public static function initializeDeviceNumber()
    {
        extract(Event::$data);

        Doctrine::getTable('Number')->getRecordListener()->get('MultiTenant')->setOption('disabled', TRUE);

        try
        {
            $locations = Doctrine_Query::create()
                ->from('Location')
                ->where('account_id = ?', array($account_id))
                ->execute();

            if (empty($locations[0]['location_id']))
            {
                kohana::log('error', 'Unable to initialize device number: could not determine location_id');
                
                return;
            }

            $location_id = $locations[0]['location_id'];

            $number = new Number();

            $number['user_id'] = $user_id;

            $number['number'] = $extension;

            $number['location_id'] = $location_id;

            $number['registry'] = array(
                'ignoreFWD' => '0',
                'ringtype' => 'ringing',
                'timeout' => 30
            );

            $dialplan = array(
                'terminate' => array(
                    'transfer' => 0,
                    'voicemail' => 0,
                    'action' => 'hangup'
                )
            );

            if (!empty($device['plugins']['voicemail']['mwi_box']))
            {
                $dialplan['terminate']['voicemail'] =
                    $device['plugins']['voicemail']['mwi_box'];

                $dialplan['terminate']['action'] = 'voicemail';
            }

            $number['dialplan'] = $dialplan;

            $number['class_type'] = 'DeviceNumber';

            $number['foreign_id'] = $device['device_id'];

            $number['NumberContext']->fromArray(array(
                0 => array('context_id' => $context_id)
            ));

            $numberType = Doctrine::getTable('NumberType')->findOneByClass('DeviceNumber');

            if (empty($numberType['number_type_id']))
            {
                return;
            }

            $number['NumberPool']->fromArray(array(
                0 => array('number_type_id' => $numberType['number_type_id'])
            ));

            $number['account_id'] = $account_id;

            $number->save();
        }
        catch (Exception $e)
        {
            kohana::log('error', 'Unable to initialize device number: ' .$e->getMessage());
        }

        Doctrine::getTable('Number')->getRecordListener()->get('MultiTenant')->setOption('disabled', FALSE);
    }
}
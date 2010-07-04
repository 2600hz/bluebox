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
            kohana::log('debug', 'Base model `' .$base .'`has no relation to number');

            return TRUE;
        }

        kohana::log('debug', 'Base model `' .$base .'` found reference to number');

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

    public static function updateAssignment()
    {
        $base = Event::$data;

        if (!isset($_POST['numbers']))
        {
            return TRUE;
        }

        // When this happens we need to do something to attach once we know
        // the number_id
        if (Router::$method == 'create')
        {
            return;
        }

        try
        {
            //$base->loadReference('Number');
        } 
        catch (Exception $e)
        {
            return TRUE;
        }

        kohana::log('debug', 'Updating number assignements on ' .get_class($base));

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

        
        if (!empty($base['Number'][0]))
        {
        foreach ($base['Number']->toArray() as $key => $number)
        {
            if (empty($_POST['numbers']['assigned'][$number['number_id']]))
            {
                Kohana::log('debug', 'Attempting to unmap number id # ' .$number['number_id'] .' from ' .$class_type .' id #' .$foreign_id);

                $base['Number'][$key]['foreign_id'] = 0;

                $base['Number'][$key]['class_type'] = NULL;    
            } 
            else
            {
                if (isset($_POST['number' .$number['number_id']]['registry']))
                {
                    Kohana::log('debug', 'Updating registry for number id # ' . $number['number_id']);

                    $base['Number'][$key]['registry'] = arr::merge(
                            $number['registry'],
                            $_POST['number' .$number['number_id']]['registry']
                    );
                }

                if (isset($_POST['number' .$number['number_id']]['dialplan']))
                {
                    Kohana::log('debug', 'Updating dialplan for number id # ' . $number['number_id']);

                    $base['Number'][$key]['dialplan'] = arr::merge(
                            $number['dialplan'],
                            $_POST['number' .$number['number_id']]['dialplan']
                    );
                }

                unset($_POST['numbers']['assigned'][$number['number_id']]);
            }
        }
        }
        
        if (empty($_POST['numbers']['assigned']))
        {
            return TRUE;
        }

        foreach($_POST['numbers']['assigned'] as $number_id => $details)
        {
            Kohana::log('debug', 'Attempting to map number id # ' .$number_id .' to ' .$class_type .' id #' .$foreign_id);

            $newNumber = Doctrine::getTable('Number')->find($number_id);
            
            $newNumber['class_type'] = $class_type;

            $newNumber['foreign_id'] = $foreign_id;

            if (isset($_POST['number' .$number_id]['registry']))
            {
                Kohana::log('debug', 'Updating registry for number id # ' .$number_id);

                $newNumber['registry'] = arr::merge(
                        $newNumber['registry'],
                        $_POST['number' .$number_id]['registry']
                );
            }

            if (isset($_POST['number' .$number_id]['dialplan']))
            {
                Kohana::log('debug', 'Updating dialplan for number id # ' .$number_id);

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

        kohana::log('debug', 'Running custom validation of number on ' .get_class($base));

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

        if (preg_match('/^1?([2-9][0-8][0-9][2-9][0-9]{6})$/', $base['number'], $matches))
        {
            $base['number'] = $matches[1];

            $base['type'] = Number::TYPE_EXTERNAL;

            if (count($numberContexts) > 1)
            {
                throw new Exception('Inbound telephony numbers can only belong to one context');
            } 
            else if (!empty($base['NumberContext'][0]['Context']['registry']['outbound']))
            {
                throw new Exception('Inbound telephony numbers can not belong to contexts that can make outbound calls');
            }
        }

        if (users::$user['user_type'] <= User::TYPE_ACCOUNT_ADMIN)
        {
            if (!empty($modified['number']) && strlen($modified['number']) > 7)
            {
                $validation->add_error('number[number]', 'Numbers must be shorter than 7 digits');

                throw new Bluebox_Validation_Exception('User is not authorized to add numbers greater than 7 digits');
            }

            $base['number'] = preg_replace('/[^0-9\*#]/', '', $base['number']);
        }

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
}
<?php defined('SYSPATH') or die('No direct access allowed.');

class CallCenter_Controller extends Bluebox_Controller
{
    public function index()
    {
        stylesheet::add('callcenter');
        javascript::add('callcenter');
        javascript::add('jquery-ui-1-7-2-custom');

        // Prepare device info for use
        $device_info = array();

        $location_id = users::getAttr('location_id');

        $location = Doctrine::getTable('Location')->findOneBy('location_id', $location_id);

        foreach($location['User'] as $user)
        {
            foreach($user['Device'] as $device)
            {   
                $device_info[] = array('name' => $device['name'], 'id' => $device['device_id']);
            }
        }

        $this->view->devices = $device_info;
        $this->view->domain = $location['domain'];
    }

    public function agents($agent_id = NULL)
    {
        //A method to make setting up APIs a breeze
        $envelope = self::setupAPI($this);

        $verb = self::requireVerb($envelope);

        switch($verb)
        {
            case 'GET':
                if (is_null($agent_id))
                {
                    $agent = Doctrine::getTable('Agent')->findAll(Doctrine::HYDRATE_ARRAY);
                }
                else
                {
                    $agent = Doctrine::getTable('Agent')->findOneBy('agent_id', $agent_id, Doctrine::HYDRATE_ARRAY);

                    if (!$agent)
                    {
                        self::throwErrorAndDie('Invalid identifier', array($agent_id), 410);
                    }
                }

                self::returnSuccessAndDie($agent);

                break;

            case 'POST':
                try
                {
                    if (is_null($agent_id))
                    {
                        self::throwErrorAndDie('Invalid request', array($agent_id), 410);
                    }

                    $data = self::requireData($envelope);

                    $agent = Doctrine::getTable('Agent')->findOneBy('agent_id', $agent_id);

                    if (!$agent)
                    {
                        self::throwErrorAndDie('invalid identifier', array($agent_id), 410);
                    }

                    $agent->synchronizeWithArray($data);

                    $agent->save();

                    self::returnSuccessAndDie($agent->toArray());
                }
                catch (Exception $e)
                {
                    self::throwErrorAndDie('invalid data', Bluebox_Controller::$validation->errors(), 400);
                }

                break;
//
//                $agent = NULL;
//
//                if(isset($envelope->data))
//                {
//                    $data = $envelope->data;
//
//                    if(isset($data->agent_id))
//                    {
//                        $id = $data->agent_id;
//
//                        if(is_array($id))
//                        {
//                            foreach($id as $_id)
//                            {
//                                if(is_int($_id))
//                                {
//                                    $agent[] = Doctrine::getTable('Agent')->findOneBy('agent_id', $_id, Doctrine::HYDRATE_ARRAY);
//                                }
//                                else
//                                {
//                                    self::throwErrorAndDie('Invalid id passed');
//                                }
//                            }
//                        }
//                        else if(is_int($id))
//                        {
//                            $agent = Doctrine::getTable('Agent')->findOneBy('agent_id', $id, Doctrine::HYDRATE_ARRAY);
//                        }
//                        else
//                        {
//                            self::throwErrorAndDie('Invalid id passed');
//                        }
//                    }
//                }
//
//                if(!isset($agent))
//                {
//                    $agent = Doctrine::getTable('Agent')->findAll(Doctrine::HYDRATE_ARRAY);
//                }
//
//                self::returnSuccessAndDie($agent);
//
//                break;

            case 'PUT':
                try
                {
                    if (!is_null($agent_id))
                    {
                        self::throwErrorAndDie('invalid identifier', array($agent_id), 410);
                    }

                    $data = self::requireData($envelope);

                    $agent = new Agent();

                    $agent->synchronizeWithArray($data);

                    $agent->save();

                    self::returnSuccessAndDie($agent->toArray());
                }
                catch (Exception $e)
                {
                    self::throwErrorAndDie('invalid data', Bluebox_Controller::$validation->errors(), 400);
                }

                break;

//
//                $data = self::requireData($envelope, 'The verb given to this API requires data to be present');
//
//                if(isset($data->id))
//                {
//                    $putType = "edit";
//                    if(!($agent = Doctrine::getTable('Agent')->findOneBy('agent_id', $data->id)))
//                    {
//                        $putType = "create";
//                        $agent = new Agent();
//                        $agent['agent_id'] = $data->id;
//                    }
//                }
//                else
//                {
//                    $putType = "create";
//                    $agent = new Agent();
//                }
//
//                if($putType == "create")
//                {
//                    if(!(isset($data->name) && isset($data->type) && isset($data->device_id)))
//                    {
//                        self::throwErrorAndDie('When creating an new record, all fields must be present');
//                    }
//                }
//
//                if(isset($data->name))
//                {
//                    $agent['name'] = $data->name;
//                }
//
//                if(isset($data->type))
//                {
//                    $agent['type'] = $data->type;
//                }
//
//                if(isset($data->device_id))
//                {
//                    if(!($device = Doctrine::getTable('Device')->findOneBy('device_id', $data->device_id)))
//                    {
//                        self::throwErrorAndDie('Invalid device id specified');
//                    }
//
//                    $agent['device_id'] = $data->device_id;
//                }
//
//                if(isset($data->registry))
//                {
//                    if(is_array($data->registry))
//                    {
//                        $registry = array();
//                        foreach($data->registry as $entry)
//                        {
//                            if(is_string($entry->name))
//                            {
//                                $registry[$entry->name] = $entry->value;
//                            }
//                            else
//                            {
//                                self::throwErrorAndDie('The registry invalid registry key');
//                            }
//                        }
//
//                        $agent->registry = arr::merge($agent->registry, $registry);
//                    }
//                    else
//                    {
//                        self::throwErrorAndDie('The registry must be an array');
//                    }
//                }
//
//                $agent->save();
//
//                self::returnSuccessAndDie($agent->toArray());
//
//                break;

            case 'DELETE':
                if (is_null($agent_id))
                {
                    self::throwErrorAndDie('invalid identifier', array($agent_id), 410);
                }

                $agent = Doctrine::getTable('Agent')->findOneBy('agent_id', $agent_id);

                if (!$agent)
                {
                    self::throwErrorAndDie('invalid identifier', array($agent_id), 410);
                }

                $agent->delete();

                self::returnSuccessAndDie(array());

                break;

            default:
                self::throwErrorAndDie('Verb does not exists');

                break;
        }
    }

    private static function setupAPI(&$obj, $msg = 'Nothing was passed')
    {
        $obj->auto_render = FALSE;

        if(($envelope = self::prepareEnvelope()))
        {
            return $envelope;
        }
        else
        {
            self::throwErrorAndDie($msg);
        }
    }

    private static function requireVerb($envelope, $msg = 'This API call requires a verb')
    {
        if(isset($envelope['verb']))
        {
            return $envelope['verb'];
        }
        else
        {
            self::throwErrorAndDie($msg);
        }
    }

    private static function requireData($envelope, $msg = 'This API call requires a verb')
    {
        if(isset($envelope['data']))
        {
            return $envelope['data'];
        }
        else
        {
            self::throwErrorAndDie($msg);
        }
    }

    private static function returnSuccessAndDie($data = NULL)
    {
        $reply = array('status' => 'success', 'data' => $data);

        echo json_encode($reply);
        flush();
        die();
    }

    private static function throwErrorAndDie($msg, $data = NULL, $code = NULL)
    {
        $error = array('status' => 'error', 'message' => $msg, 'data' => $data);

        if(isset($code))
        {
            $error['error'] = $code;
        }

        echo json_encode($error);
        flush();
        die();
    }

    private static function prepareEnvelope()
    {
        $json = json_decode(@file_get_contents('php://input'), TRUE);

        if ($json === NULL)
        {
            return FALSE;
        }

        return $json;
    }
}
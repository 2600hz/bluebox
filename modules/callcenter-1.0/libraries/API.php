<?php defined('SYSPATH') or die('No direct access allowed.');

class API
{
    public static function _API($id, $idName, $tableName, $functions = array())
    {
        //A method to make setting up APIs a breeze
        $envelope = self::setupAPI($this);

        $verb = self::requireVerb($envelope);

        switch($verb)
        {
            case 'GET':
                if(($function = arr::get($functions, 'GET')))
                {
                    eval('$response = ' . $function . ';');
                }
                else
                {
                    $response = self::generalAPI_GET($id, $idName, $tableName, $envelope);
                }

                self::returnSuccessAndDie($response);
                
                break;

            case 'POST':
                if(($function = arr::get($functions, 'POST')))
                {
                    eval('$response = ' . $function . ';');
                }
                else
                {
                    $response = self::generalAPI_POST($id, $idName, $tableName, $envelope);
                }

                self::returnSuccessAndDie($response);

                break;

            case 'PUT':
                if(($function = arr::get($functions, 'PUT')))
                {
                    eval('$response = ' . $function . ';');
                }
                else
                {
                    $response = self::generalAPI_PUT($id, $idName, $tableName, $envelope);
                }

                self::returnSuccessAndDie($response);

                break;

            case 'DELETE':
                if(($function = arr::get($functions, 'DELETE')))
                {
                    eval('$response = ' . $function . ';');
                }
                else
                {
                    $response = self::generalAPI_DELETE($id, $idName, $tableName, $envelope);
                }

                self::returnSuccessAndDie($response);

                break;

            default:
                self::throwErrorAndDie('Verb does not exists');

                break;
        }
    }

    protected static function generalAPI_GET($id, $idName, $tableName, $envelope)
    {
        if (is_null($id))
        {
            $generic = Doctrine::getTable($tableName)->findAll(Doctrine::HYDRATE_ARRAY);
        }
        else
        {
            $generic = Doctrine::getTable($tableName)->findOneBy($idName, $id, Doctrine::HYDRATE_ARRAY);

            if (!$generic)
            {
                self::throwErrorAndDie('Invalid identifier', array($id), 410);
            }
        }

        return $generic;
    }

    protected static function generalAPI_POST($id, $idName, $tableName, $envelope)
    {
        try
        {
            if (is_null($id))
            {
                self::throwErrorAndDie('Invalid request', array($id), 410);
            }

            $data = self::requireData($envelope);

            $generic = Doctrine::getTable($tableName)->findOneBy($idName, $id);

            if (!$generic)
            {
                self::throwErrorAndDie('Invalid identifier', array($id), 410);
            }

            $generic->synchronizeWithArray($data);

            $generic->save();

            return $generic->toArray();
        }
        catch (Exception $e)
        {
            Kohana::log('debug', 'MISS');
            self::throwErrorAndDie('Invalid data', Bluebox_Controller::$validation->errors(), 400);
        }
    }

    protected static function generalAPI_PUT($id, $idName, $tableName, $envelope)
    {
        try
        {
            if (!is_null($id))
            {
                self::throwErrorAndDie('Invalid identifier', array($id), 410);
            }

            $data = self::requireData($envelope);

            eval('$generic = new ' . $tableName . '();');

            $generic->synchronizeWithArray($data);

            $generic->save();

            return $generic->toArray();
        }
        catch (Exception $e)
        {
            self::throwErrorAndDie('Invalid data', Bluebox_Controller::$validation->errors(), 400);
        }
    }

    protected static function generalAPI_DELETE($id, $idName, $tableName, $envelope)
    {
        if (is_null($id))
        {
            self::throwErrorAndDie('Invalid identifier', array($id), 410);
        }

        $generic = Doctrine::getTable($tableName)->findOneBy($idName, $id);

        if (!$generic)
        {
            self::throwErrorAndDie('Invalid identifier', array($id), 410);
        }

        $generic->delete();

        return array();
    }

    protected static function setupAPI(&$obj, $msg = 'Nothing was passed')
    {
        $obj->auto_render = FALSE;
        
        Doctrine_Manager::connection()->beginTransaction();

        if(($envelope = self::prepareEnvelope()))
        {
            return $envelope;
        }
        else
        {
            self::throwErrorAndDie($msg);
        }
    }

    protected static function prepareEnvelope()
    {
        $json = json_decode(@file_get_contents('php://input'), TRUE);

        if ($json === NULL)
        {
            return FALSE;
        }

        return $json;
    }
    
    protected static function requireVerb($envelope, $msg = 'This API call requires a verb')
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

    protected static function requireData($envelope, $msg = 'This API call requires a verb')
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

    protected static function returnSuccessAndDie($data = NULL)
    {
        Doctrine_Manager::connection()->commit();

        $reply = array('status' => 'success', 'data' => $data);

        echo json_encode($reply);
        flush();
        die();
    }

    protected static function throwErrorAndDie($msg, $data = NULL, $code = NULL)
    {
        Doctrine_Manager::connection()->rollback();
        
        $error = array('status' => 'error', 'message' => $msg, 'data' => $data);

        if(isset($code))
        {
            $error['error'] = $code;
        }

        echo json_encode($error);
        flush();
        die();
    }
}
<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * @package    Asterisk
 * @author     K Anderson <bitbashing@gmail.com>
 * @license    Mozilla Public License (MPL)
 * @created    Oct 5, 2009
 */
/**
 *
 * Portions of this code are from the Asterisk PHP-AG API project. The license for that project supercedes this license for the files
 * contained within this module.
 */
class AsteriskManager
{
    /**
     * Config variables
     *
     * @var array
     * @access public
     */
    public $config;

    /**
     * Socket
     */
    public $socket = NULL;

    /**
     * Host we are connected to
     *
     * @var string
     */
    private $host;

    /**
     * Port on the host we are connected to
     *
     * @var integer
     */
    private $port;

    /**
     * Queued updates from the queuedConfigUpdate() methods
     *
     * @var array
     */
    private $queuedUpdates = array();

    /*
    * Error constants if things fail
    */
    const ERROR_SOCKET_CLOSED = 1;
    const ERROR_INVALID_COMMAND = 2;
    const AMI_DEL_FAIL1 = 'Delete did not complete successfully';
    const AMI_DEL_FAIL2 = 'Delete category did not complete successfully';
    const AMI_CAT_FAIL = 'Create category did not complete successfully';
    const AMI_BAD_FILENAME = 'Filename not specified';
    const AMI_CFG_MISSING = 'Config file not found';
    const AMI_SAVE_FAIL = 'Save of config failed';
    const AMI_BAD_CHANNEL1 = 'No channel specified';
    const AMI_BAD_CHANNEL2 = 'Channel not specified';
    const AMI_BAD_VAR = 'No variable specified';
    const AMI_BAD_PRIO = 'Invalid priority';
    const AMI_BAD_TIME1 = 'Invalid timeout';
    const AMI_BAD_TIME2 = 'No timeout specified';
    const AMI_BAD_CHNL = 'Invalid channel';
    const AMI_BAD_MAIL = 'Mailbox not specified';
    const AMI_BAD_EXTN = 'Extension not specified';
    const AMI_BAD_ACT = 'Missing action in request';
    const AMI_NO_CHANNEL1 = 'No such channel';
    const AMI_NO_CHANNEL2 = 'Channel does not exist:';
    const AMI_XFER_FAIL1 = 'Redirect failed';
    const AMI_XFER_FAIL2 = 'Redirect failed, channel not up';
    const AMI_XFER_FAIL3 = 'Redirect failed, extra channel not up';
    const AMI_XFER_FAIL4 = 'Secondary redirect failed';
    const AMI_BAD_CMD1 = 'Invalid/unknown command';
    const AMI_BAD_CMD2 = 'No command provided';
    const AMI_BLKLIST = 'Command blacklisted';
    const AMI_ORGIN_FAIL = 'Originate failed';
    const AMI_BAD_ORGIN = 'Originate with \'Exten\' requires \'Context\' and \'Priority\'';
    const AMI_BAD_AUTH = 'Must specify AuthType';
    const AMI_NEED_AUTH = 'Authentication Required';
    const AMI_AUTH_FAIL = 'Authentication failed';
    const AMI_DENIED = 'Permission denied';

    /*
    * Valid actions for configuration update commands
    */
    private $allowedConfigActions = array(
        'NewCat',
        'RenameCat',
        'DelCat',
        'Update',
        'Delete',
        'Append'
    );

    /**
     * Constructor
     *
     * @param string $config is the name of the config file to parse or a parent agi from which to read the config
     * @param array $optconfig is an array of configuration vars and vals, stuffed into $this->config
     */
    public function __construct($config = array())
    {
        // add defaults if config is not set
        if (!isset($config['host']))
        {
            $config['host'] = 'localhost';
        }

        if (!isset($config['port']))
        {
            $config['port'] = 5038;
        }

        if (!isset($config['username']))
        {
            $config['username'] = 'phpagi';
        }

        if (!isset($config['password']))
        {
            $config['password'] = 'phpagi';
        }
        
        $this->config = $config;
    }
    
    /**
     * Return a string of the current host we're connected to, or NULL if not connected
     *
     * @return string
     */
    public function getActiveHost()
    {
        return $this->host;
    }

    public function getActivePort()
    {
        return $this->port;
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function setConfig($config)
    {
        if (!isset($config['host']))
        {
            $config['host'] = 'localhost';
        }

        if (!isset($config['port']))
        {
            $config['port'] = 5038;
        }

        if (!isset($config['username']))
        {
            $config['username'] = 'phpagi';
        }
        
        if (!isset($config['password']))
        {
            $config['password'] = 'phpagi';
        }
        
        $this->config = $config;
    }

    /**
     * Temporary logger. This needs to be replaced, probably just by throwing exceptions
     * @param string $message
     */
    private function log($message)
    {
        Kohana::log('info', $message);
    }

    /**
     * Send a request
     *
     * @param string $action
     * @param array $parameters
     * @return array of parameters
     */
    public function send($action, $parameters = array())
    {
        if ($this->socket)
        {
            $req = "Action: $action\r\n";

            foreach($parameters as $var => $val)
            {
                $req.= "$var: $val\r\n";
            }

            $req.= "\r\n";

            fwrite($this->socket, $req);

            //Kohana::log('debug', print_r($req, TRUE));

            $tmp = $this->waitResponse();

            //Kohana::log('debug', print_r($tmp, TRUE));

            return $tmp;
        } 
        else
        {
            throw new AsteriskManager_Exception('Asterisk Manager socket is not active', self::ERROR_SOCKET_CLOSED);
        }
    }

    /**
     * Wait for a response
     *
     * If a request was just sent, this will return the response.
     * Otherwise, it will loop forever, handling events.
     *
     * @param boolean $allow_timeout if the socket times out, return an empty array
     * @return array of parameters, empty on timeout
     */
    public function waitResponse($allow_timeout = false)
    {
        $timeout = false;

        do
        {
            $type = NULL;

            $parameters = array();

            if (feof($this->socket))
            {
                return false;
            }

            $buffer = trim(fgets($this->socket, 4096));

            while ($buffer != '')
            {
                $a = strpos($buffer, ':');

                if ($a) 
                {
                    // first line in a response?
                    if (!count($parameters)) 
                    {
                        $type = strtolower(substr($buffer, 0, $a));

                        if (substr($buffer, $a + 2) == 'Follows')
                        {
                            // A follows response means there is a miltiline field that follows.
                            $parameters['data'] = '';

                            $buff = fgets($this->socket, 4096);

                            while (substr($buff, 0, 6) != '--END ')
                            {
                                $parameters['data'].= $buff;

                                $buff = fgets($this->socket, 4096);
                            }
                        }
                    }

                    // store parameter in $parameters
                    $parameters[substr($buffer, 0, $a) ] = substr($buffer, $a + 2);
                }

                $buffer = trim(fgets($this->socket, 4096));
            }

            // process response
            switch ($type) 
            {
                case '': // timeout occured
                    $timeout = $allow_timeout;

                    break;

                case 'event':
                    //$this->process_event($parameters);

                    break;

                case 'response':
                    break;

                default:
                    $this->log('Unhandled response packet from Manager: ' . print_r($parameters, true));

                    break;
            }
        }

        while ($type != 'response' && !$timeout);

        return $parameters;
    }

    /**
     * Connect to Asterisk
     *
     * @param string $host
     * @param string $username
     * @param string $password
     * @return boolean true on success
     */
    public function connect($host = NULL, $username = NULL, $password = NULL, $events = 'on')
    {
        // use config if not specified
        if (is_null($host))
        {
            $host = $this->config['host'];
        }

        if (is_null($username))
        {
            $username = $this->config['username'];
        }

        if (is_null($password))
        {
            $password = $this->config['password'];
        }
        
        // get port from host if specified
        if (strpos($host, ':') !== false)
        {
            $c = explode(':', $host);

            $this->host = $c[0];

            $this->port = $c[1];
        } 
        else
        {
            $this->host = $host;

            $this->port = $this->config['port'];
        }

        // connect the socket
        $errno = $errstr = NULL;

        $this->socket = @fsockopen($this->host, $this->port, $errno, $errstr);

        if (!$this->socket)
        {
            $this->log("Unable to connect to manager {$this->host}:{$this->port} ($errno): $errstr");

            return false;
        }

        // read the header
        $str = fgets($this->socket);

        if ($str == false)
        {
            // a problem.
            $this->log("Asterisk Manager header not received.");

            return false;
        } 
        else
        {
            // note: don't $this->log($str) until someone looks to see why it mangles the logging    
        }
        
        // login
        $res = $this->send('login', array(
            'Username' => $username,
            'Secret' => $password,
            'Events' => $events
        ));

        if ($res['Response'] != 'Success')
        {
            $this->log("Failed to login.");

            $this->disconnect();

            return false;
        }

        $this->log("Connected to {$this->host}:{$this->port}.");

        return true;
    }

    /**
     * Queue an Asterisk configuration file update. You can later save any queued sets via commitConfigUpdate()
     *
     * This function does not require an active connection to the AMI in order to queue updates. This is useful such that
     * independent pieces of code can request saves without knowing whether they are successful or not, and things can
     * be saved in batch. You can also execute a series of identical updates on multiple machines.
     *
     * This function is also dangerous. If you forget to commit your updates or your program crashes, the updates go away.
     * Specifically, this presents an issue if your application updates something in the database and queues an update
     * and then your software crashes before commiting the update, as your database and your host will be out of sync.
     *
     * If you wish to commit your config update immediately, call commitConfigUpdates() immediately after this method.
     *
     * Program wisely.
     *
     * @param string $file The filename of the configuration file from which to read the current information.
     * @param string $action An action to take. Must be NewCat, RenameCat, DelCat, Update, Delete, or Append.
     * @param string $category The name of the category to operate on.
     * @param string $variable The name of the variable to operate on.
     * @param string $value The value of the variable to operate on.
     * @param array $parameters Additional key value pairs to influence the configUpdate
     * @return boolean Returns true if the command is recognized.
     */
    public function queueConfigUpdate($file, $action, $category, $variable = NULL, $value = NULL, $params = array())
    {
        if (in_array($action, $this->allowedConfigActions))
        {
            //$id = str_pad(count($this->queuedUpdates), 6, '0', STR_PAD_LEFT);   // Get current command identifier
            $id = '000000';

            // Convert all the keys to lowercase to simplify the upcoming logic
            if (is_array($params))
            {
                $params = array_change_key_case($params);
            }

//            // A list of updateconfig actions that should be preceded by a delete
//            $predeleteActions = array(
//                'insert',
//                'update',
//                'append'
//            );
//            // If the updateconfig action should be preceded by a delete then add that in
//            // Once we start workin on dialplan this may be too broad....
//            if (in_array(strtolower($action) , $predeleteActions) && empty($params['skippredelete'])) {
//                $params = array_merge($params, array(
//                    'ignoreresponse' => array(
//                        AsteriskManager::AMI_DEL_FAIL1
//                    )
//                ));
//                $this->queueConfigUpdate($file, 'Delete', $category, $variable, $value, $params);
//            }
//
            // Set the required arguments
            $options = array(
                'SrcFilename' => $file,
                'DstFilename' => $file,
                'Action-' . $id => $action,
                'Cat-' . $id => $category
            );
            
            // Merge any arguments that require ids
            if (!empty($variable))
            {
                $options['Var-' . $id] = $variable;
            }

            if (!empty($value))
            {
                $options['Value-' . $id] = $value;
            }

            // Merge any optional arguments that require ids
            if (!empty($params['match']))
            {
                $options['Match-' . $id] = $params['match'];
            }

            if (isset($params['line']))
            {
                $options['Line-' . $id] = $params['line'];
            }

            // merge any remaing parameters that may have been provided
            if (!empty($params) && is_array($params))
            {
                $options+= $params;
            }

            if (isset($params['sendimmediate']))
            {
                $localParams = array(
                    'skippredelete',
                    'match',
                    'line',
                    'sendimmediate'
                );

                $options = array_diff_key($options, array_flip($localParams));

                // Execute this update immediatey if it is marked as such
                return $this->send('UpdateConfig', $options);
            } 
            else
            {
                $localParams = array(
                    'skippredelete',
                    'match',
                    'line',
                    'sendimmediate'
                );

                $options = array_diff_key($options, array_flip($localParams));

                // Queue up for use later
                $this->queuedUpdates[] = $options;
            }

            return TRUE;
        } 
        else
        {
            throw new AsteriskManager_Exception('Unknown action specified to queueConfigUpdate (' . $action . ')', self::ERROR_INVALID_COMMAND);
        }
    }

    /**
     * Immediately request a configuration change to the current Asterisk server. One command only - returns immediately
     *
     * @param string $file The filename of the configuration file from which to read the current information.
     * @param string $action An action to take. Must be NewCat, RenameCat, DelCat, Update, Delete, or Append.
     * @param string $category The name of the category to operate on.
     * @param string $variable The name of the variable to operate on.
     * @param string $value The value of the variable to operate on.
     * @param array $parameters Additional key value pairs to influence the configUpdate
     * @return boolean Returns true if the command is recognized.
     */
    public function executeConfigUpdate($file, $action, $category, $variable = NULL, $value = NULL, $params = array())
    {
        // Mark this updat to be sent out immediately
        $params+= array(
            'sendImmediate' => true
        );
        
        $this->queueConfigUpdate($file, $action, $category, $variable, $value, $params);
    }

    /**
     *
     * Note that the persistUpdates variable, if set to true, will retain all config update requests in memory after the commit.
     * This is useful if you wish to disconnect and reconnect to an alternate host and execute the exact same commands again.
     *
     * @param boolean $reload
     * @param boolean $persistUpdates Whether to persist queued updates in memory (not clear them).
     * @return <type>
     */
    public function commitConfigUpdates($reload = FALSE, $persistUpdates = FALSE, array $options = array())
    {
        if (empty($this->queuedUpdates))
        {
            return TRUE;
        }

        $ignoreResponse = array();

        foreach($this->queuedUpdates as $update)
        {
            if (!isset($update['Reload']) and ($reload))
            {
                $update['Reload'] = $reload;
            }

            if (isset($update['ignoreresponse']))
            {
                $ignoreResponse = (array)$update['ignoreresponse'];

                unset($update['ignoreresponse']);
            }

            $result = $this->send('UpdateConfig', $update);

            if ($result['Response'] != 'Success' && !in_array($result['Message'], $ignoreResponse))
            {
                if (!$persistUpdates)
                {
                    $this->queuedUpdates = array();
                }

                throw new AsteriskManager_Exception("Error during AMI transaction: " . $result['Message'], self::ERROR_INVALID_COMMAND);
            }
        }

        if (!$persistUpdates)
        {
            $this->queuedUpdates = array();
        }

        return TRUE;
    }

    public function loadConfigContext($filename, $context)
    {
        if (empty($filename) || empty($context))
        {
            return array();
        } 
        else
        {
            $config = array(
                'filename' => $filename,
                'category' => $context
            );
        }

        $result = $this->send('GetConfig', $config);

        if ($result['Response'] != 'Success')
        {
            return array();
        }

        $result = array_filter(array_flip($result) , array(
            $this,
            "filter_loadConfig"
        ));

        if (!is_array($result))
        {
            $result = array();
        }
        
        return array_flip($result);
    }

    /**
     * Cancel configuration changes that were queued
     */
    public function cancelConfigUpdates()
    {
        $this->queuedUpdates = array();
    }

    /**
     * Disconnect
     */
    public function disconnect()
    {
        if ($this->socket)
        {
            $this->logoff();

            fclose($this->socket);

            $this->log("Disconnected from {$this->host}:{$this->port}.");
        }

        $this->host = NULL;
        
        $this->port = NULL;
    }

    /**
     * Check if the socket is connected
     *
     */
    public function connected()
    {
        return (bool)$this->socket;
    }

    /**
     * Execute Command
     *
     * @param string $commadn
     */
    public function command($command)
    {
        return $this->send('Command', array(
            'Command' => $command
        ));
    }

    /**
     * Logoff Manager
     */
    public function logoff()
    {
        return $this->send('Logoff');
    }

    private function filter_loadConfig($value)
    {
        if (strstr($value, 'Category'))
        {
            return FALSE;
        }

        if (strstr($value, 'Response'))
        {
            return FALSE;
        }
        
        return TRUE;
    }
}
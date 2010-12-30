<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * ESLconnection.php - This provides ESLconnection when the native ESL
 *      extension is not avaliable
 *
 * @author K Anderson
 * @license LGPL
 * @package Esl
 */
class ESLconnection extends FS_Socket {

    private $eventQueue = array();

    private $sentCommand = FALSE;

    private $authenticated = FALSE;

    private $eventLock = FALSE;

    private $asyncExecute = FALSE;

    /**
     * Initializes a new instance of ESLconnection, and connects to the host 
     * $host on the port $port, and supplies $password to freeswitch. 
     */
    public function __construct($host = NULL, $port = NULL, $auth = NULL, $options = array()) {
        try {
            // attempt to open the socket
            $this->connect($host, $port, $options);

            // get the initial header
            $event = $this->recvEvent();

            // did we get the request for auth?
            if ($event->getHeader('Content-Type') !=  'auth/request') {
                $this->_throwError("unexpected header recieved during authentication: " . $event->getType());
            }

            // send our auth
            $event = $this->sendRecv("auth {$auth}");

            // was our auth accepted?
            $reply = $event->getHeader('Reply-Text');
            if (!strstr($reply, '+OK')) {
                $this->_throwError("connection refused: {$reply}");
            }

            // we are authenticated!
            $this->authenticated = TRUE;
            return TRUE;
        } catch (Exception $e) {
            return FALSE;
        }
    }

    public function __destruct() {
        // cleanly exit
        $this->disconnect();
    }

    /**
     * Returns the UNIX file descriptor for the connection object, if the
     * connection object is connected. This is the same file descriptor that was
     * passed to new($fd) when used in outbound mode. 
     */
    public function socketDescriptor() {
        try {
            return $this->getStatus();
        } catch (Exception $e) {
            return FALSE;
        }
    }

    /**
     * Test if the connection object is connected. Returns 1 if connected,
     * 0 otherwise.
     */
    public function connected() {
        if ($this->validateConnection() && $this->authenticated) {
            return TRUE;
        }
        return FALSE;
    }

    /**
     * When FS connects to an "Event Socket Outbound" handler, it sends a
     * "CHANNEL_DATA" event as the first event after the initial connection.
     * getInfo() returns an ESLevent that contains this Channel Data.
     * getInfo() returns NULL when used on an "Event Socket Inbound" connection.
     */
    public function getInfo() {
        $this->_throwError("has not implemented this yet!");
    }

    /**
     * Sends a command to FreeSwitch. Does not wait for a reply. You should
     * immediately call recvEvent or recvEventTimed in a loop until you get
     * the reply. The reply event will have a header named "content-type" that
     * has a value of "api/response" or "command/reply". To automatically wait
     * for the reply event, use sendRecv() instead of send().
     */
    public function send($command) {
        if (empty($command)) {
            $this->_throwError("requires non-blank command to send.");
        }
        
        // send the command out of the socket
        try {
            return $this->sendCmd($command);
        } catch (Exception $e) {
            return FALSE;
        }
    }

    /**
     * Internally sendRecv($command) calls send($command) then recvEvent(), and 
     * returns an instance of ESLevent. recvEvent() is called in a loop until it 
     * receives an event with a header named "content-type" that has a value of 
     * "api/response" or "command/reply", and then returns it as an instance of 
     * ESLevent. Any events that are received by recvEvent() prior to the reply 
     * event are queued up, and will get returned on subsequent calls to 
     * recvEvent() in your program. 
     */
    public function sendRecv($command) {
        // setup an array of content-types to wait for
        $waitFor = array('api/response', 'command/reply');
        
        // set a flag so recvEvent ignores the event queue
        $this->sentCommand = TRUE;

        // send the command
        $this->send($command);

        // collect and queue all the events
        do {
            $event = $this->recvEvent();
            $this->eventQueue[] = $event;
        } while (!in_array($event->getHeader('Content-Type'), $waitFor));

        // clear the flag so recvEvent uses the event queue
        $this->sentCommand = FALSE;
        
        // the last queued event was of the content-type we where waiting for,
        // so pop one off
        return array_pop($this->eventQueue);
    }

    /**
     * Send an API command to the FreeSWITCH server. This method blocks further 
     * execution until the command has been executed. api($command, $args) is 
     * identical to sendRecv("api $command $args"). 
     */
    public function api() {
        $args = func_get_args();
        $command = array_shift($args);

        $command = 'api ' .$command .' ' .implode(' ', $args);

        return $this->sendRecv($command);
    }

    /**
     * Send a background API command to the FreeSWITCH server to be executed in
     * it's own thread. This will be executed in it's own thread, and is
     * non-blocking. bgapi($command, $args) is identical to
     * sendRecv("bgapi $command $args") 
     */
    public function bgapi() {
        $args = func_get_args();
        $command = array_shift($args);

        $command = 'bgapi ' .$command .' ' .implode(' ', $args);

        return $this->sendRecv($command);
    }

    public function sendEvent($event) {
        $this->_throwError("does not implement this becuase there is no info on it in the docs!");

        //$command = 'sendevent ' .$event->name . ' ' .$event->serialize();
    }

    /**
     * Returns the next event from FreeSwitch. If no events are waiting, this
     * call will block until an event arrives. If any events were queued during
     * a call to sendRecv(), then the first one will be returned, and removed
     * from the queue. Otherwise, then next event will be read from the
     * connection.
     */
    public function recvEvent() {
        // if we are not waiting for an event and the event queue is not empty
        // shift one off
        if (!$this->sentCommand && !empty($this->eventQueue)) {
            return array_shift($this->eventQueue);
        }

        // wait for the first line
        $this->setBlocking();
        do {
            $line = $this->readLine();

            // if we timeout while waiting return NULL
            $streamMeta = $this->getMetaData();
            if (!empty($streamMeta['timed_out'])) {
                return NULL;
            }
        } while (empty($line));

        // save our first line
        $response = array($line);

        // keep reading the buffer untill we get a new line
        $this->setNonBlocking();
        do {
            $line = $response[] = $this->readLine();
        } while ($line != "\n");

        // build a new event from our response
        $event = new ESLevent($response);

        // if the response contains a content-length ...
        if ($contentLen = $event->getHeader('Content-Length')) {
            // ... add the content to this event
            $this->setBlocking();
            while ($contentLen > 0) {
                // our fread stops every 8192 so break up the reads into the
                // appropriate chunks
                if ($contentLen > 8192) {
                    $getLen = 8192;
                } else {
                    $getLen = $contentLen;
                }
                $event->addBody($this->getContent($getLen));
                $contentLen = $contentLen - $getLen;
            }
        }

        $contentType = $event->getHeader('Content-Type');
        if ($contentType == 'text/disconnect-notice') {
            $this->disconnect();
            return FALSE;
        }

        // return our ESLevent object
        return $event;
    }

    /**
     * Similar to recvEvent(), except that it will block for at most milliseconds.
     * A call to recvEventTimed(0) will return immediately. This is useful for
     * polling for events. 
     */
    public function recvEventTimed($milliseconds) {
        // set the stream timeout to the users preference
        $this->setTimeOut(0, $milliseconds);

        // try to get an event
        $event = $this->recvEvent();

        // restore the stream time out
        $this->restoreTimeOut();

        // return the results (null or event object)
        return $event;
    }

    /**
     * Specify event types to listen for. Note, this is not a filter out but
     * rather a "filter in," that is, when a filter is applied only the filtered
     * values are received. Multiple filters on a socket connection are allowed.
     */
    public function filter($header, $value) {
        return $this->sendRecv('filter ' .$header .' ' .$value);
    }

    /**
     * $event_type can have the value "plain" or "xml". Any other value
     * specified for $event_type gets replaced with "plain". See the event FS
     * wiki socket event command for more info. 
     */
    public function events($event_type, $value) {
        $event_type = strtolower($event_type);
        if ($event_type == 'xml') {
            $event = $this->sendRecv('event ' .$event_type .' ' .$value);
        } else {
            $event = $this->sendRecv('event plain ' .$value);
        }
        return $event;
    }

    /**
     * Execute a dialplan application, and wait for a response from the server. 
     * On socket connections not anchored to a channel (THIS!),
     * all three arguments are required -- $uuid specifies the channel to 
     * execute the application on. Returns an ESLevent object containing the 
     * response from the server. The getHeader("Reply-Text") method of this 
     * ESLevent object returns the server's response. The server's response will 
     * contain "+OK [Success Message]" on success or "-ERR [Error Message]" 
     * on failure. 
     */
    public function execute($app, $arg, $uuid) {
        $command = 'sendmsg';

        if (!empty($uuid)) {
            $command .= " {$uuid}";
        }

        $command .= "\ncall-command: execute\n";

        if (!empty($app)) {
            $command .= "execute-app-name: {$app}\n";
        }

        if (!empty($arg)) {
            $command .= "execute-app-arg: {$arg}\n";
        }

        if ($this->eventLock) {
            $command .= "event-lock: true\n";
        }

        if ($this->asyncExecute) {
            $command .= "async: true\n";
        }

        return $this->sendRecv($command);
    }

    /**
     * Same as execute, but doesn't wait for a response from the server. This
     * works by causing the underlying call to execute() to append "async: true"
     * header in the message sent to the channel. 
     */
    public function executeAsync($app, $arg, $uuid) {
        $currentAsync = $this->asyncExecute;
        $this->asyncExecute = TRUE;

        $response = $this->execute($app, $arg, $uuid);

        $this->asyncExecute = $currentAsync;

        return $response;
    }

    public function setAsyncExecute($value = NULL) {
        $this->asyncExecute = !empty($value);
        return TRUE;
    }

    /**
     * Force sync mode on for a socket connection. This command has no effect on
     *  outbound socket connections that are not set to "async" in the dialplan,
     * since these connections are already set to sync mode. $value should be 1
     * to force sync mode, and 0 to not force it. 
     */
    public function setEventLock($value = NULL) {
        $this->eventLock = !empty($value);
        return TRUE;        
    }

    /**
     * Close the socket connection to the FreeSWITCH server. 
     */
    public function disconnect() {
        // if we are connected cleanly exit
        if ($this->connected()) {
            $this->send('exit');
            $this->authenticated = FALSE;
        }
        // disconnect the socket
        return parent::disconnect();
    }

    /**
     * Throws an error
     *
     * @return void
     */
    private function _throwError($errorMessage)
    {
        message::set("ESL {$errorMessage}", 'alert');
        throw new Exception("ESL {$errorMessage}");
    }
}
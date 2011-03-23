<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * FS_Socket.php - This supports ESLconnection when the native ESL
 *      extension is not avaliable
 *
 * @author K Anderson
 * @license LGPL
 * @package Esl
 */
class FS_Socket
{
    /**
     * @var int $timeOut
     * @desc The timeout used to open the socket
     */
    private $timeOut = 10;

    /**
     * @var resource $connection
     * @desc Connection resource
     */
    private $connection = NULL;

    /**
     * @var string $connectionState
     * @desc
     */
    private $connectionState = FALSE;

    /**
     * @var float $defaultTimeout
     * @desc Default timeout for connection to a server
     */
    private $defaultTimeout = 30;

    /**
     * @var bool $persistentConnection
     * @desc Determines wether to use a persistent socket connection or not
     */
    private $persistentConnection = FALSE;

    /**
     * If there still was a connection alive, disconnect it
     */
    public function __destruct()
    {
        $this->disconnect();
    }

    /**
     * Connects to the socket with the given address and port
     *
     * @return void
     */
    protected function connect($host, $port, $options = array())
    {
        // initialize our defaults
        $options += array(
            'persistentConnection' => $this->persistentConnection,
            'timeOut' => $this->defaultTimeout,
            'blocking' => 1
        );
        extract($options);

        // store the timeOut that was actually used
        $this->timeOut = $timeOut;

        // decided the stream type
        $socketFunction = $persistentConnection ? "pfsockopen" : "fsockopen";

        // Check if the function parameters are set.
        if(empty($host))
        {
            $this->_throwError("invalid host provided!");
        }
        if(empty($port))
        {
            $this->_throwError("invalid port provided!");
        }

        // attempt to open a socket
        $connection = @$socketFunction($host, $port, $errorNumber, $errorString, $timeOut);
        $this->connection = $connection;

        // if we didnt get a valid socket throw an error
        if($connection == false)
        {
            $this->_throwError("connection to {$host}:{$port} failed: {$errorString}");
        }

        // initialize our stream blocking setting
        stream_set_blocking($this->connection, $blocking);

        // set this stream as connected
        $this->connectionState = TRUE;
    }

    /**
     * Disconnects from the server
     *
     * @return bool
     */
    protected function disconnect()
    {
        if($this->validateConnection())
        {
            fclose($this->connection);
            $this->connectionState = FALSE;

            return TRUE;
        }

        return FALSE;
    }

    /**
     * Sends a command to the server
     *
     * @return string
     */
    protected function sendCmd($command)
    {
        if($this->validateConnection())
        {
            $command = trim($command);

            kohana::log('debug', 'ESL Command "' .$command .'"');

            $command .= "\n\n";

            $result = fwrite($this->connection, $command, strlen($command));

            return $result;
        }

        $this->_throwError("sending command \"{$command}\" failed: Not connected");
    }

    /**
     * Gets the content/body of the response
     *
     * @return string
     */
    protected function getContent($contentLength = 2048)
    {
        if($this->validateConnection())
        {
            return fread($this->connection, (int)$contentLength);
        }

        $this->_throwError("receiving content from server failed: Not connected");
    }

    /**
     * Reads a line out of buffer
     *
     * @return string
     */
    protected function readLine()
    {
        if($this->validateConnection()) {
            return fgets($this->connection, 1024);
        }

        $this->_throwError("read line failed: Not connected");
    }

    /**
     * Sets the socket to blocking operations
     *
     * @return bool
     */
    protected function setBlocking() {
        if($this->validateConnection()) {
            return stream_set_blocking($this->connection, 1);
        }

        $this->_throwError("set stream to blocking failed: Not connected");
    }

    /**
     * Sets the socket to non-blocking operations
     *
     * @return bool
     */
    protected function setNonBlocking() {
        if($this->validateConnection()) {
            return stream_set_blocking($this->connection, 0);
        }

        $this->_throwError("set stream to non-blocking failed: Not connected");
    }

    /**
     *  Sets the timeout for this socket to an arbitrary value
     *
     * @return bool
     */
    protected function setTimeOut($seconds = 0, $milliseconds = 0) {
        if($this->validateConnection()) {
            return stream_set_timeout ($this->connection, (int)$seconds, (int)$milliseconds);
        }

        $this->_throwError("set stream timeout failed: Not connected");
    }

    /**
     * Restores the time out for this socket to the value it was opened with
     *
     * @return bool
     */
    protected function restoreTimeOut() {
        if($this->validateConnection()) {
            return stream_set_timeout ($this->connection, $this->timeOut, 0);
        }

        $this->_throwError("restore stream timeout failed: Not connected");
    }

    /**
     * Gets the meta data on this socket
     *
     * @return string
     */
    protected function getMetaData() {
        if($this->validateConnection()) {
            return stream_get_meta_data($this->connection);
        }

        $this->_throwError("get stream meta data failed: Not connected");
    }

    /**
     * Gets the socket status
     *
     * @return string
     */
    protected function getStatus() {
        if($this->validateConnection())
        {
            return socket_get_status($this->connection);
        }

        $this->_throwError("getting socket descriptor failed: Not connected");
    }

    /**
     * Validates the connection state
     *
     * @return bool
     */
    protected  function validateConnection()
    {
        return (is_resource($this->connection) && ($this->connectionState != FALSE));
    }

    /**
     * Throws an error
     *
     * @return void
     */
    private function _throwError($errorMessage)
    {
        throw new ESL_Exception("Socket {$errorMessage}");
    }
}
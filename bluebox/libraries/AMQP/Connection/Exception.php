<?php

/**
 * Description of Exception
 * 
 * Retrieved from http://code.google.com/p/php-amqplib/
 *
 * @author Vadim Zaliva <lord@crocodile.org>
 */
class AMQP_Connection_Exception extends AMQP_Exception {

    public function __construct($reply_code, $reply_text, $method_sig)
    {
        parent::__construct($reply_code, $reply_text, $method_sig);
    }
    
}
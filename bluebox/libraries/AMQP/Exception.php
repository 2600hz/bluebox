<?php

/**
 * Description of Exception
 *
 * Retrieved from http://code.google.com/p/php-amqplib/
 *
 * @author Vadim Zaliva <lord@crocodile.org>
 */
class AMQP_Exception extends Exception {

    public function __construct($reply_code, $reply_text, $method_sig)
    {
        parent::__construct(NULL,0);

        $this->amqp_reply_code = $reply_code;
        $this->amqp_reply_text = $reply_text;
        $this->amqp_method_sig = $method_sig;

        $ms=AMQP_Core::methodSig($method_sig);
        if(array_key_exists($ms, AMQP_Core::$METHOD_NAME_MAP))
            $mn = AMQP_Core::$METHOD_NAME_MAP[$ms];
        else
            $mn = "";
        $this->args = array(
            $reply_code,
            $reply_text,
            $method_sig,
            $mn
        );
    }
    
}
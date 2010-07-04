<?php

/**
 * A Message for use with the Channnel.basic_* methods.
 *
 * Retrieved from http://code.google.com/p/php-amqplib/
 *
 * @author Vadim Zaliva <lord@crocodile.org>
 */
class AMQP_Message extends AMQP_Generic_Content
{
    protected static $PROPERTIES = array(
        "content_type" => "shortstr",
        "content_encoding" => "shortstr",
        "application_headers" => "table",
        "delivery_mode" => "octet",
        "priority" => "octet",
        "correlation_id" => "shortstr",
        "reply_to" => "shortstr",
        "expiration" => "shortstr",
        "message_id" => "shortstr",
        "timestamp" => "timestamp",
        "type" => "shortstr",
        "user_id" => "shortstr",
        "app_id" => "shortstr",
        "cluster_id" => "shortst"
    );

    public function __construct($body = '', $properties = null)
    {
        $this->body = $body;

        parent::__construct($properties, $prop_types=AMQP_Message::$PROPERTIES);
    }
}
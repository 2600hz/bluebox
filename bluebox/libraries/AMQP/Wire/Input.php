<?php

/**
 * Description of Input
 *
 * Retrieved from http://code.google.com/p/php-amqplib/
 *
 * @author Vadim Zaliva <lord@crocodile.org>
 */
class AMQP_Wire_Input {
    public function __construct($sock)
    {
        $this->block_size = 8192;

        $this->sock = $sock;
        $this->reset("");

    }

    public function read($n)
    {
        if ($this->offset >= strlen($this->buffer))
        {
            if (!($rv = $this->populate_buffer()))
            {
                return $rv;
            }
        }
        return $this->read_buffer($n);
    }

    public function close()
    {
        fclose($this->sock);
        $this->reset("");
    }

    private function read_buffer($n)
    {
        $n = min($n, strlen($this->buffer) - $this->offset);
        if ($n === 0)
        {
            // substr("", 0, 0) => FALSE, which screws up read loops that are
            // expecting non-blocking reads to return "". This avoids that edge
            // case when the buffer is empty/used up.
            return "";
        }
        $block = substr($this->buffer, $this->offset, $n);
        $this->offset += $n;
        return $block;
    }

    private function reset($block)
    {
        $this->buffer = $block;
        $this->offset = 0;
    }

    private function populate_buffer()
    {
        while(feof($this->sock))
        {
            $this->reset("");
            return FALSE;
        }
        
        $block = fread($this->sock, $this->block_size);           
        if ($block !== FALSE)
        {
            $this->reset($block);
            return TRUE;
        } else
        {
            return $block;
        }
    }
}

<?php

/**
 * Description of Writer
 *
 * Retrieved from http://code.google.com/p/php-amqplib/
 *
 * @author Vadim Zaliva <lord@crocodile.org>
 */
class AMQP_Writer {
    public function __construct()
    {
        $this->out = "";
        $this->bits = array();
        $this->bitcount = 0;
    }

    private static function chrbytesplit($x, $bytes)
    {
        return array_map('chr', AMQP_Writer::bytesplit($x,$bytes));
    }

    /**
     * Splits number (could be either int or string) into array of byte
     * values (represented as integers) in big-endian byte order.
     */
    private static function bytesplit($x, $bytes)
    {
        if(is_int($x))
        {
            if($x<0)
                $x = sprintf("%u", $x);
        }

        $res = array();
        for($i=0;$i<$bytes;$i++)
        {
            $b = bcmod($x,'256');
            array_unshift($res,(int)$b);
            $x=bcdiv($x,'256', 0);
        }
        if($x!=0)
            throw new Exception("Value too big!");
        return $res;
    }

    private function flushbits()
    {
        if(count($this->bits))
        {
            $this->out .= implode("", array_map('chr',$this->bits));
            $this->bits = array();
            $this->bitcount = 0;
        }
    }

    /**
     * Get what's been encoded so far.
     */
    public function getvalue()
    {
        $this->flushbits();
        return $this->out;
    }

    /**
     * Write a plain Python string, with no special encoding.
     */
    public function write($s)
    {
        $this->flushbits();
        $this->out .= $s;
    }

    /**
     * Write a boolean value.
     */
    public function write_bit($b)
    {
        if($b)
            $b = 1;
        else
            $b = 0;
        $shift = $this->bitcount % 8;
        if($shift == 0)
            $last = 0;
        else
            $last = array_pop($this->bits);

        $last |= ($b << $shift);
        array_push($this->bits, $last);

        $this->bitcount += 1;
    }

    /**
     * Write an integer as an unsigned 8-bit value.
     */
    public function write_octet($n)
    {
        if($n < 0 || $n > 255)
            throw new Exception('Octet out of range 0..255');
        $this->flushbits();
        $this->out .= chr($n);
    }

    /**
     * Write an integer as an unsigned 16-bit value.
     */
    public function write_short($n)
    {
        if($n < 0 ||  $n > 65535)
            throw new Exception('Octet out of range 0..65535');
        $this->flushbits();
        $this->out .= pack('n', $n);
    }

    /**
     * Write an integer as an unsigned 32-bit value.
     */
    public function write_long($n)
    {
        $this->flushbits();
        $this->out .= implode("", AMQP_Writer::chrbytesplit($n,4));
    }

    private function write_signed_long($n)
    {
        $this->flushbits();
        // although format spec for 'N' mentions unsigned
        // it will deal with sinned integers as well. tested.
        $this->out .= pack('N', $n);
    }

    /**
     * Write an integer as an unsigned 64-bit value.
     */
    public function write_longlong($n)
    {
        $this->flushbits();
        $this->out .= implode("", AMQP_Writer::chrbytesplit($n,8));
    }

    /**
     * Write a string up to 255 bytes long after encoding.
     * Assume UTF-8 encoding.
     */
    public function write_shortstr($s)
    {
        $this->flushbits();
        if(strlen($s) > 255)
            throw new Exception('String too long');
        $this->write_octet(strlen($s));
        $this->out .= $s;
    }


    /*
     * Write a string up to 2**32 bytes long.  Assume UTF-8 encoding.
     */
    public function write_longstr($s)
    {
        $this->flushbits();
        $this->write_long(strlen($s));
        $this->out .= $s;
    }


    /**
     * Write unix time_t value as 64 bit timestamp.
     */
   public function write_timestamp($v)
   {
       $this->write_longlong($v);
   }

   /**
    * Write PHP array, as table. Input array format: keys are strings,
    * values are (type,value) tuples.
    */
    public function write_table($d)
    {
        $this->flushbits();
        $table_data = new AMQP_Writer();
        foreach($d as $k=>$va)
        {
            list($ftype,$v) = $va;
            $table_data->write_shortstr($k);
            if($ftype=='S')
            {
                $table_data->write('S');
                $table_data->write_longstr($v);
            } else if($ftype=='I')
            {
                $table_data->write('I');
                $table_data->write_signed_long($v);
            } else if($ftype=='D')
            {
                // 'D' type values are passed AMQPDecimal instances.
                $table_data->write('D');
                $table_data->write_octet($v->e);
                $table_data->write_signed_long($v->n);
            } else if($ftype=='T')
            {
                $table_data->write('T');
                $table_data->write_timestamp($v);
            } else if($ftype='F')
            {
                $table_data->write('F');
                $table_data->write_table($v);
            }
        }
        $table_data = $table_data->getvalue();
        $this->write_long(strlen($table_data));
        $this->write($table_data);
    }
}

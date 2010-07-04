<?php

/**
 * Abstract base class for AMQP content.  Subclasses should override
 * the PROPERTIES attribute.
 * 
 * Retrieved from http://code.google.com/p/php-amqplib/
 *
 * @author Vadim Zaliva <lord@crocodile.org>
 */
class AMQP_Generic_Content {
    protected static $PROPERTIES = array(
        "dummy" => "shortstr"
    );

    public function __construct($props, $prop_types=NULL)
    {
        if($prop_types)
            $this->prop_types = $prop_types;
        else
            $this->prop_types = AMQP_Generic_Content::$PROPERTIES;
        $d = array();
        if ($props)
            $d = array_intersect_key($props, $this->prop_types);
        else
            $d = array();
        $this->properties = $d;
    }


    /**
     * Look for additional properties in the 'properties' dictionary,
     * and if present - the 'delivery_info' dictionary.
     */
    public function get($name)
    {
        if(array_key_exists($name,$this->properties))
            return $this->properties[$name];

        if(isset($this->delivery_info))
            if(array_key_exists($name,$this->delivery_info))
                return $this->delivery_info[$name];

        throw new Exception("No such property");
    }


    /**
     * Given the raw bytes containing the property-flags and
     * property-list from a content-frame-header, parse and insert
     * into a dictionary stored in this object as an attribute named
     * 'properties'.
     */
    public function load_properties($raw_bytes)
    {
        $r = new AMQP_Reader($raw_bytes);

        // Read 16-bit shorts until we get one with a low bit set to zero
        $flags = array();
        while(true)
        {
            $flag_bits = $r->read_short();
            array_push($flags, $flag_bits);
            if(($flag_bits & 1) == 0)
                break;
        }

        $shift = 0;
        $d = array();
        foreach ($this->prop_types as $key => $proptype)
        {
            if($shift == 0) {
                if(!$flags) {
                    break;
                }
                $flag_bits = array_shift($flags);
                $shift = 15;
            }
            if($flag_bits & (1 << $shift))
                $d[$key] = call_user_func(array($r,"read_".$proptype));
            $shift -= 1;
        }
        $this->properties = $d;
    }


    /**
     * serialize the 'properties' attribute (a dictionary) into the
     * raw bytes making up a set of property flags and a property
     * list, suitable for putting into a content frame header.
     */
    public function serialize_properties()
    {
        $shift = 15;
        $flag_bits = 0;
        $flags = array();
        $raw_bytes = new AMQP_Writer();
        foreach ($this->prop_types as $key => $proptype)
        {
            if(array_key_exists($key,$this->properties))
                $val = $this->properties[$key];
            else
                $val = NULL;
            if($val != NULL)
            {
                if($shift == 0)
                {
                    array_push($flags, $flag_bits);
                    $flag_bits = 0;
                    $shift = 15;
                }

                $flag_bits |= (1 << $shift);
                if($proptype != "bit")
                    call_user_func(array($raw_bytes, "write_" . $proptype),
                                   $val);
            }
            $shift -= 1;
        }
        array_push($flags, $flag_bits);
        $result = new AMQP_Writer();
        foreach($flags as $flag_bits)
            $result->write_short($flag_bits);
        $result->write($raw_bytes->getvalue());

        return $result->getvalue();
    }
}

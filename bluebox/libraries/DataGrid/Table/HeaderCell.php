<?php defined('SYSPATH') OR die('No direct access allowed.');

class DataGrid_Table_HeaderCell {
    
    private $value = NULL;

    private $attributes = array();

    public function  __construct($value = NULL, $attributes = array())
    {
        $this->value = $value;
        $this->attributes = $attributes;
    }

    public function __set($attribute, $value)
    {
        if (strtolower($attribute) == 'value')
        {
            $this->value = $value;
        } else {
            $this->attributes[$attribute] = $value;
        }
    }

    public function __get($attribute)
    {
        if (strtolower($attribute) == 'value')
        {
            return $this->value;
        }

        if (isset($this->attributes[$attribute]))
        {
            return $this->attributes[$attribute];
        }
        return NULL;
    }

    public function __isset($attribute)
    {
        if (strtolower($attribute) == 'value')
        {
            return TRUE;
        }
        return isset($this->attributes[$attribute]);
    }

    public function __unset($attribute)
    {
        if (strtolower($attribute) == 'value')
        {
            $this->value = NULL;
        } else {
            unset($this->attributes[$attribute]);
        }
    }

    public function __toString()
    {
        return '<th'.html::attributes($this->attributes) .' >' .$this->value .'</th>';
    }

    public function toString()
    {
        return (string)$this;
    }
}

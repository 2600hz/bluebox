<?php defined('SYSPATH') OR die('No direct access allowed.');

class DataGrid_Column {

    private $content = NULL;

    private $displayName = NULL;

    private $options = array();

    private $attributes = array(
        'header' => array(),
        'cell' => array(),
        'footer' => array()
    );

    public function  __construct($content, $displayName = NULL, $options = array())
    {
        $this->content = $content;
        
        $this->displayName = $displayName;

        $this->options = $options;

        return $this;
    }


    public function __set($attribute, $value)
    {
        $this->attributes['cell'][$attribute] = $value;
    }

    public function __get($attribute)
    {
        if (isset($this->attributes['cell'][$attribute]))
        {
            return $this->attributes['cell'][$attribute];
        }
        return NULL;
    }

    public function __isset($attribute)
    {
        return isset($this->attributes['cell'][$attribute]);
    }

    public function __unset($attribute)
    {
        unset($this->attributes['cell'][$attribute]);
    }

    public function getContent()
    {
        return $this->content;
    }

    public function getDisplayName()
    {
        return $this->displayName;
    }

    public function setAttribute($for, $attribute, $value)
    {
        switch (strtolower($for))
        {
            case 'header':
            case 'cell':
            case 'footer':
                $this->attributes[$for][$attribute] = $value;
                break;
            default:
                throw new Exception('Unknown attribute owner');
        }
    }

    public function getAttribute($for, $attribute)
    {
        switch (strtolower($for))
        {
            case 'header':
            case 'cell':
            case 'footer':
                return $this->attributes[$for][$attribute];
            default:
                throw new Exception('Unknown attribute owner');
        }
    }

    public function getAttributes($for)
    {
        switch (strtolower($for))
        {
            case 'header':
            case 'cell':
            case 'footer':
                return $this->attributes[$for];
            default:
                throw new Exception('Unknown attribute owner');
        }
    }
}

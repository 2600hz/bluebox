<?php defined('SYSPATH') OR die('No direct access allowed.');

class DataGrid_Table_Head {

    private $elements = array();

    private $attributes = array();

    public function  __construct($attributes = array())
    {
        $this->attributes = $attributes;
    }

    public function __set($attribute, $value)
    {
        $this->attributes[$attribute] = $value;
    }

    public function __get($attribute)
    {
        switch (strtolower($attribute)) {
            case 'rows':
                return $this->elements;
            default:
                if (isset($this->attributes[$attribute]))
                {
                    return $this->attributes[$attribute];
                }
                return NULL;
        }
    }

    public function __isset($attribute)
    {
        switch (strtolower($attribute)) {
            case 'rows':
                return isset($this->elements);
            default:
                return isset($this->attributes[$attribute]);
        }
    }

    public function __unset($attribute)
    {
        switch (strtolower($attribute)) {
            case 'rows':
                $this->elements = array();
                break;
            default:
                unset($this->attributes[$attribute]);
        }
    }

    public function __toString()
    {
        $html = '<thead'.html::attributes($this->attributes).' >';

        foreach ($this->elements as $element) {
            $html .= $element->toString();
        }
        
        return $html .'</thead>';
    }

    public function row($attributes = array(), $isTemplate = FALSE)
    {
        return $this->elements[] =
                new DataGrid_Table_Row($attributes, $isTemplate);
    }

    public function toString()
    {
        return (string)$this;
    }

    public function getTemplate()
    {
        $template = '<thead'.html::attributes($this->attributes).' >';

        foreach ($this->elements as $element) {
            if ($element->isTemplate()) {
                $template .=  '{{#head}}' .$element->toString() .'{{/head}}';
            } else {
                $template .= $element->toString();
            }
        }

        return $template .'</thead>';
    }

    public function getPartialsTemplate()
    {
        $template = '<thead'.html::attributes($this->attributes).' >';

        foreach ($this->elements as $element) {
            if ($element->isTemplate()) {
                $template .=  '{{#head}}{{>head}}{{/head}}';
            } else {
                $template .= $element->toString();
            }
        }

        return $template .'</thead>';
    }

    public function getPartialTemplate()
    {
        $template = '';

        foreach ($this->elements as $element) {
            $template .= $element->toString();
        }

        return $template;
    }
}

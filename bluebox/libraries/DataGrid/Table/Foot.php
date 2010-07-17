<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * @package    Core/Libraries/DataGrid
 * @author     K Anderson <bitbashing@gmail.com>
 * @license    Mozilla Public License (MPL)
 */
class DataGrid_Table_Foot
{
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
        switch (strtolower($attribute))
        {
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
        switch (strtolower($attribute))
        {
            case 'rows':
                return isset($this->elements);

            default:
                return isset($this->attributes[$attribute]);
        }
    }

    public function __unset($attribute)
    {
        switch (strtolower($attribute))
        {
            case 'rows':
                $this->elements = array();

                break;
            
            default:
                unset($this->attributes[$attribute]);
        }
    }

    public function __toString()
    {
        $html = '<tfoot'.html::attributes($this->attributes).' >';

        foreach ($this->elements as $element)
        {
            $html .= $element->toString();
        }
        
        return $html .'</tfoot>';
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
        $template = '<tfoot'.html::attributes($this->attributes).' >';

        foreach ($this->elements as $element)
        {
            if ($element->isTemplate())
            {
                $template .=  '{{#foot}}' .$element->toString() .'{{/foot}}';
            } 
            else
            {
                $template .= $element->toString();
            }
        }
        
        return $template .'</tfoot>';
    }

    public function getPartialsTemplate()
    {
        $template = '<tfoot'.html::attributes($this->attributes).' >';

        foreach ($this->elements as $element)
        {
            if ($element->isTemplate())
            {
                $template .=  '{{#foot}}{{>foot}}{{/foot}}';
            } 
            else
            {
                $template .= $element->toString();
            }
        }

        return $template .'</tfoot>';
    }

    public function getPartialTemplate()
    {
        $template = '';

        foreach ($this->elements as $element)
        {
            $template .= $element->toString();
        }

        return $template;
    }
}
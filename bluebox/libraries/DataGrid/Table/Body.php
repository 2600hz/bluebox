<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * @package    Core/Libraries/DataGrid
 * @author     K Anderson <bitbashing@gmail.com>
 * @license    Mozilla Public License (MPL)
 */
class DataGrid_Table_Body
{
    private $elements = array();

    private $attributes = array();

    private $preRendered = NULL;

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
        $html = '<tbody'.html::attributes($this->attributes).' >';

        if (!is_null($this->preRendered))
        {
            $html .= $this->preRendered;
        } 
        else
        {
            foreach ($this->elements as $element)
            {
                $html .= $element->toString();
            }
        }
        
        return $html .'</tbody>';
    }

    public function row($attributes = array(), $isTemplate = FALSE)
    {
        return $this->elements[] =
                new DataGrid_Table_Row($attributes, $isTemplate);
    }

    public function getRow($number)
    {
        if (isset($this->elements[$number]))
        {
            return $this->elements[$number];
        }
        
        return FALSE;
    }

    public function getRows()
    {
        return $this->elements;
    }

    public function setRendered($preRendered)
    {
        $this->preRendered = $preRendered;
    }

    public function toString()
    {
        return (string)$this;
    }

    public function getTemplate()
    {
        $template  = '<tbody' .html::attributes($this->attributes) .' >';

        foreach ($this->elements as $element) {
            if ($element->isTemplate()) {
                $template .=  '{{#body}}' .$element->toString() .'{{/body}}';
            } else {
                $template .= $element->toString();
            }            
        }

        return $template .'</tbody>';
    }

    public function getPartialsTemplate()
    {
        $template = '<tbody'.html::attributes($this->attributes).' >';

        foreach ($this->elements as $element)
        {
            if ($element->isTemplate())
            {
                $template .=  '{{#body}}{{>body}}{{/body}}';
            } 
            else
            {
                $template .= $element->toString();
            }
        }

        return $template .'</tbody>';
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
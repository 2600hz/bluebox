<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * @package    Core/Libraries/DataGrid
 * @author     K Anderson <bitbashing@gmail.com>
 * @license    Mozilla Public License (MPL)
 */
class DataGrid_Table_Row
{
    private $elements = array();

    private $attributes = array();

    private $isTemplate = FALSE;

    public function  __construct($attributes = array(), $isTemplate = FALSE)
    {
        $this->attributes = $attributes;

        $this->isTemplate = $isTemplate;
    }
    
    public function __get($attribute)
    {
        switch (strtolower($attribute))
        {
            case 'cells':
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
            case 'cells':
                return isset($this->elements);
            
            default:
                return isset($this->attributes[$attribute]);
        }
    }

    public function __unset($attribute)
    {
        switch (strtolower($attribute))
        {
            case 'cells':
                $this->elements = array();

                break;

            default:
                unset($this->attributes[$attribute]);
        }
    }

    public function __toString()
    {
        $html = '<tr' .html::attributes($this->attributes) .' >';

        foreach ($this->elements as $element)
        {
            $html .= $element->toString();
        }

        return $html .'</tr>';
    }

    public function headerCell($value = NULL, $attributes = array())
    {
        return $this->elements[] =
                new DataGrid_Table_HeaderCell($value, $attributes);
    }

    public function dataCell($value = NULL, $attributes = array())
    {
        $this->elements[] =
                new DataGrid_Table_DataCell($value, $attributes);
    }

    public function isTemplate()
    {
        return $this->isTemplate;
    }

    public function toString()
    {
        return (string)$this;
    }
}
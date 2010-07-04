<?php defined('SYSPATH') OR die('No direct access allowed.');

class DataGrid_Table {

    private $elements = array();

    private $attributes = array();

    public function  __construct($attributes = array())
    {
        $this->attributes = $attributes;
        
        return $this;
    }

    public function __set($attribute, $value)
    {
        $this->attributes[$attribute] = $value;
    }

    public function __get($attribute)
    {
        switch(strtolower($attribute)) {
            case 'head':
                return $this->head();
            case 'body':
                return $this->body();
            case 'foot':
                return $this->foot();
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
        switch(strtolower($attribute)) {
            case 'head':
                return isset($this->elements['head']);
            case 'body':
                return isset($this->elements['body']);
            case 'foot':
                return isset($this->elements['foot']);
            default:
                return isset($this->attributes[$attribute]);
        }
    }

    public function __unset($attribute)
    {
        switch(strtolower($attribute)) {
            case 'head':
                unset($this->elements['head']);
                break;
            case 'body':
                unset($this->elements['body']);
                break;
            case 'foot':
                unset($this->elements['foot']);
                break;
            default:
                unset($this->attributes[$attribute]);
        }
    }
    
    public function __toString()
    {
        $html = '<table'.html::attributes($this->attributes).' >';

        foreach ($this->elements as $element) {
            $html .= $element->toString();
        }

        return $html .'</table>';
    }

    public function head($isTemplate = FALSE, $attributes = array())
    {
        if (empty($this->elements['head']))
        {
            return $this->elements['head'] = new DataGrid_Table_Head($isTemplate, $attributes);
        } else {
            return $this->elements['head'];
        }
    }

    public function foot($isTemplate = FALSE, $attributes = array())
    {
        if (empty($this->elements['foot']))
        {
            return $this->elements['foot'] = new DataGrid_Table_Foot($isTemplate, $attributes);
        } else {
            return $this->elements['foot'];
        }
    }

    public function body($isTemplate = FALSE, $attributes = array())
    {
        if (empty($this->elements['body']))
        {
            return $this->elements['body'] = new DataGrid_Table_Body($isTemplate, $attributes);
        } else {
            return $this->elements['body'];
        }
    }

    public function squareTable()
    {
        $forceTo = 0;

        foreach($this->elements as $element) {

            foreach ($element->rows as $row) {

                if (count($row->cells) > $forceTo) {
                    $forceTo = count($row->cells);
                }
                
            }
        }

        foreach($this->elements as $element) {

            foreach ($element->rows as $row) {

                if (count($row->cells) < $forceTo) {
                    $lastCell = $row->cells[count($row->cells) - 1];
                    $lastCell->colspan = $forceTo - count($row->cells) + 1;
                }

            }
        }
    }

    public function toString()
    {
        return (string)$this;
    }

    public function getPartialsTemplate() {
        $template  = '<table'.html::attributes($this->attributes).' >';

        foreach ($this->elements as $element) {
            $template .= $element->getPartialsTemplate();
        }

        return $template .'</table>';
    }

    public function getTemplate()
    {
        $template = '<table'.html::attributes($this->attributes).' >';

        foreach ($this->elements as $element) {
            $template .= $element->getTemplate();
        }

        return $template .'</table>';
    }
}



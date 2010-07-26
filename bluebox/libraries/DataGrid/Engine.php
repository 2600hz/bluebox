<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * @package    Core/Libraries/DataGrid
 * @author     K Anderson <bitbashing@gmail.com>
 * @license    Mozilla Public License (MPL)
 */
class DataGrid_Engine
{
    private $columns = array();

    private $name = NULL;

    private $attributes = array();

    private $table = NULL;

    private $fields = array();

    private $driver = NULL;

    public function __construct($name, $attributes = array())
    {
        $name = trim(preg_replace('/[^a-zA-Z0-9_]+/mx', '_', $name), '_');
        
        $this->name = $name;

        if (!isset($attributes['id']))
        {
            $attributes['id'] = $name;
        }

        $this->table = new DataGrid_Table($attributes);
        
        return $this;
    }

    public function __set($variable, $value)
    {
        switch (strtolower($variable)) {
            case 'columns':
                return $this->addColumn($value);
            case 'name':
                return $this->name = $value;
            case 'attributes':
                return $this->attributes = $value;
            case 'table':
                return $this->table = $value;
            case 'fields':
                return $this->fields = $value;
            case 'driver':
                return $this->driver = $value;
            default:
                return $this->attributes[$attribute] = $value;
        }
    }

    public function __get($variable)
    {
        switch (strtolower($variable))
        {
            case 'columns':
                return $this->columns;

            case 'name':
                return $this->name;

            case 'attributes':
                return $this->attributes;

            case 'table':
                return $this->table;

            case 'fields':
                return $this->fields;

            case 'driver':
                return $this->driver;
            
            default:
                if (isset($this->attributes[$variable]))
                {
                    return $this->attributes[$variable];
                }
                
                return NULL;
        }
    }

    public function __isset($variable)
    {
        switch (strtolower($variable))
        {
            case 'columns':
                return isset($this->columns);

            case 'name':
                return isset($this->name);

            case 'attributes':
                return isset($this->attributes);

            case 'table':
                return isset($this->table);

            case 'fields':
                return isset($this->fields);

            case 'driver':
                return isset($this->driver);
            
            default:
                return isset($this->attributes[$attribute]);
        }
    }

    public function __unset($variable)
    {
        switch (strtolower($variable))
        {
            case 'columns':
                $this->columns = array();

                break;

            case 'attributes':
                $this->attributes = array();

                break;

            case 'fields':
                $this->fields = array();

                break;

            case 'driver':
                $this->driver = NULL;

                break;

            default:
                unset($this->attributes[$attribute]);
        }
    }

    public function addAction($uri, $title, $displayName = NULL, $options = array())
    {

    }

    public function addCallback($callback, $displayName = NULL, $options = array())
    {

    }

    public function addAnchor($uri, $title, $displayName = NULL, $options = array())
    {

    }

    public function addColumn($content, $displayName = NULL, $options = array())
    {
        $fields = array();
        
        preg_match_all('/{{(.*?)}}/', $content, $fields);

        if (!empty($fields[1]))
        {
            foreach($fields[1] as $field)
            {
                $this->fields[$field] = $field;   
            }
        }
        
        return $this->columns[] =
                new DataGrid_Column($content, $displayName, $options);
    }

    public function addField($field, $displayName = NULL, $options = array(), $number = NULL)
    {
        $this->fields[$field] = $field;

        return $this->columns[] =
                new DataGrid_Column('{{' .$field .'}}', $displayName, $options);
    }

    public function render($type = 'table', $data = array())
    {
        switch (strtolower($type))
        {
            case 'table':
                return $this->renderTable($data);

                break;

            case 'skeleton':
                return $this->renderSkeleton($data);

            case 'template':
                return $this->renderTemplate($data);

            case 'partials':
                return $this->renderPartialsTemplate($data);

            case 'json':
                break;

            case 'xml':
                break;

            default:
                throw new Exception('Unknown render type');
        }
    }

    private function renderTemplate() 
    {
        if (!($this->table instanceof DataGrid_Table))
        {
            return NULL;
        }

        $header = $this->table->head()->row(array(), TRUE);

        $body = $this->table->body()->row(array(), TRUE);

        $foot = $this->table->foot()->row(array(), TRUE);

        foreach ($this->columns as $column)
        {
            $header->headerCell($column->getContent(), $column->getAttributes('header'));

            $body->dataCell($column->getContent(), $column->getAttributes('cell'));

            $foot->dataCell($column->getContent(), $column->getAttributes('footer'));
        }

        $this->table->squareTable();

        return  $this->table->getTemplate();
    }

    private function renderPartialsTemplate()
    {
        if (!($this->table instanceof DataGrid_Table))
        {
            return NULL;
        }

        $header = $this->table->head->row(array(), TRUE);

        $body = $this->table->body->row(array(), TRUE);

        $foot = $this->table->foot->row(array(), TRUE);

        foreach ($this->columns as $column)
        {
            $header->headerCell($column->getContent(), $column->getAttributes('header'));

            $body->dataCell($column->getContent(), $column->getAttributes('cell'));

            $foot->dataCell($column->getContent(), $column->getAttributes('footer'));
        }

        $template = array(
            'template' => $this->table->getPartialsTemplate(),
            'partials' => array(
                'head' => $this->table->head->getPartialTemplate(),
                'body' => $this->table->body->getPartialTemplate(),
                'foot' => $this->table->foot->getPartialTemplate()
            )
        );
        
        $this->table->squareTable();
        
        return $template;
    }

    private function renderTable($data = array())
    {
        if (!($this->table instanceof DataGrid_Table))
        {
            return NULL;
        }

        if (!isset($data['body']))
        {
            if (isset($data['head']) or isset($data['foot']))
            {
                $data['body'] = array();
            } 
            else
            {
                $data['body'] = $data;   
            }
        }

        $body = $this->table->body->row(array(), TRUE);

        foreach($this->columns as $column)
        {
            $columnContent = $column->getContent();

            $columnDisplayName = $column->getDisplayName();

            if (!empty($columnDisplayName))
            {
                if (!isset($header))
                {
                    $header = $this->table->head->row();
                }

                $header->headerCell($columnDisplayName, $column->getAttributes('header'));
            }

            $body->dataCell($columnContent, $column->getAttributes('cell'));
        }

        $this->table->squareTable();

        $renderedBody = '';

        $bodyRowTemplate = $body->toString();

        $fields = $fieldOrder = array();

        preg_match_all('/{{(.*?)}}/', $bodyRowTemplate, $fields);

        if (count($fields[1]) > 0)
        {
            $fieldOrder = $fields[0];
            
            $fields = $fields[1];
        } 
        else
        {
            $fields = array();   
        }

        foreach ($data['body'] as $rowData)
        {
            $search = $replace = array();

            foreach($fields as $field)
            {
                $search[] = '{{' .$field .'}}';

                if (isset($rowData[$field]))
                {
                    $replace[] = $rowData[$field];
                }
                else
                {
                    $replace[] = '&nbsp;';   
                }
            }
            
            $renderedBody .= str_replace($search, $replace, $bodyRowTemplate);
        }

        $this->table->body->setRendered($renderedBody);

        return $this->table->toString();
    }

    private function renderSkeleton($data = array())
    {
        if (!($this->table instanceof DataGrid_Table))
        {
            return NULL;
        }

        if (!isset($data['body']))
        {
            if (isset($data['head']) or isset($data['foot']))
            {
                $data['body'] = array();
            } 
            else
            {
                $data['body'] = $data;
            }
        }

        $body = $this->table->body->row();

        foreach ($this->columns as $column)
        {
            $columnContent = $column->getContent();

            $columnDisplayName = $column->getDisplayName();
            
            if (!empty($columnDisplayName))
            {
                if (!isset($header))
                {
                    $header = $this->table->head->row();
                }

                $header->headerCell($columnDisplayName, $column->getAttributes('header'));
            }

            $body->dataCell('&nbsp;', $column->getAttributes('cell'));
        }

        $this->table->squareTable();

        return $this->table->toString();
    }
}
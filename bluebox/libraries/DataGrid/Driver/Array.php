<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * @package    Core/Libraries/DataGrid
 * @author     K Anderson <bitbashing@gmail.com>
 * @license    Mozilla Public License (MPL)
 */
class DataGrid_Driver_Array implements Iterator
{    
    private $data = array();

    public function  __construct()
    {
        return $this;
    }

    public function load($data)
    {
        if (!is_array($data))
        {
            throw new Exception('Invalid data format, must be an array');
        }

        $this->data = $data;
        
        return $this;
    }

    public function loadRow($date, $rowNumber = NULL)
    {
        if (!is_array($data))
        {
            throw new Exception('Invalid data format, must be an array');
        }
        
        if (is_null($rowNumber))
        {
            $this->data[] = $data;
        } 
        else
        {
            $this->data[$rowNumber] = $data;
        }

        return $this;
    }

    public function get($dataSet, $field)
    {
        if (isset($dataSet[$field]))
        {
            return $dataSet[$field];
        }
        
        return '&nbsp';
    }

    public function rewind()
    {
        reset($this->data);
    }

    public function current()
    {
        return current($this->data);
    }

    public function key()
    {
        return key($this->data);
    }

    public function next()
    {
        next($this->data);
    }

    public function valid()
    {
        return isset($this->data[$this->key()]);
    }
}
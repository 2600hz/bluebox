<?php defined('SYSPATH') OR die('No direct access allowed.');

class DataGrid_Factory {

    public static function create($name)
    {
        return new DataGrid_Engine($name);
    }    
}


/**
 *
 * $grid = DataGrid_Factory::create();
 *
 * $column = $grid->column('test', 'Test');
 * $column->sortable = FALSE;
 *
 *
 *
 *
 */
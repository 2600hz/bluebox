<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * @package    Core/Libraries/DataGrid
 * @author     K Anderson <bitbashing@gmail.com>
 * @license    Mozilla Public License (MPL)
 */
class DataGrid_Factory
{
    public static function create($name)
    {
        return new DataGrid_Engine($name);
    }    
}
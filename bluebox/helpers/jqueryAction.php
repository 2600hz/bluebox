<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * @package    Core/Helpers/JgridAction
 * @author     Anton Shevchuk
 * @license    Mozilla Public License (MPL)
 */
class jqueryAction
{
    /**
     * add param to list
     * 
     * @param  string $param
     * @param  string $value
     * @return jQuery_Action
     */
    public function add($param, $value)
    {
        $this->$param = $value;
        return $this;
    }
}
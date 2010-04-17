<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * Class jqueryAction
 *
 * Abstract class for any parameter of any action
 * retrieved from http://jquery.hohli.com/ by K Anderson on 06 05 2009
 *
 * @author Anton Shevchuk
 * @license LGPL
 * @access   public
 * @package  jQuery
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
<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * @package    Core/Helpers/JgridElement
 * @author     Anton Shevchuk
 * @license    Mozilla Public License (MPL)
 */
class jqueryElement
{
    /**
     * selector path
     * @var string
     */
    public $s;
    
    /**
     * methods
     * @var array
     */
    public $m = array();
    
    /**
     * args
     * @var array
     */
    public $a = array();
    
    /**
     * __construct
     * contructor of jquery
     *
     * @return jqueryElement
     */
    public function __construct($selector)
    {
        jquery::addElement($this); 
        $this->s = $selector;
    }
    
    /**
     * __call
     *
     * @return jqueryElement
     */
    public function __call($method, $args)
    {
        array_push($this->m, $method);
        array_push($this->a, $args);
        
        return $this;
    }
    
    /**
     * end
     * need to create new jquery
     *
     * @return jqueryElement
     */
    public function end()
    {
        return new jqueryElement($this->s);
    }
}
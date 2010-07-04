<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * jqueryElement - class for work with jquery framework
 * retrieved from http://jquery.hohli.com/ by K Anderson on 06 05 2009
 *
 * @author Anton Shevchuk
 * @license LGPL
 * @access   public
 * @package  jquery
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
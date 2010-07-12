<?php defined('SYSPATH') or die('No direct access allowed.');

class Package_Dependency_Exception extends Package_Exception
{
    protected $failures = array();
    
    public function loadFailures($failures)
    {
        $this->failures = $failures;
    }
}
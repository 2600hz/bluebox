<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * @package    Core/Libraries/Package
 * @author     K Anderson <bitbashing@gmail.com>
 * @license    Mozilla Public License (MPL)
 */
class Package_Dependency_Exception extends Package_Exception
{
    protected $failures = array();
    
    public function loadFailures($failures)
    {
        $this->failures = $failures;
    }
}
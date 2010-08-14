<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * @package    Core/Libraries/Package
 * @author     K Anderson <bitbashing@gmail.com>
 * @license    Mozilla Public License (MPL)
 */
class Package_Import_Exception extends Package_Exception
{
    public function __construct($message = null, $code = 0, Exception $previous = null)
    {
        if ($code == 0)
        {
            Package_Import_Message::set($message, 'error', 'import');
        }
        else
        {
            Package_Import_Message::set($message, 'error', $code);
        }

        parent::__construct($message, $code);
    }
}
<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * @package    Core/Libraries/Package
 * @author     K Anderson <bitbashing@gmail.com>
 * @license    Mozilla Public License (MPL)
 */
class Package_Transaction_Exception extends Package_Exception
{
    public function __construct($message = null, $code = 0, Exception $previous = null)
    {
        if ($code == 0)
        {
            Package_Operation_Message::set($message, 'error', 'transaction');
        }
        else
        {
            Package_Operation_Message::set($message, 'error', $code);
        }

        parent::__construct($message, $code, $previous);
    }
}
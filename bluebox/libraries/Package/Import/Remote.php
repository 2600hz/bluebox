<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * @package    Core/Libraries/Package
 * @author     K Anderson <bitbashing@gmail.com>
 * @license    Mozilla Public License (MPL)
 */
class Package_Import_Remote
{
    public static function fetch($URL, $overwrite = TRUE)
    {
        $filename = basename($URL);

        if (empty($filename))
        {
            throw new Package_Import_Exception('Could not determine the filename for ' .$URL);
        }

        $filepath = APPPATH .'cache/' .$filename;

        if (file_exists($filepath))
        {
            if ($overwrite)
            {
                kohana::log('debug', 'Removing existing archive ' .$filepath);

                unlink($filepath);
            }
            else
            {
                return $filepath;
            }
        }

        kohana::log('debug', 'Retrieving package archive from ' .$URL);

        if (!@file_put_contents($filepath, file_get_contents($URL)))
        {
            throw new Package_Import_Exception('Unable to fetch ' .$URL);
        }

        return $filepath;
    }
}
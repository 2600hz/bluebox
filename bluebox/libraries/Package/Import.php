<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * @package    Core/Libraries/Package
 * @author     K Anderson <bitbashing@gmail.com>
 * @license    Mozilla Public License (MPL)
 */
class Package_Import
{
    public static function core()
    {
        // This is the dangerous one...
    }

    public static function package($path)
    {
        kohana::log('debug', 'Attempting to import package ' .$path);

        $filename = basename($path);
        
        if (self::is_url($path))
        {
            $path = Package_Import_Remote::fetch($path);
        }

        if (!class_exists('ZipArchive'))
        {
            throw new Package_Import_Exception('System does not have zip archive support');
        }

        $zip = new ZipArchive;

        if(!($error = $zip->open($path, ZIPARCHIVE::CHECKCONS)))
        {
            switch ($error)
            {
                case ZIPARCHIVE::ER_EXISTS:
                    throw new Package_Import_Exception('Package archive already exists', $error);

                case ZIPARCHIVE::ER_INCONS:
                    throw new Package_Import_Exception('Consistency check on the package archive failed', $error);

                case ZIPARCHIVE::ER_INVAL:
                    throw new Package_Import_Exception('Invalid argument while opening package archive', $error);

                case ZIPARCHIVE::ER_MEMORY:
                    throw new Package_Import_Exception('Memory allocation failure while opening package archive', $error);

                case ZIPARCHIVE::ER_NOENT:
                    throw new Package_Import_Exception('Could not locate package archive', $error);

                case ZIPARCHIVE::ER_NOZIP:
                    throw new Package_Import_Exception('Package archive is not a zip', $error);

                case ZIPARCHIVE::ER_OPEN:
                    throw new Package_Import_Exception('Cant open package archive', $error);

                case ZIPARCHIVE::ER_READ:
                    throw new Package_Import_Exception('Package archive read error', $error);

                case ZIPARCHIVE::ER_SEEK:
                    throw new Package_Import_Exception('Package archive seek error', $error);

                default:
                    throw new Package_Import_Exception('Unknown error while opening package archive', $error);
            }
        }

        $importPath = MODPATH .pathinfo($path, PATHINFO_FILENAME);

        if (is_dir($importPath))
        {
            throw new Package_Import_Exception('Import path `' .$importPath .'` already exists');
        }

        kohana::log('debug', 'Extracting package archive to ' .$importPath);

        if (!@$zip->extractTo($importPath))
        {
            throw new Package_Import_Exception('Failed to extract package archive ' .$filename);
        }
        
        $zip->close();

        Package_Catalog::buildCatalog();

        return $importPath;
    }

    protected static function is_url($url)
    {
        $schema = @parse_url($url, PHP_URL_SCHEME);

        switch($schema)
        {
            case 'http':
                return TRUE;
            case 'https':
                return TRUE;
            case 'ftp':
                return TRUE;
            default:
                return FALSE;
        }
    }
}
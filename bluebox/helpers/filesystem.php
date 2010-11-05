<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * @package    Core/Helpers/Filesystem
 * @author     K Anderson <bitbashing@gmail.com>
 * @license    Mozilla Public License (MPL)
 */
class filesystem
{
    /**
     * Recursively create a directory
     * Retrieved from http://us2.php.net/function.mkdir on July 18, 2009 by K Anderson
     *
     * @author bat at flurf dot net
     * @param string $dir the full directory to create
     * @param int $mode the mode to use during creation
     * @return bool true on success, otherwise false
     */
    public static function createDirectory($dir, $mode = 0755)
    {
        if (is_dir($dir) || @mkdir($dir, $mode, TRUE))
        {
            return TRUE;
        }

        if (!self::createDirectory(dirname($dir) , $mode))
        {
            return FALSE;
        }
        
        return @mkdir($dir, $mode, TRUE);
    }

    /**
     * Copy a file, or recursively copy a folder and its contents
     *
     * @author      Aidan Lister <aidan@php.net> modified by K Anderson
     * @version     1.0.1
     * @link        http://aidanlister.com/repos/v/function.copyr.php
     * @param       string   $source    Source path
     * @param       string   $dest      Destination path
     * @return      bool     Returns TRUE on success, FALSE on failure
     */
    public static function copy($source, $dest, $mode = 0755, $flags = array(), $ignore = array())
    {
        if (is_link($dest) || is_file($dest))
        {
            if (!empty($flags['overwrite']))
            {
                kohana::log('debug', 'Instructed not to overwrite ' . basename($dest));

                return NULL;
            }

            if (!empty($flags['update']))
            {
                if (filemtime($dest) > filemtime($source))
                {
                    kohana::log('debug', 'Existing copy of ' . basename($dest) . ' is newer');

                    return NULL;
                }
                
                kohana::log('debug', 'Found ' . $source . ' to be a newer copy of ' . $dest);
            }
        }

        // Check for symlinks
        if (is_link($source))
        {
            kohana::log('debug', 'Creating symlink of ' . basename($dest));
            
            return symlink(readlink($source) , $dest);
        }

        // Simple copy for a file
        if (is_file($source))
        {
            kohana::log('debug', 'Copying ' . basename($dest));
            
            return copy($source, $dest);
        }

        // Make destination directory
        if (!is_dir($dest))
        {
            self::createDirectory($dest, $mode);
        }

        // Loop through the folder
        $dir = @dir($source);

        if (!$dir)
        {
            kohana::log('error', 'Unable to read from ' . $source);

            return FALSE;
        }

        while (false !== $entry = $dir->read())
        {
            // Skip pointers
            if ($entry == '.' || $entry == '..' || in_array($entry, $ignore))
            {
                continue;
            }

            // Deep copy directories
            self::copy($source .DIRECTORY_SEPARATOR .$entry, $dest .DIRECTORY_SEPARATOR .$entry, $mode, $flags);
        }

        // Clean up
        $dir->close();
        
        return true;
    }

    /**
     * This function takes a guess as to if a file is a binary file
     * (jpeg, mp3, exe, ect).  It should not be relied upon...
     *
     * @param string $file
     * @param string $blk Used by the internal function during recursion
     * @return bool
     */
    public static function is_binary($file, $blk = NULL)
    {
        if (!is_null($blk))
        {
            $blk = substr($blk, 0, 512);
        }
        else
        {
            if (!file_exists($file))
            {
                return 0;
            }

            if (!is_file($file))
            {
                return 0;
            }

            $fh = fopen($file, "r");

            $blk = fread($fh, 512);

            fclose($fh);

            clearstatcache();
        }

        if (substr_count($blk, "\x00") > 0)
        {
            return TRUE;
        }

        $blk = preg_replace('/[^\x00-\x08\x0B\x0E-\x1F]*/','', $blk);

        if (strlen($blk) > 2)
        {
            return TRUE;
        }
        
        return FALSE;
    }

    /**
     * Recursive delete
     *
     * @param string $dir
     * @param bool $removeParent
     * @return bool
     */
    public static function delete($dir, $removeParent = TRUE)
    {
        if(is_file($dir))
        {
            return @unlink($dir);
        }

        if(!$dh = @opendir($dir))
        {
            return;
        }

        while (($obj = readdir($dh)))
        {
            if($obj=='.' || $obj=='..')
            {
                continue;
            }

            try
            {
                if (!@unlink($dir.'/'.$obj))
                {
                    self::delete($dir.'/'.$obj, true);
                }
            } 
            catch (Exception $e)
            {
                self::delete($dir.'/'.$obj, true);
            }
        }

        if ($removeParent)
        {
            closedir($dh);

            @rmdir($dir);
        }
    }

    /**
     * This function is used to test if a directory is writable
     * Retrieved from http://us.php.net/manual/en/function.is-writable.php on 6/18/2009
     * will work in despite of Windows ACLs bug NOTE: use a trailing slash for folders!!!
     *
     * @return bool true if directory or file is writable otherwise false
     * @param string $path
     */
    public static function is_writable($path)
    {
        if ($path{strlen($path) - 1} == DIRECTORY_SEPARATOR)
        {
            return self::is_writable($path . uniqid(mt_rand()) . '.tmp');
        }
        else if (is_dir($path))
        {
            return self::is_writable($path .DIRECTORY_SEPARATOR . uniqid(mt_rand()) . '.tmp');
        }

        // check tmp file for read/write capabilities
        $rm = file_exists($path);

        $f = @fopen($path, 'c');

        if ($f === false)
        {
            return false;
        }

        fclose($f);
        
        if (!$rm)
        {
            unlink($path);
        }
        
        return true;
    }

    /**
     * This is a helper funciton to convert windows or linux paths into the
     * appropriate system, it is very rudamentary and converts all slashs at
     * this time...
     * @param string $path
     * @param  bool $escape
     * @return string
     */
    public static function convert_path(&$path, $escape)
    {
        $path = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $path);

        if ($escape)
        {
            $path = str_replace('/', '\/', $path);
        }

        return $path;
    }
}

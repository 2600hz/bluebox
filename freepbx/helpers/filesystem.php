<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * filesystem.php - FreePbx helper for manipulating the filesystem
 *
 * @author Karl Anderson
 * @license LGPL
 * @package FreePBX3
 * @subpackage Core
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
        if (is_dir($dir) || @mkdir($dir, $mode, TRUE)) return TRUE;
        if (!self::createDirectory(dirname($dir) , $mode)) return FALSE;
        return @mkdir($dir, $mode, TRUE);
    }
    /**
     * Copy a file, or recursively copy a folder and its contents
     *
     * @author      Aidan Lister <aidan@php.net>
     * @version     1.0.1
     * @link        http://aidanlister.com/repos/v/function.copyr.php
     * @param       string   $source    Source path
     * @param       string   $dest      Destination path
     * @return      bool     Returns TRUE on success, FALSE on failure
     */
    public static function copy($source, $dest, $mode = 0755, $options = array())
    {
        if (is_link($dest) || is_file($dest)) {
            if (!empty($options['overwrite'])) {
                kohana::log('debug', 'Instructed not to overwrite ' . basename($dest));
                return null;
            }
            if (!empty($options['update'])) {
                if (filemtime($dest) > filemtime($source)) {
                    kohana::log('debug', 'Existing copy of ' . basename($dest) . ' is newer');
                    return null;
                }
                kohana::log('debug', 'Found ' . $source . ' to be a newer copy of ' . $dest);
            }
        }
        // Check for symlinks
        if (is_link($source)) {
            kohana::log('debug', 'Creating symlink of ' . basename($dest));
            return symlink(readlink($source) , $dest);
        }
        // Simple copy for a file
        if (is_file($source)) {
            kohana::log('debug', 'Copying ' . basename($dest));
            return copy($source, $dest);
        }
        // Make destination directory
        if (!is_dir($dest)) {
            self::createDirectory($dest, $mode);
        }
        // Loop through the folder
        $dir = @dir($source);
        if (!$dir) {
            kohana::log('error', 'Unable to read from ' . $source);
            return FALSE;
        }
        while (false !== $entry = $dir->read()) {
            // Skip pointers
            if ($entry == '.' || $entry == '..' || $entry == '.svn') {
                continue;
            }
            // Deep copy directories
            self::copy("$source/$entry", "$dest/$entry", $mode, $options);
        }
        // Clean up
        $dir->close();
        return true;
    }
    public static function directoryToArray($directory, $recursive = TRUE, $options = array())
    {
        $options+= array(
            'filterDirectory' => NULL,
            'multidimensional' => TRUE,
            'prependDirectory' => NULL
        );
        // Create a placeholder
        $array_items = array();
        extract($options);
        // if the baseDirectory was not supplied assume the $directory is it
        if (is_null($filterDirectory)) {
            $options['filterDirectory'] = $filterDirectory = $directory;
        }
        // Open this directory
        if ($handle = opendir($directory)) {
            // iterate the contents of this handle
            while (false !== ($file = readdir($handle))) {
                // skip work against the parent and child directory
                if ($file != "." && $file != "..") {
                    // Set a var with the combined file and full dir
                    $filePath = rtrim($directory, '/') . '/' . $file;
                    if (empty($options['multidimensional'])) {
                        $key = $filePath;
                    } else {
                        $key = $file;
                    }
                    if (!empty($options['filterDirectory'])) {
                        $key = str_replace($options['filterDirectory'], '', $key);
                    }
                    if (!empty($options['prependDirectory'])) {
                        $key = rtrim($options['prependDirectory'], '/') . '/' . $key;
                    }
                    if (is_dir($filePath)) {
                        if ($recursive) {
                            $directory_contents = self::directoryToArray($filePath, $recursive, $options);
                            // a NULL value marks an empty directory
                            if (empty($directory_contents)) {
                                $array_items[$key] = NULL;
                            } else if (empty($options['multidimensional'])) {
                                $array_items+= $directory_contents;
                            } else {
                                $array_items[$key] = $directory_contents;
                            }
                        }
                    } else {
                        $array_items[$key] = $filePath;
                    }
                }
            }
            // clean up and go home
            closedir($handle);
        }
        return $array_items;
    }
    public static function is_binary($file, $blk = NULL)
    {
        if (!is_null($blk)) {
            $blk = substr($blk, 0, 512);
        } else {
            if (!file_exists($file)) return 0;
            if (!is_file($file)) return 0;
            $fh = fopen($file, "r");
            $blk = fread($fh, 512);
            fclose($fh);
            clearstatcache();
        }
        if (substr_count($blk, "\x00") > 0) {
            return TRUE;
        }
        $blk = preg_replace('/[^\x00-\x08\x0B\x0E-\x1F]*/','', $blk);
        if (strlen($blk) > 2) {
            return TRUE;
        }
        return FALSE;
    }

    public static function delete($dir, $removeParent = TRUE) {
        if(is_file($dir)){
            return @unlink($dir);
        }
        if(!$dh = @opendir($dir)) return;
        while (($obj = readdir($dh))) {
            if($obj=='.' || $obj=='..') continue;
            try {
                if (!@unlink($dir.'/'.$obj))  {
                    self::delete($dir.'/'.$obj, true);
                }
            } catch (Exception $e) {
                self::delete($dir.'/'.$obj, true);
            }
        }
        if ($removeParent){
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
        if ($path{strlen($path) - 1} == '/') // recursively return a temporary file path
        return self::is_writable($path . uniqid(mt_rand()) . '.tmp');
        else if (is_dir($path)) return self::is_writable($path . '/' . uniqid(mt_rand()) . '.tmp');
        // check tmp file for read/write capabilities
        $rm = file_exists($path);
        $f = @fopen($path, 'a');
        if ($f === false) return false;
        fclose($f);
        if (!$rm) unlink($path);
        return true;
    }
    public static function esc_dir($dir = '') {
        return str_replace('/', '\/', $dir);
    }
}

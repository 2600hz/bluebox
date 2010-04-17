<?php defined('SYSPATH') or die('No direct access allowed.');
/*
* FreePBX Modular Telephony Software Library / Application
*
* Module:
*
* The contents of this file are subject to the Mozilla Public License
* Version 1.1 (the "License"); you may not use this file except in
* compliance with the License. You may obtain a copy of the License at
* http://www.mozilla.org/MPL/
*
* Software distributed under the License is distributed on an "AS IS"
* basis, WITHOUT WARRANTY OF ANY KIND, either express or implied. See the
* License for the specific language governing rights and limitations
* under the License.
*
* The Initial Developer of the Original Code is Michael Phillips <michael.j.phillips@gmail.com>.
*
* Portions created by the Initial Developer are Copyright (C)
* the Initial Developer. All Rights Reserved.
*
* Contributor(s):
*
*
*/
/**
 * configure.php - Media Manager Hook
 *
 * @author Michael Phillips <michael.j.phillips@gmail.com>
 * @license MPL
 * @package FreePBX3
 * @subpackage MediaManager
 */
class MediaManager_Configure extends FreePbx_Configure
{
    public static $version = 0.1;
    public static $packageName = 'file';
    public static $displayName = 'Media Management';
    public static $author = 'Michael Phillips';
    public static $vendor = 'FreePbx';
    public static $license = 'MPL';
    public static $summary = 'Media Management';
    public static $default = TRUE;
    public static $type = FreePbx_Installer::TYPE_MODULE;
    public static $required = array(
        'core' => 0.1
    );
    public static $navIcon = 'assets/img/icons/mainSettingsX.png';
    public static $navBranch = '/Media/';
    public static $navURL = 'mediamanager/index';        
    public static $navSubmenu = array(
        'Search Media' => '/mediamanager/index',
        'Upload Media' => '/mediamanager/add',
        'Delete Media' => array(
            'url' => '/mediamanager/delete',
            'disabled' => TRUE
        ) ,
        'Edit Media' => array(
            'url' => '/mediamanager/edit',
            'disabled' => TRUE
        ) ,
        'Preview Media' => array(
            'url' => '/mediamanager/preview',
            'disabled' => TRUE
        )
    );
    
    public static function _checkDirectory()
    {
        $upload_dir = rtrim(Kohana::config('upload.directory') , '/') . '/';
        // Check if the upload.directory can be written to
        if (!filesystem::is_writable($upload_dir)) {
            // if the directory exists then produce and error that it isnt writtable
            if (is_dir($upload_dir)) return 'The upload directory ' . $upload_dir . ' can not be written to!';
            // If the upload directory doesnt exist see if we have permissions one directory up to create it
            $pos = strrpos(rtrim(Kohana::config('upload.directory') , '/') , '/');
            if ($pos !== false && !filesystem::is_writable(substr($upload_dir, 0, $pos + 1))) return 'The upload directory ' . $upload_dir . ' can not be created!';
            else return true;
        } else {
            return true;
        }
    }
    public static function _checkUploadCapability()
    {
        /**
         * These vars configure the error and warning (respectively) threashold
         * for file upload parameters.  Memory_limit, and post_max_size will
         * base their threasholds on these values as well.
         */
        $min_upload_max_filesize = 2097152;
        $recommended_upload_max_filesize = 5242880;
        /**
         * This is the min amount of memory that a script should need,
         * used in addtion with min_upload_max_filesize to determine if
         * there is enough memory per script
         */
        $min_script_memory_limit = 5242880;
        /**
         * These are the error and warnign threasholds for
         * max_execution_time (in seconds)
         */
        $min_max_execution_time = 30;
        $recommended_max_execution_time = 60;
        /*
        * UPLOAD ENVIRONMENT TESTS
        */
        $issues = array(
            'errors' => false,
            'warnings' => false
        );
        // Make sure that file uploads are enabled
        $file_uploads = ini_get('file_uploads');
        if (empty($file_uploads) || $file_uploads === false) $issues['errors'][] = 'PHP setting ' . html::anchor('http://www.php.net/manual/en/ini.core.php', 'file_uploads', array(
            'target' => '_blank'
        )) . ' is disabled!';
        // Check if we met the upload_max_filesize limit
        $upload_max_filesize = self::return_bytes(ini_get('upload_max_filesize'));
        if (empty($upload_max_filesize) || (int)$upload_max_filesize < $min_upload_max_filesize) $issues['errors'][] = 'PHP setting ' . html::anchor('http://www.php.net/manual/en/ini.core.php#ini.upload-max-filesize', 'upload_max_filesize', array(
            'target' => '_blank'
        )) . ' is bellow ' . self::bytesToMb($min_upload_max_filesize) . '!';
        else if ((int)$upload_max_filesize < $recommended_upload_max_filesize) $issues['warnings'][] = 'Recommend setting ' . html::anchor('http://www.php.net/manual/en/ini.core.php#ini.upload-max-filesize', 'upload_max_filesize', array(
            'target' => '_blank'
        )) . ' larger than ' . self::bytesToMb($recommended_upload_max_filesize);
        // Check if post_max_size is equal to or larger than upload_max_filesize
        $post_max_size = self::return_bytes(ini_get('post_max_size'));
        if (empty($post_max_size) || (int)$post_max_size < $min_upload_max_filesize) $issues['errors'][] = 'PHP setting ' . html::anchor('http://www.php.net/manual/en/ini.core.php#ini.post-max-size', 'post_max_size ', array(
            'target' => '_blank'
        )) . ' is bellow ' . self::bytesToMb($min_upload_max_filesize) . '!';
        else if ($post_max_size != '-1' && (int)$post_max_size < $upload_max_filesize) $issues['warnings'][] = 'Recommend setting ' . html::anchor('http://www.php.net/manual/en/ini.core.php#ini.post-max-size', 'post_max_size ', array(
            'target' => '_blank'
        )) . ' larger than ' . self::bytesToMb($upload_max_filesize);
        // Check if memory limit is acceptable
        $memory_limit = self::return_bytes(ini_get('memory_limit'));
        if ((empty($memory_limit) || (int)$memory_limit < ($min_upload_max_filesize + $min_script_memory_limit)) && $memory_limit != - 1) $issues['errors'][] = 'PHP setting ' . html::anchor('http://www.php.net/manual/en/ini.core.php#ini.memory-limit', 'memory_limit ', array(
            'target' => '_blank'
        )) . ' is bellow ' . self::bytesToMb($min_upload_max_filesize + $min_script_memory_limit) . '!';
        else if ($memory_limit != - 1 && (int)$memory_limit < ($recommended_upload_max_filesize + $min_script_memory_limit)) $issues['warnings'][] = 'PHP setting ' . html::anchor('http://www.php.net/manual/en/ini.core.php#ini.memory-limit', 'memory_limit ', array(
            'target' => '_blank'
        )) . ' larger than ' . self::bytesToMb($recommended_upload_max_filesize + $min_script_memory_limit);
        // Ensure max_execution_time is acceptable
        $max_execution_time = ini_get('max_execution_time');
        if (empty($max_execution_time) || (int)$max_execution_time < $min_max_execution_time) $issues['errors'][] = 'PHP setting ' . html::anchor('http://www.php.net/manual/en/info.configuration.php#ini.max-execution-time', 'max_execution_time ', array(
            'target' => '_blank'
        )) . ' is bellow ' . $min_max_execution_time . ' sec !';
        else if ((int)$max_execution_time < $recommended_max_execution_time) $issues['warnings'][] = 'Recommend setting ' . html::anchor('http://www.php.net/manual/en/info.configuration.php#ini.max-execution-time', 'max_execution_time ', array(
            'target' => '_blank'
        )) . ' larger than ' . $recommended_max_execution_time . ' sec';
        // Ensure max_input_time is acceptable
        $max_input_time = ini_get('max_input_time');
        if (empty($max_input_time) || (int)$max_input_time < $min_max_execution_time * 2) $issues['errors'][] = 'PHP setting ' . html::anchor('http://www.php.net/manual/en/info.configuration.php#ini.max_input_time', 'max_input_time ', array(
            'target' => '_blank'
        )) . ' is bellow ' . $min_max_execution_time * 2 . ' sec !';
        else if ((int)$max_input_time < ($recommended_max_execution_time * 2)) $issues['warnings'][] = 'Recommend setting ' . html::anchor('http://www.php.net/manual/en/info.configuration.php#ini.max_input_time', 'max_input_time ', array(
            'target' => '_blank'
        )) . ' larger than ' . $recommended_max_execution_time * 2 . ' sec';
        return $issues;
    }
    private function return_bytes($val)
    {
        $val = trim($val);
        $last = strtolower(substr($val, strlen($val / 1) , 1));
        if ($last == 'g') $val = $val * 1024 * 1024 * 1024;
        if ($last == 'm') $val = $val * 1024 * 1024;
        if ($last == 'k') $val = $val * 1024;
        return $val;
    }
    private function bytesToMb($byte)
    {
        return $byte / 1048576 . 'Mb';
    }
}

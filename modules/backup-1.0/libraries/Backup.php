<?php

/**
 * Backup.php - Backup Module library
 *
 * @author Francis Genet & Peter Defebvre <francis.peter@2600hz.com>
 * @license MPL
 */

defined('SYSPATH') or die('No direct access allowed.');

class Backup {

	private static function deleteDirectory($dirname) {

		if(is_dir($dirname)) {
			$dir_handle = opendir($dirname);
		}
		if(!$dir_handle) {
			return false;
		}
		while($file = readdir($dir_handle)) {
			if($file != "." && $file != "..") {
				if(!is_dir($dirname."/".$file)) {
					unlink($dirname."/".$file);
				}else{
					Backup::deleteDirectory($dirname.'/'.$file);
				}
			}
		}
		closedir($dir_handle);
		rmdir($dirname);
		return true;
	}

	private static function fullCopy($source, $target) {
		if(is_dir($source)) {
			if(!is_dir($target)) {
				mkdir($target, 0777, true);
			}
			$d = dir($source);
			while(FALSE !== ( $entry = $d->read() )) {
				if($entry == '.' || $entry == '..') {
					continue;
				}
				$Entry = $source.'/'.$entry;
				if(is_dir($Entry)) {
					Backup::fullCopy($Entry, $target.'/'.$entry);
					continue;
				}
				if($entry != "freeswitch.serial") {
					copy($Entry, $target.'/'.$entry);
				}
			}

			$d->close();
		}else{
			copy($source, $target);
		}
	}

	////////////////////////////////////////////////////////////////////////////

	private static function mysqlExport($sql_dump) {
		$database = KOHANA::config('database.default');
		
		$command = "mysqldump -u"+$database['connection']['user']+" --password="+$database['connection']['pass']+" "+$database['connection']['database']+" > ".$sql_dump;
		exec($command);
	}

	public static function export($files = array("/opt/freeswitch/conf"), $sql_dump = "/dump.sql", $dir_backup_tmp = "/tmp/backup_freeswitch", $dir_backup = "/tmp/freeswitch/backup") {

		//Creation directory backup
		if(!is_dir($dir_backup)) {
			if(!mkdir($dir_backup, 0777, true)) {
				KOHANA::log('debug', "Could not create the ".$dir_backup." directory !");
			}
		}

		//Creation directory temp backup
		if(!is_dir($dir_backup_tmp)) {
			if(!mkdir($dir_backup_tmp, 0777, true)) {
				KOHANA::log('debug', "Could not create the ".$dir_backup_tmp." directory !");
			}
		}

		//Copy Conf files to temp directory
		foreach($files as $file) {
			if(is_dir($file)) {
				Backup::fullCopy($file, $dir_backup_tmp.$file);
			}
		}

		//Copy dump sql to temp directory
		Backup::mysqlExport($dir_backup_tmp.$sql_dump);

		//Archive Creation
		$file_name = $dir_backup.'/backup_'.date('m_j_y_h-i');
		exec("tar -czpf ".$file_name.".tar.gz ".$dir_backup_tmp);

		//Delete directory temp backup
		Backup::deleteDirectory($dir_backup_tmp);
	}

	////////////////////////////////////////////////////////////////////////////

	private static function mysqlImport($sql_dump) {
		$database = KOHANA::config('database.default');
		
		$command = "mysql -u"+$database['connection']['user']+" --password="+$database['connection']['pass']+" "+$database['connection']['database']+" < ".$sql_dump;
		exec($command);
	}

	public static function import($backup_file = "", $files = array("/opt/freeswitch/conf"), $sql_dump = "/dump.sql", $dir_backup_tmp = "/tmp/backup_freeswitch", $dir_backup = "/tmp/freeswitch/backup") {

		$state = 1;

		KOHANA::log('debug', $backup_file);

		$command = "tar -xzf ".$dir_backup."/".$backup_file." -C /";
		exec($command);

		Backup::mysqlImport($dir_backup_tmp.$sql_dump);
		unlink($dir_backup_tmp.$sql_dump);

		foreach($files as $file) {
			if(is_dir($file)) {
				Backup::fullCopy($dir_backup_tmp.$file, $file);
			}else{
				$state = "Create directory ".$file." first !";
			}
		}

		//Delete directory temp backup
		Backup::deleteDirectory($dir_backup_tmp);

		return $state;
	}

	////////////////////////////////////////////////////////////////////////////
	public static function listBackup() {

		$select_options = array();
		$pathBaseDir = "/tmp/freeswitch/backup";

		if(!is_dir($pathBaseDir)) {
			if(!mkdir($pathBaseDir, 0777, true)) {
				KOHANA::log('debug', "Could not create the ".$pathBaseDir." directory !");
			}
		}

		$baseDir = opendir($pathBaseDir);
		while($file = readdir($baseDir)) {
			if($file != "." && $file != ".." && $file != "" && $file != null) {
				$pattern = '/backup*/';
				if(preg_match($pattern, $file) == 1) {
					$select_options[$file] = $file;
				}
			}
		}
		closedir($baseDir);

		return array_reverse($select_options);
	}

}
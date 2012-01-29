<?php defined('SYSPATH') or die('No direct access allowed.');
class Disposition_Driver
{
	public static function set($faxProfile)
	{
		return false;
	}
	
	public static function delete($faxProfile)
	{
		return false;
	}
	
	public static function network()
	{
		return false;
	}
	
	public static function conditioning()
	{
		return false;
	}
	
	public static function preRoute()
	{
		return false;
	}
		
	public static function postRoute()
	{
		return false;
	}
	
	public static function preAnswer()
	{
		return false;
	}
	
	public static function postAnswer()
	{
		return false;
	}
	
	public static function main()
	{
		return false;
	}
	
	public static function preNumber()
	{
		return false;
	}
	
	public static function dialplan($number)
	{
		return false;
	}
	
	public static function postNumber()
	{
		return false;
	}
	
	public static function catchAll()
	{
		return false;
	}
	
	public static function postExecute()
	{
		return false;
	}
}	
?>
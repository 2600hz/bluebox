<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Model base class.
 *
 * $Id: Model.php 3914 2009-01-19 21:14:37Z zombor $
 *
 * @package    Core
 * @author     Kohana Team
 * @copyright  (c) 2007-2009 Kohana Team
 * @license    http://kohanaphp.com/license.html
 */
class Model_Core {

	// Database object
	protected $db;

	/**
	 * Loads the database instance, if the database is not already loaded.
	 *
	 * @return  void
	 */
	public function __construct()
	{
		if ( ! is_object($this->db))
		{
			// Load the default database
			$this->db = Database::instance('default');
		}
	}

} // End Model
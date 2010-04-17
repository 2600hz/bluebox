<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * PHPUnit library.
 *
 *
 * @package    PHPUnit
 */
class PHPUnit_Core
{
	protected $_phpunit_options = array();
	protected $_config = array();
	
	public function __construct($groups = NULL)
	{
		spl_autoload_unregister(array('Kohana', 'auto_load'));
		spl_autoload_register(array($this, 'auto_load'));
		set_include_path(MODPATH.'phpunit/vendor' . PATH_SEPARATOR . get_include_path());

		$this->_config = Kohana::config('phpunit');

		$this->_get_phpunit_options();
		
		if ( ! isset($this->_phpunit_options['groups']))
			$this->_phpunit_options['groups'] = array();
		
		if (isset($groups) AND is_array($groups))
			$this->_phpunit_options['groups'] += $groups;
		
	}
	
	protected function _get_phpunit_options($config_group = 'default')
	{
		if ( ! isset($this->_config[$config_group]))
			return;
		
		$args = $this->_config[$config_group];

		if (isset($args['plain']) && ! $args['plain'] instanceof PHPUnit_TextUI_ResultPrinter)
		{
			$args['listeners'][] = new PHPUnit_TextUI_ResultPrinter($args['plain'], TRUE);
		}
//		if ( ! isset($this->_phpunit_options['listGroups']))
//			$this->_phpunit_options['listGroups'] = FALSE;

		$this->_phpunit_options += $args;
	}
	
	protected function _get_test_suite()
	{
		$this->_phpunit_options['test'] = new PHPUnit_Test_Suite('Kohana', 'tests');
	}
	
	
	
	public function execute($config_group = 'default')
	{
		ini_set('memory_limit','512M');
		
		$this->_get_phpunit_options($config_group);
		
		require_once Kohana::find_file('vendor', 'PHPUnit/Util/Filter');
		require_once Kohana::find_file('vendor', 'PHPUnit/Framework/TestSuite');
		require_once Kohana::find_file('vendor', 'PHPUnit/TextUI/TestRunner');

		$this->_whitelist();
		$this->_blacklist();

		// We want all warnings so PHPUnit can take care of them.
		error_reporting( E_ALL | E_STRICT );

		// Hand control of errors and exceptions to PHPUnit
		restore_exception_handler();
		restore_error_handler();

		// Turn off the output biffer.
		ob_end_flush();

		//define('PHPUnit_MAIN_METHOD', 'PHPUnit_TextUI_Command::main');
		
		$this->_get_test_suite();

		$arguments = $this->_phpunit_options;
		
		$runner = new PHPUnit_TextUI_TestRunner;

		if (is_object($arguments['test']) AND $arguments['test'] instanceof PHPUnit_Framework_Test)
		{
			$suite = $arguments['test'];
		}
		else
		{
			$suite = $runner->getTest(
				$arguments['test'],
				$arguments['testFile'],
				$arguments['syntaxCheck']
			);
		}

		if ($suite->testAt(0) instanceof PHPUnit_Framework_Warning AND
			strpos($suite->testAt(0)->getMessage(), 'No tests found in class') !== FALSE)
		{
			$message   = $suite->testAt(0)->getMessage();
			$start     = strpos($message, '"') + 1;
			$end       = strpos($message, '"', $start);
			$className = substr($message, $start, $end - $start);

			require Kohana::find_file('vendor', 'PHPUnit/Util/Skeleton/Test');
			$skeleton = new PHPUnit_Util_Skeleton_Test($className, $arguments['testFile']);

			$result = $skeleton->generate(TRUE);

			if ( ! $result['incomplete'])
			{
				eval(str_replace(array('<?php', '?>'), '', $result['code']));
				$suite = new PHPUnit_Framework_TestSuite($arguments['test'].'Test');
			}
		}

		if ($arguments['listGroups'])
		{
			PHPUnit_TextUI_TestRunner::printVersionString();

			print "Available test group(s):\n";

			$groups = $suite->getGroups();
			sort($groups);

			foreach ($groups as $group)
			{
				print " - $group\n";
			}

			exit(PHPUnit_TextUI_TestRunner::SUCCESS_EXIT);
		}

		try
		{
			$result = $runner->doRun($suite, $arguments);

		}
		catch (Exception $e)
		{
			throw new RuntimeException('Could not create and run test suite: '.$e->getMessage());
		}

		if ($result->wasSuccessful())
			exit(PHPUnit_TextUI_TestRunner::SUCCESS_EXIT);
		else if ($result->errorCount() > 0)
			exit(PHPUnit_TextUI_TestRunner::EXCEPTION_EXIT);
		else
			exit(PHPUnit_TextUI_TestRunner::FAILURE_EXIT);
	}
	
	protected function _whitelist()
	{
		$folders = array('helpers','controllers','libraries');
		foreach ($folders as $folder)
		{
			$files = self::list_files($folder, TRUE);
			foreach ($files as $file)
			{
				if (is_file($file))
				{
					if ($file == __FILE__)
					{
						continue;
					}
					else
					{
						PHPUnit_Util_Filter::addFileToWhitelist($file);
					}
				}
			}
		}
	}
	
	protected function _blacklist()
	{
		PHPUnit_Util_Filter::addFileToFilter(__FILE__);

		$folders = array('vendor','libraries/drivers/Database');

		foreach ($folders as $folder)
		{
			$files = self::list_files($folder, TRUE);
			foreach ($files as $file)
			{
				if (is_file($file))
				{
					PHPUnit_Util_Filter::addFileToFilter($file);
					PHPUnit_Util_Filter::removeFileFromWhitelist($file);
				}
			}
		}
	}

	
	/**
	 * Custom list_files function 
	 * 
	 * Modified version for PHPUnit compat.
	 *
	 */
	 
	 public static function list_files($directory, $recursive = FALSE, $path = FALSE)
	{
		$files = array();

		if ($path === FALSE)
		{
			$paths = array_reverse(Kohana::include_paths());

			foreach ($paths as $path)
			{
				// Recursively get and merge all files
				$files = array_merge($files, self::list_files($directory, $recursive, $path.$directory));
			}
		}
		else
		{
			$path = rtrim($path, '/').'/';

			if (is_readable($path))
			{
				$items = (array) glob($path.'*');

				if ( ! empty($items))
				{
					foreach ($items as $index => $item)
					{
						$item = str_replace('\\', '/', $item);
						$file_key = end(explode('/', pathinfo($item, PATHINFO_DIRNAME)));

						// Use the filename as the key so we don't list the same file twice
						$files[$file_key.'-'.pathinfo($item, PATHINFO_FILENAME)] = $item;
						// Handle recursion
						if (is_dir($item) AND $recursive == TRUE)
						{
							// Filename should only be the basename
							$item = pathinfo($item, PATHINFO_BASENAME);

							// Append sub-directory search
							$files = array_merge($files, self::list_files($directory, TRUE, $path.$item));
						}
					}
				}
			}
		}
		return $files;
	}
	
	/**
	 * Provides class auto-loading.
	 *
	 * Copy of Kohana::auto_load with minor changes
	 * use require_once instead of require
	 * add $suffix === 'Test'
	 * change self::find_file to Kohana::file_file
	 * change self::$configuration to Kohana::config()
	 *
	 * @throws  Kohana_Exception
	 * @param   string  name of class
	 * @return  bool
	 */
	public static function auto_load($class)
	{
		if (class_exists($class, FALSE))
			return TRUE;

		if (($suffix = strrpos($class, '_')) > 0)
		{
			// Find the class suffix
			$suffix = substr($class, $suffix + 1);
		}
		else
		{
			// No suffix
			$suffix = FALSE;
		}

		if ($suffix === 'Core')
		{
			$type = 'libraries';
			$file = substr($class, 0, -5);
		}
		elseif ($suffix === 'Controller')
		{
			$type = 'controllers';
			// Lowercase filename
			$file = strtolower(substr($class, 0, -11));
		}
		elseif ($suffix === 'Model')
		{
			$type = 'models';
			// Lowercase filename
			$file = strtolower(substr($class, 0, -6));
		}
		elseif ($suffix === 'Driver')
		{
			$type = 'libraries/drivers';
			$file = str_replace('_', '/', substr($class, 0, -7));
		}
		elseif ($suffix === 'Test')
		{
			$type = 'tests';
			$file = $class;
		}
		else
		{
			// This could be either a library or a helper, but libraries must
			// always be capitalized, so we check if the first character is
			// uppercase. If it is, we are loading a library, not a helper.
			$type = ($class[0] < 'a') ? 'libraries' : 'helpers';
			$file = $class;
		}

		if ($filename = Kohana::find_file($type, $file))
		{
			// Load the class
			require_once $filename;
		}
		else
		{
			// The class could not be found
			return FALSE;
		}

		if ($filename = Kohana::find_file($type, Kohana::config('core.extension_prefix').$class))
		{
			// Load the class extension
			require $filename;
		}
		elseif ($suffix !== 'Core' AND class_exists($class.'_Core', FALSE))
		{
			// Class extension to be evaluated
			$extension = 'class '.$class.' extends '.$class.'_Core { }';

			// Start class analysis
			$core = new ReflectionClass($class.'_Core');

			if ($core->isAbstract())
			{
				// Make the extension abstract
				$extension = 'abstract '.$extension;
			}

			// Transparent class extensions are handled using eval. This is
			// a disgusting hack, but it gets the job done.
			eval($extension);
		}

		return TRUE;
	}
}
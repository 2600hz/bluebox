<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * @author K Anderson
 * @license LGPL
 * @package Bluebox
 * @subpackage Core
 */
class url extends url_Core
{
	public static function guess_site_domain()
	{
		if (PHP_SAPI === 'cli')
		{
			// Command line requires a bit of hacking
			if (isset($_SERVER['argv'][1]))
			{
				$current_uri = $_SERVER['argv'][1];

				// Remove GET string from segments
				if (($query = strpos($current_uri, '?')) !== FALSE)
				{
					list ($current_uri, $query) = explode('?', $current_uri, 2);

					// Parse the query string into $_GET
					parse_str($query, $_GET);

					// Convert $_GET to UTF-8
					$_GET = utf8::clean($_GET);
				}
			}
		}
		elseif (isset($_GET['kohana_uri']))
		{
			// Use the URI defined in the query string
			$current_uri = $_GET['kohana_uri'];

			// Remove the URI from $_GET
			unset($_GET['kohana_uri']);

			// Remove the URI from $_SERVER['QUERY_STRING']
			$_SERVER['QUERY_STRING'] = preg_replace('~\bkohana_uri\b[^&]*+&?~', '', $_SERVER['QUERY_STRING']);
		}
		elseif (isset($_SERVER['REQUEST_URI']) AND $_SERVER['REQUEST_URI'])
		{
			$current_uri = $_SERVER['REQUEST_URI'];
		}
		elseif (isset($_SERVER['ORIG_PATH_INFO']) AND $_SERVER['ORIG_PATH_INFO'])
		{
			$current_uri = $_SERVER['ORIG_PATH_INFO'];
		}
		elseif (isset($_SERVER['PHP_SELF']) AND $_SERVER['PHP_SELF'])
		{
			$current_uri = $_SERVER['PHP_SELF'];
		} else {
                        return '/';
                }

		// The front controller directory and filename
		$fc = substr(realpath($_SERVER['SCRIPT_FILENAME']), strlen(DOCROOT));

		if (($strpos_fc = strpos($current_uri, $fc)) !== FALSE)
		{
			// Remove the front controller from the current uri
			$current_uri = substr($current_uri, 0, $strpos_fc + strlen($fc) + 1);
		}
                
		if ($current_uri !== '')
		{
                        // remove the index page if it is in there
                        $indexPage = Kohana::config('core.index_page');
			if (!empty($indexPage))
			{
                                $current_uri = str_replace($indexPage, '', $current_uri);
			} else {
                            $current_uri = str_replace('index.php', '', $current_uri);
                        }

                        // Reduce multiple slashes into single slashes
			$current_uri = preg_replace('#//+#', '/', $current_uri);

                        return trim($current_uri, '/');
		} else {
                        return '/';
                }
	}

}
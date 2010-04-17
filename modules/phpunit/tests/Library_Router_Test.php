<?php
/**
 * Router Library Unit Tests
 *
 * @package     Core
 * @subpackage  Libraries
 * @author      Chris Bandy
 * @group   core
 * @group   core.libraries
 * @group   core.libraries.router
 */
class Library_Router_Test extends PHPUnit_Framework_TestCase
{
	protected $router_vars;
	protected $server_vars;

	protected function setUp()
	{
		// Save $_SERVER
		$this->server_vars = $_SERVER;

		// Save Router members
		$this->router_vars = array(
			'complete_uri'  => Router::$complete_uri,
			'current_uri'   => Router::$current_uri,
			'query_string'  => Router::$query_string,
			'rsegments'     => Router::$rsegments,
			'routed_uri'    => Router::$routed_uri,
			'segments'      => Router::$segments,
			'url_suffix'    => Router::$url_suffix,
		);
	}

	protected function tearDown()
	{
		// Restore Router members
		foreach ($this->router_vars as $key => $value)
		{
			Router::$$key = $value;
		}

		// Restore $_SERVER
		$_SERVER = $this->server_vars;
	}

	public function find_uri_provider()
	{
		return array(
			array('/', '',
				array(
					// Apache 2.2
					array(
						'PHP_SELF' => '/'.KOHANA,
						'SCRIPT_NAME' => '/'.KOHANA,
					),

					// lighttpd 1.4.20
					array(
						'PATH_INFO' => '',
						'PHP_SELF' => '/'.KOHANA,
						'SCRIPT_NAME' => '/'.KOHANA,
					),

					// IIS 6.0
					array(
						'ORIG_PATH_INFO' => DOCROOT.'/'.KOHANA,
						'PHP_SELF' => DOCROOT.'/'.KOHANA,
						'SCRIPT_NAME' => DOCROOT.'/'.KOHANA,
					),

					// IIS 6.0 with Ionics Isapi Rewrite Filter, see #1730
					array(
						'ORIG_PATH_INFO' => '/'.KOHANA,
						'PATH_INFO' => '',
						'PHP_SELF' => '/'.KOHANA,
						'SCRIPT_NAME' => '/'.KOHANA,
					),
				)
			),

			array('/'.KOHANA, '',
				array(
					// Apache 2.2
					array(
						'PHP_SELF' => '/'.KOHANA,
						'SCRIPT_NAME' => '/'.KOHANA,
					),

					// lighttpd 1.4.20
					array(
						'PATH_INFO' => '',
						'PHP_SELF' => '/'.KOHANA,
						'SCRIPT_NAME' => '/'.KOHANA,
					),

					// IIS 6.0
					array(
						'ORIG_PATH_INFO' => DOCROOT.'/'.KOHANA,
						'PHP_SELF' => DOCROOT.'/'.KOHANA,
						'SCRIPT_NAME' => DOCROOT.'/'.KOHANA,
					),

					// IIS 6.0 with Ionics Isapi Rewrite Filter, see #1730
					array(
						'ORIG_PATH_INFO' => '/'.KOHANA,
						'PATH_INFO' => '',
						'PHP_SELF' => '/'.KOHANA,
						'SCRIPT_NAME' => '/'.KOHANA,
					),
				)
			),

			array('/default/index', 'default/index',
				array(
					// Apache 2.2, lighttpd 1.2.40
					array(
						'PATH_INFO' => '/default/index',
						'PHP_SELF' => '/'.KOHANA.'/default/index',
						'SCRIPT_NAME' => '/'.KOHANA,
					),

					// IIS 6.0
					array(
						'ORIG_PATH_INFO' => DOCROOT.'/'.KOHANA.'/default/index',
						'PATH_INFO' => '/default/index',
						'PHP_SELF' => DOCROOT.'/'.KOHANA.'/default/index',
						'SCRIPT_NAME' => DOCROOT.'/'.KOHANA,
					),

					// IIS 6.0 with Ionics Isapi Rewrite Filter, see #1730
					array(
						'ORIG_PATH_INFO' => '/'.KOHANA.'/default/index',
						'PATH_INFO' => '/default/index',
						'PHP_SELF' => '/'.KOHANA,
						'SCRIPT_NAME' => '/'.KOHANA,
					),
				)
			),

			// URL may contain KOHANA, see #1810
			array('/default/index/'.KOHANA, 'default/index/'.KOHANA,
				array(
					// Apache 2.2, lighttpd 1.2.40
					array(
						'PATH_INFO' => '/default/index/'.KOHANA,
						'PHP_SELF' => '/'.KOHANA.'/default/index/'.KOHANA,
						'SCRIPT_NAME' => '/'.KOHANA,
					),

					// IIS 6.0
					array(
						'ORIG_PATH_INFO' => DOCROOT.'/'.KOHANA.'/default/index/'.KOHANA,
						'PATH_INFO' => '/default/index/'.KOHANA,
						'PHP_SELF' => DOCROOT.'/'.KOHANA.'/default/index/'.KOHANA,
						'SCRIPT_NAME' => DOCROOT.'/'.KOHANA,
					),

					// IIS 6.0 with Ionics Isapi Rewrite Filter, see #1730
					array(
						'ORIG_PATH_INFO' => '/'.KOHANA.'/default/index/'.KOHANA,
						'PATH_INFO' => '/default/index/'.KOHANA,
						'PHP_SELF' => '/'.KOHANA,
						'SCRIPT_NAME' => '/'.KOHANA,
					),
				)
			),
		);
	}

	/**
	 * @dataProvider find_uri_provider
	 * @test
	 */
	public function find_uri($request_uri, $current_uri, $servers)
	{
		if (PHP_SAPI === 'cli')
		{
			$this->markTestSkipped();
		}

		foreach ($servers as $vars)
		{
			$_SERVER = array_merge(
				$this->server_vars,
				array(
					'ORIG_PATH_INFO' => NULL,
					'PATH_INFO' => NULL,
					'PHP_SELF' => NULL,
					'REQUEST_URI' => $request_uri,
					'SCRIPT_NAME' => NULL,
				),
				$vars
			);

			Router::find_uri();

			$this->assertEquals($current_uri, Router::$current_uri);
		}
	}
}

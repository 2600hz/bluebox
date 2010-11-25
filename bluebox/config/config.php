<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Allow the installer to be run.  This should be set to false after installation
 */
$config['installer_enabled'] = FALSE;

/**
 * Base path of the web site. If this includes a domain, eg: localhost/kohana/
 * then a full URL will be used, eg: http://localhost/kohana/. If it only includes
 * the path, and a site_protocol is specified, the domain will be auto-detected.
 */
$config['site_domain'] = '/';

/**
 * Force a default protocol to be used by the site. If no site_protocol is
 * specified, then the current protocol is used, or when possible, only an
 * absolute path (with no protocol/domain) is used.
 */
$config['site_protocol'] = '';

/**
 * Name of the front controller for this application. Default: index.php
 *
 * This can be removed by using URL rewriting.
 */
$config['index_page'] = 'index.php';      // If you don't have mod_rewrite enabled, uncomment this line!

/**
 * Fake file extension that will be added to all generated URLs. Example: .html
 * NOTE: DO NOT enable this line if you plan to use JSON and XML as the URL ending is utilized to detect what's being asked for
 */
//$config['url_suffix'] = '.html';

/**
 * Length of time of the internal cache in seconds. 0 or FALSE means no caching.
 * The internal cache stores file paths and config entries across requests and
 * can give significant speed improvements at the expense of delayed updating.
 */
$config['internal_cache'] = FALSE;

/**
 * Enable or disable gzip output compression. This can dramatically decrease
 * server bandwidth usage, at the cost of slightly higher CPU usage. Set to
 * the compression level (1-9) that you want to use, or FALSE to disable.
 *
 * Do not enable this option if you are using output compression in php.ini!
 */
$config['output_compression'] = FALSE;

/**
 * Enable or disable global XSS filtering of GET, POST, and SERVER data. This
 * option also accepts a string to specify a specific XSS filtering tool.
 */
$config['global_xss_filtering'] = FALSE;

/**
 * Enable or disable hooks.
 */
$config['enable_hooks'] = TRUE;     // Bluebox will fail miserably if you turn this off

/**
 * Log thresholds:
 *  0 - Disable logging
 *  1 - Errors and exceptions
 *  2 - Warnings
 *  3 - Notices
 *  4 - Debugging
 */
$config['log_threshold'] = 4;

/**
 * Message logging directory.
 */
$config['log_directory'] = APPPATH.'logs';

/**
 * Enable or disable displaying of Kohana error pages. This will not affect
 * logging. Turning this off will disable ALL error pages.
 */
$config['display_errors'] = TRUE;

/**
 * Enable or disable statistics in the final output. Stats are replaced via
 * specific strings, such as {execution_time}.
 *
 * @see http://docs.kohanaphp.com/general/configuration
 */
$config['render_stats'] = TRUE;

/**
 * Filename prefixed used to determine extensions. For example, an
 * extension to the Controller class would be named MY_Controller.php.
 */
$config['extension_prefix'] = 'Bluebox_';

/**
 * Additional resource paths, or "modules". Each path can either be absolute
 * or relative to the docroot. Modules can include any resource that can exist
 * in your application directory, configuration files, controllers, views, etc.
 */
$config['modules'] = array
(
	MODPATH.'auth-1.0',      // Authentication
        MODPATH.'packagemanager-1.0'
	// MODPATH.'forge',     // Form generation
	// MODPATH.'kodoc',     // Self-generating documentation
	// MODPATH.'media',     // Media caching and compression
	// MODPATH.'gmaps',     // Google Maps integration
	// MODPATH.'archive',   // Archive utility
	// MODPATH.'phpunit', // Unit testing
	// MODPATH.'object_db', // New OOP Database library (testing only!)
);

// Do we require login for all pages?
// NOTE: Turning this off will break any modules that ASSUME login is required
// THIS OPTION IS FOR DEBUGGING ONLY! It is NOT a feature. Things will definitely break if you enable this.
$config['require_login'] = TRUE;

/**
 * If this is true the user has allowed us to collect anonymous statistics about what modules they use
 */
$config['anonymous_statistics'] = FALSE;

/**
 * Is this an oxymoron? Well we need to be able to group
 * multiple responses, it is not used for any other reason. Promise :)
 */
$config['anonymous_id'] = '4AAC2428C730C14766CA3C25198E4FEF';

/**
 * If true then a minimum password complexity is enforced
 */
$config['pwd_complexity'] = TRUE;

/**
 * Whether we allow registrations at all from the login pages or whether only system administrators can create/manage accounts via
 * the user manager.
 */
$config['allow_registrations'] = FALSE;

/**
 * Whether or not to combine the login and registration views and functionality on the same interface, or use seperate pages
 */
$config['combine_login_register'] = TRUE;

/**
 * Always set username and email to be the same thing?
 * NOTE: This just copies the email address into the username field during registration and hides the field for username during registration.
 * During login, the username field is replaced with the email address field (and associated lookups in the DB)
 */
$config['username_is_email'] = TRUE;

/**
 * This is a list of repositories to use to check for updates
 */
$config['repositories'] = array();

/**
 * Product display name. If you want to rebrand Bluebox with another name, change this
 */
$config['product_name'] = 'blue.box';

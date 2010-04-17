<?php defined('SYSPATH') OR die('No direct access allowed.');

$lang = array
(
	E_KOHANA             => array( 1, 'Framework-virhe',    'Please check the Kohana documentation for information about the following error.'),
	E_PAGE_NOT_FOUND     => array( 1, 'Sivua ei löydy',     'The requested page was not found. It may have moved, been deleted, or archived.'),
	E_DATABASE_ERROR     => array( 1, 'Tietokantavirhe',    'A database error occurred while performing the requested procedure. Please review the database error below for more information.'),
	E_RECOVERABLE_ERROR  => array( 1, 'Palautuva virhe',    'An error was detected which prevented the loading of this page. If this problem persists, please contact the website administrator.'),
	E_ERROR              => array( 1, 'Vakava virhe',       ''),
	E_USER_ERROR         => array( 1, 'Vakava virhe',       ''),
	E_PARSE              => array( 1, 'Syntaksivirhe',      ''),
	E_WARNING            => array( 1, 'Varoitus',           ''),
	E_USER_WARNING       => array( 1, 'Varoitus',           ''),
	E_STRICT             => array( 2, 'Strict Mode -virhe', ''),
	E_NOTICE             => array( 2, 'Huomautus',          ''),
);
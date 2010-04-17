<?php defined('SYSPATH') OR die('No direct access allowed.');

$lang = array
(
	E_KOHANA             => array( 1, 'Framework Error',   'Проверете во документацијата на Kohana во врска со следната грешка.'),
	E_PAGE_NOT_FOUND     => array( 1, 'Page Not Found',    'Страната не е пронајдена. Можеби е преместена, избришана или архивирана.'),
	E_DATABASE_ERROR     => array( 1, 'Database Error',    'Се појави грешка во базата на податоци при извршување на побараната процедура. За повеќе информации, прегледајте ја грешката опишана доле.'),
	E_RECOVERABLE_ERROR  => array( 1, 'Recoverable Error', 'Се појави грешка кое го прекина вчитувањето на оваа страна. Ако овој проблем перзистира, контактирајте со веб администраторот.'),
	E_ERROR              => array( 1, 'Fatal Error',       ''),
	E_USER_ERROR         => array( 1, 'Fatal Error',       ''),
	E_PARSE              => array( 1, 'Syntax Error',      ''),
	E_WARNING            => array( 1, 'Warning Message',   ''),
	E_USER_WARNING       => array( 1, 'Warning Message',   ''),
	E_STRICT             => array( 2, 'Strict Mode Error', ''),
	E_NOTICE             => array( 2, 'Runtime Message',   ''),
);
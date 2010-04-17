<?php defined('SYSPATH') OR die('No direct access allowed.');

$lang = array
(
	E_KOHANA             => array( 1, 'Framework Error',   '以下错误信息详情请参考Kohana文档.'),
	E_PAGE_NOT_FOUND     => array( 1, 'Page Not Found',    '请求的文档不存在.'),
	E_DATABASE_ERROR     => array( 1, 'Database Error',    '请求执行过程中数据库发生错误. 请检查数据库错误信息.'),
	E_RECOVERABLE_ERROR  => array( 1, 'Recoverable Error', '页面加载失败. 如果此信息反复出现,请联系网站管理员.'),
	E_ERROR              => array( 1, 'Fatal Error',       ''),
	E_USER_ERROR         => array( 1, 'Fatal Error',       ''),
	E_PARSE              => array( 1, 'Syntax Error',      ''),
	E_WARNING            => array( 1, 'Warning Message',   ''),
	E_USER_WARNING       => array( 1, 'Warning Message',   ''),
	E_STRICT             => array( 2, 'Strict Mode Error', ''),
	E_NOTICE             => array( 2, 'Runtime Message',   ''),
);
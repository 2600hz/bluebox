<?php defined('SYSPATH') OR die('No direct access allowed.');

$lang = array
(
	E_KOHANA             => array( 1, 'Framework Error',   'Por favor, verifique a documentacão do Kohana para maiores informacões sobre o seguinte erro.'),
	E_PAGE_NOT_FOUND     => array( 1, 'Page Not Found',    'Não foi possível encontrar a página requisitada, em virtude de remocão, renomeacão, ou arquivamento.'),
	E_DATABASE_ERROR     => array( 1, 'Database Error',    'Ocorreu um erro de banco de dados enquanto se processava o procedimento requisitado. Por favor, revise o erro de banco de dados abaixo para maiores informacões.'),
	E_RECOVERABLE_ERROR  => array( 1, 'Recoverable Error', 'Um erro foi detectado, prevenindo o carregamento desta página. Caso o problema persista, por favor, contacte o administrador do website.'),
	E_ERROR              => array( 1, 'Fatal Error',       ''),
	E_USER_ERROR         => array( 1, 'Fatal Error',       ''),
	E_PARSE              => array( 1, 'Syntax Error',      ''),
	E_WARNING            => array( 1, 'Warning Message',   ''),
	E_USER_WARNING       => array( 1, 'Warning Message',   ''),
	E_STRICT             => array( 2, 'Strict Mode Error', ''),
	E_NOTICE             => array( 2, 'Runtime Message',   ''),
);
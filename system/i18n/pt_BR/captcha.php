<?php defined('SYSPATH') OR die('No direct access allowed.');

$lang = array
(
	'file_not_found' => 'O arquivo especificado, %s, não foi encontrado. Por favor verifique se o arquivo existe usando file_exists() antes de utiliza-lo.',
	'requires_GD2'   => 'A biblioteca Captcha necessita do GD2 com suporte FreeType. Por favor veja http://php.net/gd_info para maiores informações.',

	// Words of varying length for the Captcha_Word_Driver to pick from
	// Note: use only alphanumeric characters
	'words' => array
	(
		'cd', 'tv', 'it', 'to', 'be', 'or',
		'sun', 'car', 'dog', 'bed', 'kid', 'egg',
		'bike', 'tree', 'bath', 'roof', 'road', 'hair',
		'hello', 'world', 'earth', 'beard', 'chess', 'water',
		'barber', 'bakery', 'banana', 'market', 'purple', 'writer',
		'america', 'release', 'playing', 'working', 'foreign', 'general',
		'aircraft', 'computer', 'laughter', 'alphabet', 'kangaroo', 'spelling',
		'architect', 'president', 'cockroach', 'encounter', 'terrorism', 'cylinders',
	),

	// Riddles for the Captcha_Riddle_Driver to pick from
	// Note: use only alphanumeric characters
	'riddles' => array
	(
		array('Você odeia spam? (sim ou não)', 'sim'),
		array('Você é um robo?', 'não'),
		array('Fogo é... (quente ou frio)', 'quente'),
		array('A estação após o outono é...', 'inverno'),
		array('Que dia da semana é hoje?', strftime('%A')),
		array('Em que mês do ano nós estamos?', strftime('%B')),
	),
);

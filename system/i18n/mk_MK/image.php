<?php defined('SYSPATH') OR die('No direct access allowed.');

$lang = array
(
	'getimagesize_missing'    => 'Библиотеката Image задолжително ја бара PHP функцијата <tt>getimagesize</tt>, која не е достапна во оваа инсталација.',
	'unsupported_method'      => 'Конфигурираниот драјвер не поддржува %s сликовни трансформации.',
	'file_not_found'          => 'Назначената слика, %s, не е пронајдена. Потврдете дека сликите постојат користејќи <tt>file_exists</tt> пред да вршите манипулација со нив.',
	'type_not_allowed'        => 'Назначената слика, %s, е тип на слика кој не е дозволен.',
	'invalid_width'           => 'Ширината која ја имате назначено, %s, не е валидна.',
	'invalid_height'          => 'Висината која ја имате назначено, %s, не е валидна.',
	'invalid_dimensions'      => 'Назначените димензии за %s не се валидни.',
	'invalid_master'          => 'Главните назначени димензии не се валидни.',
	'invalid_flip'            => 'Flip насоката која ја имате назначено не е валидна.',

	// ImageMagick specific messages
	'imagemagick' => array
	(
		'not_found' => 'Назначениот директориум на ImageMagick не го содржи задолжителниот програм, %s.',
	),

	// GD specific messages
	'gd' => array
	(
		'requires_v2' => 'За библиотеката Image задолжителен е GD2. Молиме погледнете на http://php.net/gd_info за повеќе информации.',
	),
);

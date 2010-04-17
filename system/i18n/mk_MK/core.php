<?php defined('SYSPATH') OR die('No direct access allowed.');

$lang = array
(
	'there_can_be_only_one' => 'Може да има само една инстанца на Kohana при еден циклус.',
	'uncaught_exception'    => 'Uncaught %s: %s во датотека %s, линија %s',
	'invalid_method'        => 'Повикана е погрешна метода <tt>%s</tt> во <tt>%s</tt>.',
	'log_dir_unwritable'    => 'Твојот log.directory config елемент не поинтира во директориум во кој може да се пишува.',
	'resource_not_found'    => 'Побараниот %s, <tt>%s</tt>, не е пронајден.',
	'invalid_filetype'      => 'Побараниот тип на датотека, <tt>.%s</tt>, не е дозволен во view конфигурационата датотека.',
	'no_default_route'      => 'Подесете ја default рутата во <tt>config/routes.php</tt>.',
	'view_set_filename'     => 'Мора да се сетира view датотеката пред са се повикува render',
	'no_controller'         => 'Kohana не пронајде контролер за да го процесира ова барање: %s',
	'page_not_found'        => 'Страната која ја побаравте, <tt>%s</tt>, не е пронајдена.',
	'stats_footer'          => 'Вчитано за {execution_time} секунди, употребено {memory_usage} меморија. Креирано со Kohana v{kohana_version}.',
	'error_file_line'       => '<tt>%s <strong>[%s]:</strong></tt>',
	'stack_trace'           => 'Stack Trace',
	'generic_error'         => 'Барањето Не Може Да Биде Извршено',
	'errors_disabled'       => 'Можете да отидете на <a href="%s">почетната страница</a> или да <a href="%s">пробате повторно</a>.',

	// Drivers
	'driver_implements'     => 'Драјверот %s за библиотеката %s мора да го имплементира интерфејсот %s',
	'driver_not_found'      => 'Драјверот %s за библиотеката %s не е пронајден',

	// Resource names
	'controller'            => 'controller',
	'helper'                => 'helper',
	'library'               => 'library',
	'driver'                => 'driver',
	'model'                 => 'model',
	'view'                  => 'view',
);
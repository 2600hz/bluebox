<?php defined('SYSPATH') OR die('No direct access allowed.');

$lang = array
(
	'there_can_be_only_one' => 'Yhdellä sivulla voi olla vain yksi Kohana-instanssi.',
	'uncaught_exception'    => 'Käsittelemätön virhe %s: %s tiedostossa %s rivillä %s',
	'invalid_method'        => 'Tuntematon metodi %s kutsuttu %s',
	'invalid_property'      => 'Ominaisuutta %s ei löydy luokasta %s.',
	'log_dir_unwritable'    => 'Lokihakemistoon ei voida kirjoittaa: %s',
	'resource_not_found'    => 'Pyydettyä %s, %s, ei löydy',
	'invalid_filetype'      => 'Pyydetty tiedostotyyppi, .%s, ei ole sallittu näkymäasetuksissa',
	'view_set_filename'     => 'Anna näkymän tiedostonimi ennen kuin kutsut render',
	'no_default_route'      => 'Anna oletusreititys tiedostossa config/routes.php',
	'no_controller'         => 'Kohana ei pystynyt päätellä kontrolleria käsittelemään pyyntöä: %s',
	'page_not_found'        => 'Pyytämääsi sivua, %s, ei löydy.',
	'stats_footer'          => 'Ladattu {execution_time} sekunnissa, käyttäen {memory_usage} muistia. Loi Kohana v{kohana_version}.',
	'error_file_line'       => '<tt>%s <strong>[%s]:</strong></tt>',
	'stack_trace'           => 'Stack Trace',
	'generic_error'         => 'Pyyntä ei voitu suorittaa',
	'errors_disabled'       => 'Voit mennä <a href="%s">etusivulle</a> tai <a href="%s">yrittää uudelleen</a>.',

	// Drivers
	'driver_implements'     => 'Ajurin %s kirjastolle %s täytyy soveltaa rajapintaa %s',
	'driver_not_found'      => 'Ajuria %s kirjastolle %s ei löydy',

	// Resource names
	'controller'            => 'kontrolleri',
	'helper'                => 'apuri',
	'library'               => 'kirjasto',
	'driver'                => 'ajuri',
	'model'                 => 'malli',
	'view'                  => 'näkymä',
);

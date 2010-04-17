<?php defined('SYSPATH') OR die('No direct access allowed.');

$lang = array
(
	'getimagesize_missing'    => 'Biblioteka edycji grafiki wymaga funkcji PHP getimagesize(), która nie jest dostępna dla obecnej instalacji.',
	'unsupported_method'      => 'Biblioteka edycji grafiki nie posiada opcji: %s.',
	'file_not_found'          => 'Podana grafika %s, nie została znaleziona. Sprawdź proszę czy plik grafiki istnieje używając funkcji file_exists przed próbą transformacji.',
	'type_not_allowed'        => 'Podana grafika, %s, jest niedozwolonego typu.',
	'invalid_width'           => 'Podana szerokość, %s, jest nieprawidłowa.',
	'invalid_height'          => 'Podana wysokość, %s, jest nieprawidłowa.',
	'invalid_dimensions'      => 'Podane rozmiary dla %s są nieprawidłowe.',
	'invalid_master'          => 'Nadrzędne podane wymiary, nie są prawidłowe.',
	'invalid_flip'            => 'Podany kierunek obrotu jest nieprawidłowy.',
	'directory_unwritable'    => 'W podanym folderze, %s, zapis jest niedozwolony.',

	// ImageMagick specific messages
	'imagemagick' => array
	(
		'not_found' => 'Podany katalog ImageMagick nie zawiera wymaganego programu, %s.',
	),

	// GraphicsMagick specific messages
	'graphicsmagick' => array
	(
		'not_found' => 'Podany katalog GraphicsMagick nie zawiera wymaganego programu, %s.',
	),

	// GD specific messages
	'gd' => array
	(
		'requires_v2' => 'Biblioteka manipulacji obrazem wymaga GD2. Zobacz http://php.net/gd_info aby dowiedzieć się więcej.',
	),
);

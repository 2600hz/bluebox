<?php defined('SYSPATH') OR die('No direct access allowed.');

$lang = array
(
	'getimagesize_missing'    => 'A biblioteca de imagem precisa da função getimagesize() do PHP, a qual não esta disponível na sua instalação.',
	'unsupported_method'      => 'O seu driver que esta configurado não suporta a transformação de imagem %s.',
	'file_not_found'          => 'A imagem especificada, %s, não foi encontrada. Por Favor verifique se a imagem existe usando file_exists() antes de manipula-la.',
	'type_not_allowed'        => 'A imagem especificada, %s, não é um tipo permitido de imagem.',
	'invalid_width'           => 'A largura que você especificou, %s, não é valida.',
	'invalid_height'          => 'A altura que você especificou, %s, não é valida.',
	'invalid_dimensions'      => 'As dimensões especificadas para %s não são validas.',
	'invalid_master'          => 'A dimenção principal especificada não é valida.',
	'invalid_flip'            => 'A direção de rotação especificada não é valida.',
    'directory_unwritable'    => 'O diretório especificado, %s, não pode ser escrito.',

	// ImageMagick specific messages
	'imagemagick' => array
	(
		'not_found' => 'O diretório ImageMagick especificado não contém um programa necessário, %s.',
	),

	// GD specific messages
	'gd' => array
	(
		'requires_v2' => 'A biblioteca de imagem requer GD2. Por favor veja http://php.net/gd_info para maiores informações.',
	),
);

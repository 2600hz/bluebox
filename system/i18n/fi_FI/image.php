<?php defined('SYSPATH') OR die('No direct access allowed.');

$lang = array
(
	'getimagesize_missing'    => 'Image-kirjasto tarvitsee getimagesize() PHP-funktion jota ei ole saatavilla.',
	'unsupported_method'      => 'Määrittelemäsi ajuri ei tue kuvamuunnosta %s.',
	'file_not_found'          => 'Määriteltyä kuvaa %s ei löydy. Varmista, että tiedostot ovat olemassa käyttämällä file_exists() ennen niiden käyttöä.',
	'type_not_allowed'        => 'Määritelty kuva %s ei ole sallittua tyyppiä.',
	'invalid_width'           => 'Antamasi leveys %s ei ole hyväksyttävä.',
	'invalid_height'          => 'Antamasi korkeus %s ei ole hyväksyttävä.',
	'invalid_dimensions'      => 'Kuvalle %s antamasi mitat eivät ole hyväksyttäviä.',
	'invalid_master'          => 'Määritellyt päämitat eivät ole hyväksyttäviä.',
	'invalid_flip'            => 'Määritelty kääntösuunta ei ole hyväksyttävä.',

	'directory_unwritable'    => 'Määriteltyyn hakemistoon %s ei voida kirjoittaa.',

	// ImageMagick specific messages
	'imagemagick' => array
	(
		'not_found' => 'Määritelty ImageMagick -hakemisto ei sisällä vaadittua ohjelmaa, %s.',
	),

	// GD specific messages
	'gd' => array
	(
		'requires_v2' => 'Image-kirjasto vaatii GD2-kirjaston. Lisätietoja: http://php.net/gd_info',
	),
);

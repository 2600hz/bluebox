<?php defined('SYSPATH') OR die('No direct access allowed.');

$lang = array
(
	'file_not_found' => 'Määriteltyä tiedostoa %s ei löydy. Varmista, että tiedostot ovat olemassa käyttämällä file_exists() ennen niiden käyttöä.',
	'requires_GD2'   => 'Captcha-kirjasto vaatii GD2-kirjaston FreeType-tuella. Lisätietoja: http://php.net/gd_info',

	// Words of varying length for the Captcha_Word_Driver to pick from
	// Note: use only alphanumeric characters
	'words' => array
	(
		'cd', 'tv', 'se', 'ja', 'ei', 'on',
		'vai', 'oli', 'ilo', 'voi', 'maa', 'kuu',
		'auto', 'yksi', 'vesi', 'talo', 'valo', 'peli',
		'ankka', 'kirja', 'hiiri', 'kissa', 'peili', 'polvi',
		'salkku', 'ikkuna', 'nikama', 'pensas', 'paperi', 'kaunis',
		'ranneke', 'sininen', 'alainen', 'karisma', 'iloinen', 'pehmeys',
		'aikuinen', 'punainen', 'kymmenen', 'rakennus', 'menestys', 'laatikko',
		'suurennus', 'valkoinen', 'rohkeasti', 'paljastaa', 'sylinteri', 'seuraamus',
	),

	// Riddles for the Captcha_Riddle_Driver to pick from
	// Note: use only alphanumeric characters
	'riddles' => array
	(
		array('Onko lumi mustaa? (kyllä tai ei)', 'ei'),
		array('Oletko sinä robotti? (kyllä tai en)', 'en'),
		array('Aurinko on... (kuuma tai kylmä)', 'kuuma'),
		array('Syksyn jälkeen tulee...', 'talvi'),
		array('Mikä on ensimmäinen viikonpäivä?', 'maanantai'),
		array('Mikä on vuoden viimeinen kuukausi?', 'joulukuu'),
	),
);

<?php defined('SYSPATH') OR die('No direct access allowed.');

$lang = array
(
	'userfile_not_set'   => 'Ei löydy lomakkeen kenttää %s.',
	'file_exceeds_limit' => 'Lähetetyn tiedoston koko ylittää PHP-asetuksissa määritellyn suurimman tiedostokoon.',
	'file_partial'       => 'Vain osa tiedostosta lähetettiin',
	'no_file_selected'   => 'Et valinnut tiedostoa lähetettäväksi',
	'invalid_filetype'   => 'Lähettämäsi tiedoston tyyppi ei ole sallittu.',
	'invalid_filesize'   => 'Lähettämäsi tiedosto ylittää suurimman sallitun koon (%s)',
	'invalid_dimensions' => 'Lähettämäsi kuva ylittää suurimman sallitun leveyden tai korkeuden (%s)',
	'destination_error'  => 'Virhe siirrettäessä lähetettyä tiedostoa lopulliseen kohteeseen.',
	'no_filepath'        => 'Lähetysten tallennushakemisto on viallinen.',
	'no_file_types'      => 'Et ole määritellyt yhtään sallittua tiedostotyyppiä.',
	'bad_filename'       => 'Palvelimella on jo lähettämäsi tiedoston niminen tiedosto.',
	'not_writable'       => 'Lähetyksen kohdehakemistoon %s ei voida kirjoittaa.',
	'error_on_file'      => 'Virhe lähettäessä %s:',
	// Error code responses
	'set_allowed'        => 'Turvallisuussyistä sinun tulee määritellä hyväksytyt tiedostotyypit.',
	'max_file_size'      => 'Turvallisuussyistä MAX_FILE_SIZE ei ole riittävä tiedostokoon rajoittamiseen.',
	'no_tmp_dir'         => 'Väliaikaishakemistoa johon voi kirjoittaa ei löydy.',
	'tmp_unwritable'     => 'Määriteltyyn lähetyshakemistoon %s ei voida kirjoittaa.'
);

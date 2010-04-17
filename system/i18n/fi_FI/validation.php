<?php defined('SYSPATH') OR die('No direct access allowed.');

$lang = array
(
	// Class errors
	'invalid_rule'  => 'Viallinen validointisääntö: %s',

	// General errors
	'unknown_error' => 'Tuntematon validointivirhe tarkastettaessa kenttää %s.',
	'required'      => '%s on pakollinen.',
	'min_length'    => '%s pitää olla vähintään %d merkkiä pitkä.',
	'max_length'    => '%s saa olla korkeintaan %d merkkiä pitkä.',
	'exact_length'  => '%s pitää olla tasan %d merkkiä pitkä.',
	'in_array'      => '%s pitää olla valittuna listasta.',
	'matches'       => '%s pitää olla sama kuin %s.',
	'valid_url'     => '%s pitää olla oikea URL.',
	'valid_email'   => '%s pitää olla oikea email-osoite.',
	'valid_ip'      => '%s pitää olla oikea IP-osoite.',
	'valid_type'    => '%s saa sisältää vain %s -merkkejä.',
	'range'         => '%s pitää olla annettujen arvojen välillä.',
	'regex'         => '%s ei täsmää hyväksyttyyn sisältöön.',
	'depends_on'    => '%s riippuu kentästä %s.',

	// Upload errors
	'user_aborted'  => 'Tiedoston %s lähetys keskeytettiin.',
	'invalid_type'  => 'Tiedosto %s ei ole sallittua tyyppiä.',
	'max_size'      => 'Lähettämäsi tiedosto %s oli liian suuri. Suurin sallittu koko on %s.',
	'max_width'     => 'Lähettämäsi tiedosto %s oli liian suuri. Suurin sallittu leveys on %spx.',
	'max_height'    => 'Lähettämäsi tiedosto %s oli liian suuri. Suurin sallittu korkeus on %spx.',
	'min_width'     => 'Lähettämäsi tiedosto %s oli liian pieni. Pienin sallittu leveys on %spx.',
	'min_height'    => 'Lähettämäsi tiedosto %s oli liian pieni. Pienin sallittu korkeus on %spx.',

	// Field types
	'alpha'         => 'aakkoset',
	'alpha_numeric' => 'aakkoset ja numerot',
	'alpha_dash'    => 'aakkoset, väli- ja alaviivat',
	'digit'         => 'numero',
	'numeric'       => 'numerot',
);

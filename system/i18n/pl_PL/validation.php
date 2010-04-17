<?php defined('SYSPATH') OR die('No direct access allowed.');

$lang = array
(
	// Class errors
	'invalid_rule'  => 'Użyto niepoprawnej reguły walidacji: %s',
	'i18n_array'    => 'Klucz %s z i18n musi być tablicą do użycia z zasadami in_lang.',
	'not_callable'  => 'Funkcja callback %s uzyta w walidacji nie może zostać wywołana.',

	// General errors
	'unknown_error' => 'Nieznany błąd walidacji podczas walidowania pola %s.',
	'required'      => 'Pole %s jest wymagane.',
	'min_length'    => 'Minimalna wymagana ilość znaków dla pola %s to %d.',
	'max_length'    => 'Maksymalana wymagana ilość znaków dla pola %s to %d.',
	'exact_length'  => 'Wymagana ilość znaków dla pola %s to dokładnie %d.',
	'in_array'      => 'Wartość pola %s musi zostać wybrana z listy.',
	'matches'       => 'Pole %s musi być identyczne z polem %s.',
	'valid_url'     => 'Pole %s musi zawierać poprawny adres URL, zaczynający się od %s://.',
	'valid_email'   => 'Pole %s musi zawierać poprawny adres email.',
	'valid_ip'      => 'Pole %s musi zawierać poprawny numer IP.',
	'valid_type'    => 'Pole %s może zawierać wyłącznie znaki typu %s.',
	'range'         => 'Pole %s musi się zawierać w określonym zakresie.',
	'regex'         => 'Pole %s nie odpowiada zdefiniowanej masce wprowadzania.',
	'depends_on'    => 'Pole %s jest zależne od pola %s.',

	// Upload errors
	'user_aborted'  => 'Przerwano podczas wysyłania pliku %s.',
	'invalid_type'  => 'Plik %s ma nieprawidłowy typ.',
	'max_size'      => 'Rozmiar pliku %s przekracza dozwoloną wartość. Maksymalna wielkość to %s.',
	'max_width'     => 'Szerokość pliku %s przekracza dozwoloną wartość. Maksymalna szerokość to %spx.',
	'max_height'    => 'Wysokość pliku %s przekracza dozwoloną wartość. Maksymalna wysokość to %spx.',
	'min_width'     => 'Plik %s który próbujesz wysłać, jest zbyt mały. Minimalna dozwolona szerokość to %spx.',
	'min_height'    => 'Plik %s który próbujesz wysłać, jest zbyt mały. Minimalna dozwolona wysokość to %spx.',

	// Field types
	'alpha'         => 'litera',
	'alpha_numeric' => 'litera i/lub cyfra',
	'alpha_dash'    => 'litera, podkreślenie i myślnik',
	'digit'         => 'cyfra',
	'numeric'       => 'liczba',
);
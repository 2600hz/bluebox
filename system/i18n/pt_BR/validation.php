<?php defined('SYSPATH') OR die('No direct access allowed.');

$lang = array
(
	// Class errors
	'invalid_rule'  => 'Regra de validação inválida usada: %s',

	// General errors
	'unknown_error' => 'Erro desconhecido de validação ao validar o campo %s.',
	'required'      => 'O campo %s é requerido.',
	'min_length'    => 'O campo %s deve ter ao menos %d caracteres de tamanho.',
	'max_length'    => 'O campo %s deve ter %d caracteres ou menos.',
	'exact_length'  => 'O campo %s deve ser de exatamente %d caracteres.',
	'in_array'      => 'O campo %s deve ser selecionado entre as opções listadas.',
	'matches'       => 'O campo %s deve ser igual ao campo %s.',
	'valid_url'     => 'O campo %s deve conter um URL válido.',
	'valid_email'   => 'O campo %s deve conter um endereço de email válido.',
	'valid_ip'      => 'O campo %s deve conter um endereço de IP válido.',
	'valid_type'    => 'O campo %s deve conter apenas caracteres %s.',
	'range'         => 'O campo %s deve estar entre os intervalos especificados.',
	'regex'         => 'O campo %s não combina com a entrada aceita.',
	'depends_on'    => 'O campo %s depende do campo %s.',

	// Upload errors
	'user_aborted'  => 'O arquivo %s foi abortado durante o envio.',
	'invalid_type'  => 'O arquivo %s não é de um tipo de arquivo permitido.',
	'max_size'      => 'O arquivo %s que você enviou é muito grande. O tamanho máximo permitido é %s.',
	'max_width'     => 'O arquivo %s que você enviou é muito grande. A largura máxima permitida é %spx.',
	'max_height'    => 'O arquivo %s que você enviou é muito grande. A altura máxima permitida é %spx.',
	'min_width'     => 'O arquivo %s que você enviou é muito pequeno. A largura mínima permitida é %spx.',
	'min_height'    => 'O arquivo %s que você enviou é muito pequeno. A altura mínima permitida é %spx.',

	// Field types
	'alpha'         => 'alfabético',
    'alpha_numeric' => 'alfabético e numérico',
	'alpha_dash'    => 'alfabético, hífen e sublinhado',
	'digit'         => 'digito',
	'numeric'       => 'numérico',
);

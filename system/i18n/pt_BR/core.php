<?php defined('SYSPATH') OR die('No direct access allowed.');

$lang = array
(
	'there_can_be_only_one' => 'Apenas uma instância do Kohana é permitida para cada página requisitada.',
	'uncaught_exception'    => 'Não foi possível capturar  %s: %s no arquivo %s, linha %s',
	'invalid_method'        => 'Método <tt>%s</tt> inválido, chamado por <tt>%s</tt>.',
	'invalid_property'      => 'A propriedade %s não existe na classe %s.',
	'log_dir_unwritable'    => 'A diretiva de configuracao do seu log.directory nao esta apontando para um diretorio com permissao de escrita disponivel.',
	'resource_not_found'    => 'Nao foi possivel executar a requisicao %s, <tt>%s</tt>,.',
	'invalid_filetype'      => 'O tipo de arquivo solicitado, <tt>.%s</tt>, não é permitido em seu arquivo de configuração do view.',
	'no_default_route'      => 'Por favor, selecione uma rota padrão em <tt>config/routes.php</tt>.',
	'no_controller'         => 'Não foi possível determinar um controlador para processar a requisicao: %s',
	'page_not_found'        => 'A página <tt>%s</tt> requisitada, não foi encontrada.',
	'stats_footer'          => 'Carregado em {execution_time} segundo(s), utilizando {memory_usage} de memória. Gerado por Kohana v{kohana_version}.',
	'error_message'         => 'Erro ocorrido na <strong>linha %s</strong> de <strong>%s</strong>.',
	'error_file_line'       => '<tt>%s <strong>[%s]:</strong></tt>', 
	'stack_trace'           => 'Stack Trace',
	'generic_error'         => 'Incapaz de completar a solicitação',
	'errors_disabled'       => 'Você pode ir para <a href="%s">home page</a> ou <a href="%s">tentar novamente</a>.',

	// Resource names
	'config'                => 'arquivo de configuração',
	'controller'            => 'controlador',
	'helper'                => 'ajudante',
	'library'               => 'biblioteca',
	'driver'                => 'driver',
	'model'                 => 'modelo',
	'view'                  => 'vista',
);
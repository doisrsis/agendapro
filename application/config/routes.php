<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/userguide3/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
// Rota padrão - Redireciona para login
$route['default_controller'] = 'auth/login';
$route['404_override'] = '';
$route['translate_uri_dashes'] = TRUE;

// Rotas de autenticação
$route['login'] = 'auth/login';
$route['logout'] = 'auth/logout';
$route['recuperar-senha'] = 'auth/recuperar_senha';
$route['resetar-senha/(:any)'] = 'auth/resetar_senha/$1';

// Rotas administrativas
$route['admin'] = 'admin/dashboard';
$route['admin/dashboard'] = 'admin/dashboard';

// Estabelecimentos
$route['admin/estabelecimentos'] = 'admin/estabelecimentos/index';
$route['admin/estabelecimentos/criar'] = 'admin/estabelecimentos/criar';
$route['admin/estabelecimentos/editar/(:num)'] = 'admin/estabelecimentos/editar/$1';
$route['admin/estabelecimentos/deletar/(:num)'] = 'admin/estabelecimentos/deletar/$1';

// Profissionais
$route['admin/profissionais'] = 'admin/profissionais/index';
$route['admin/profissionais/criar'] = 'admin/profissionais/criar';
$route['admin/profissionais/editar/(:num)'] = 'admin/profissionais/editar/$1';
$route['admin/profissionais/deletar/(:num)'] = 'admin/profissionais/deletar/$1';
$route['admin/profissionais/get_servicos/(:num)'] = 'admin/profissionais/get_servicos/$1';

// Serviços
$route['admin/servicos'] = 'admin/servicos/index';
$route['admin/servicos/criar'] = 'admin/servicos/criar';
$route['admin/servicos/editar/(:num)'] = 'admin/servicos/editar/$1';
$route['admin/servicos/deletar/(:num)'] = 'admin/servicos/deletar/$1';

// Clientes
$route['admin/clientes'] = 'admin/clientes/index';
$route['admin/clientes/criar'] = 'admin/clientes/criar';
$route['admin/clientes/editar/(:num)'] = 'admin/clientes/editar/$1';
$route['admin/clientes/visualizar/(:num)'] = 'admin/clientes/visualizar/$1';
$route['admin/clientes/deletar/(:num)'] = 'admin/clientes/deletar/$1';

// Agendamentos
$route['admin/agendamentos'] = 'admin/agendamentos/index';
$route['admin/agendamentos/criar'] = 'admin/agendamentos/criar';
$route['admin/agendamentos/editar/(:num)'] = 'admin/agendamentos/editar/$1';
$route['admin/agendamentos/cancelar/(:num)'] = 'admin/agendamentos/cancelar/$1';
$route['admin/agendamentos/finalizar/(:num)'] = 'admin/agendamentos/finalizar/$1';
$route['admin/agendamentos/get_horarios_disponiveis'] = 'admin/agendamentos/get_horarios_disponiveis';
$route['admin/agendamentos/get_clientes/(:num)'] = 'admin/agendamentos/get_clientes/$1';
$route['admin/agendamentos/get_profissionais/(:num)'] = 'admin/agendamentos/get_profissionais/$1';
$route['admin/agendamentos/get_servicos/(:num)'] = 'admin/agendamentos/get_servicos/$1';

// Usuários (já existente)
$route['admin/usuarios'] = 'admin/usuarios';
$route['admin/usuarios/(:any)'] = 'admin/usuarios/$1';

// Configurações (já existente)
$route['admin/configuracoes/testar_email'] = 'admin/configuracoes/testar_email';
$route['admin/configuracoes/smtp'] = 'admin/configuracoes/smtp';
$route['admin/configuracoes/geral'] = 'admin/configuracoes/geral';
$route['admin/configuracoes'] = 'admin/configuracoes/index';

// Perfil e Logs (já existente)
$route['admin/perfil'] = 'admin/perfil';
$route['admin/logs'] = 'admin/logs';

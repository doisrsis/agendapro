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

// =========================================================================
// ROTAS DE AUTENTICAÇÃO
// =========================================================================

$route['default_controller'] = 'login';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

// Login e Logout
$route['login'] = 'login/index';
$route['logout'] = 'login/logout';
$route['sair'] = 'login/logout';

// Recuperação de Senha
$route['recuperar-senha'] = 'login/recuperar_senha';
$route['resetar-senha/(:any)'] = 'login/resetar_senha/$1';

// =========================================================================
// ROTAS ADMIN (Super Admin)
// =========================================================================

$route['admin'] = 'admin/dashboard';
$route['admin/dashboard'] = 'admin/dashboard';
$route['admin/(:any)'] = 'admin/$1';

// =========================================================================
// ROTAS PAINEL (Estabelecimento)
// =========================================================================

$route['painel'] = 'painel/dashboard';
$route['painel/dashboard'] = 'painel/dashboard';

// Rotas de Checkout (ANTES da rota genérica)
$route['painel/checkout/gerar-pix'] = 'painel/checkout/gerar_pix';
$route['painel/checkout/status/(:any)'] = 'painel/checkout/status/$1';
$route['painel/checkout/sucesso'] = 'painel/checkout/sucesso';
$route['painel/checkout/falha'] = 'painel/checkout/falha';
$route['painel/checkout/processar'] = 'painel/checkout/processar';
$route['painel/checkout/(:any)'] = 'painel/checkout/index/$1';

$route['painel/(:any)'] = 'painel/$1';


// Páginas especiais do painel
$route['painel/suspenso'] = 'painel/suspenso';
$route['painel/cancelado'] = 'painel/cancelado';

// =========================================================================
// ROTAS AGENDA (Profissional)
// =========================================================================

$route['agenda'] = 'agenda/dashboard';
$route['agenda/dashboard'] = 'agenda/dashboard';
$route['agenda/(:any)'] = 'agenda/$1';

// =========================================================================
// ROTAS PÚBLICAS
// =========================================================================

// Webhook Mercado Pago
$route['webhook/mercadopago'] = 'webhook/mercadopago';

// API Pública (se houver)
$route['api/(:any)'] = 'api/$1';

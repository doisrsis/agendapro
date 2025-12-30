<?php
/**
 * Script de teste para verificar status WAHA
 * Rafael Dias - doisr.com.br (29/12/2025)
 */

define('BASEPATH', true);
require_once 'index.php';

$CI =& get_instance();
$CI->load->model('Estabelecimento_model');
$CI->load->model('Configuracao_model');
$CI->load->library('waha_lib');

$estabelecimento_id = 4;
$estabelecimento = $CI->Estabelecimento_model->get_by_id($estabelecimento_id);

echo "=== TESTE STATUS WAHA ===\n\n";

echo "Estabelecimento: {$estabelecimento->nome}\n";
echo "ID: {$estabelecimento->id}\n";
echo "waha_ativo: " . ($estabelecimento->waha_ativo ?? 'NULL') . "\n";
echo "waha_status (banco): " . ($estabelecimento->waha_status ?? 'NULL') . "\n";
echo "waha_numero_conectado: " . ($estabelecimento->waha_numero_conectado ?? 'NULL') . "\n";
echo "waha_session_name: " . ($estabelecimento->waha_session_name ?? 'NULL') . "\n\n";

// Buscar configurações WAHA do SaaS
$configs = $CI->Configuracao_model->get_by_grupo('waha');
$config_array = [];
foreach ($configs as $config) {
    $config_array[$config->chave] = $config->valor;
}

echo "=== CONFIGURAÇÕES WAHA SAAS ===\n";
echo "API URL: " . ($config_array['waha_api_url'] ?? 'NULL') . "\n";
echo "API Key: " . (isset($config_array['waha_api_key']) ? substr($config_array['waha_api_key'], 0, 10) . '...' : 'NULL') . "\n\n";

// Configurar WAHA
if (!empty($config_array['waha_api_url']) && !empty($config_array['waha_api_key'])) {
    $session_name = $estabelecimento->waha_session_name ?? 'est_' . $estabelecimento_id;

    $CI->waha_lib->set_credentials(
        $config_array['waha_api_url'],
        $config_array['waha_api_key'],
        $session_name
    );

    echo "=== CONSULTANDO API WAHA ===\n";
    echo "Session Name: {$session_name}\n\n";

    $resultado = $CI->waha_lib->get_sessao();

    echo "Success: " . ($resultado['success'] ? 'SIM' : 'NÃO') . "\n";
    echo "Response:\n";
    print_r($resultado['response']);

    if ($resultado['success'] && isset($resultado['response']['status'])) {
        $status_api = $resultado['response']['status'];
        $me = $resultado['response']['me'] ?? null;

        echo "\n=== ANÁLISE ===\n";
        echo "Status API: {$status_api}\n";

        $status_banco = 'desconectado';
        if (in_array($status_api, ['WORKING', 'CONNECTED'])) {
            $status_banco = 'conectado';
        } elseif ($status_api === 'SCAN_QR_CODE') {
            $status_banco = 'conectando';
        }

        echo "Status para banco: {$status_banco}\n";

        if ($me && isset($me['id'])) {
            echo "Número conectado: {$me['id']}\n";
        }

        echo "\n=== AÇÃO NECESSÁRIA ===\n";
        if ($estabelecimento->waha_status !== $status_banco) {
            echo "❌ Status no banco está DESATUALIZADO!\n";
            echo "   Banco: {$estabelecimento->waha_status}\n";
            echo "   Deveria ser: {$status_banco}\n";
            echo "\nExecute o SQL:\n";
            echo "UPDATE estabelecimentos SET waha_status = '{$status_banco}'";
            if ($me && isset($me['id'])) {
                echo ", waha_numero_conectado = '{$me['id']}'";
            }
            echo " WHERE id = {$estabelecimento_id};\n";
        } else {
            echo "✅ Status no banco está CORRETO!\n";
        }
    }
} else {
    echo "❌ Configurações WAHA não encontradas!\n";
}

echo "\n=== FIM DO TESTE ===\n";

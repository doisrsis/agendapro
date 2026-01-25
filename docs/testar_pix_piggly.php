<?php
/**
 * Script para testar biblioteca piggly/php-pix
 * Autor: Rafael Dias - doisr.com.br
 * Data: 24/01/2026
 */

// Definir constantes CodeIgniter
define('BASEPATH', true);
define('FCPATH', 'c:/xampp/htdocs/agendapro/');

// Simular função log_message para testes
if (!function_exists('log_message')) {
    function log_message($level, $message) {
        // Silencioso em testes
        return true;
    }
}

// Carregar biblioteca Pix_lib
require_once FCPATH . 'application/libraries/Pix_lib.php';

echo "=== TESTE DA BIBLIOTECA PIGGLY/PHP-PIX ===\n\n";

// Criar instância
$pix_lib = new Pix_lib();

// ============================================
echo "=== TESTE 1: PIX COM EMAIL ===\n\n";

$dados_email = [
    'chave_pix' => 'rafaeldiaswebdev@gmail.com',
    'nome_recebedor' => 'RAFAEL DE ANDRADE DIAS',
    'cidade' => 'LAJE',
    'valor' => 1.00,
    'txid' => 'AG0000000238',
    'descricao' => 'BARBA'
];

$brcode_email = $pix_lib->gerar_br_code($dados_email);

if ($brcode_email) {
    echo "✅ BR Code gerado com sucesso!\n\n";
    echo "Chave: {$dados_email['chave_pix']}\n";
    echo "Nome: {$dados_email['nome_recebedor']}\n";
    echo "Cidade: {$dados_email['cidade']}\n";
    echo "Valor: R$ " . number_format($dados_email['valor'], 2, ',', '.') . "\n\n";
    echo "CÓDIGO PIX (EMAIL):\n";
    echo $brcode_email . "\n\n";
    echo "Tamanho: " . strlen($brcode_email) . " caracteres\n\n";
} else {
    echo "❌ Erro ao gerar BR Code com email\n\n";
}

echo str_repeat("=", 80) . "\n\n";

// ============================================
echo "=== TESTE 2: PIX COM UUID (SEM TRAÇOS) ===\n\n";

$dados_uuid = [
    'chave_pix' => '420ab7c44d6346d4809ecd3eebc129ec',
    'nome_recebedor' => 'RAFAEL DE ANDRADE DIAS',
    'cidade' => 'LAJE',
    'valor' => 1.00,
    'txid' => 'AG0000000238',
    'descricao' => 'BARBA'
];

$brcode_uuid = $pix_lib->gerar_br_code($dados_uuid);

if ($brcode_uuid) {
    echo "✅ BR Code gerado com sucesso!\n\n";
    echo "Chave: {$dados_uuid['chave_pix']}\n";
    echo "Nome: {$dados_uuid['nome_recebedor']}\n";
    echo "Cidade: {$dados_uuid['cidade']}\n";
    echo "Valor: R$ " . number_format($dados_uuid['valor'], 2, ',', '.') . "\n\n";
    echo "CÓDIGO PIX (UUID):\n";
    echo $brcode_uuid . "\n\n";
    echo "Tamanho: " . strlen($brcode_uuid) . " caracteres\n\n";
} else {
    echo "❌ Erro ao gerar BR Code com UUID\n\n";
}

echo str_repeat("=", 80) . "\n\n";

// ============================================
echo "=== TESTE 3: PIX COM UUID (COM TRAÇOS) ===\n\n";

$dados_uuid_tracos = [
    'chave_pix' => '420ab7c4-4d63-46d4-809e-cd3eebc129ec',
    'nome_recebedor' => 'RAFAEL DE ANDRADE DIAS',
    'cidade' => 'LAJE',
    'valor' => 1.00,
    'txid' => 'AG0000000238',
    'descricao' => 'BARBA'
];

$brcode_uuid_tracos = $pix_lib->gerar_br_code($dados_uuid_tracos);

if ($brcode_uuid_tracos) {
    echo "✅ BR Code gerado com sucesso!\n\n";
    echo "Chave: {$dados_uuid_tracos['chave_pix']}\n";
    echo "Nome: {$dados_uuid_tracos['nome_recebedor']}\n";
    echo "Cidade: {$dados_uuid_tracos['cidade']}\n";
    echo "Valor: R$ " . number_format($dados_uuid_tracos['valor'], 2, ',', '.') . "\n\n";
    echo "CÓDIGO PIX (UUID COM TRAÇOS):\n";
    echo $brcode_uuid_tracos . "\n\n";
    echo "Tamanho: " . strlen($brcode_uuid_tracos) . " caracteres\n\n";
} else {
    echo "❌ Erro ao gerar BR Code com UUID com traços\n\n";
}

echo str_repeat("=", 80) . "\n\n";

echo "✅ TESTES CONCLUÍDOS!\n\n";
echo "COPIE UM DOS CÓDIGOS ACIMA E TESTE NO APP DO BANCO\n";
?>

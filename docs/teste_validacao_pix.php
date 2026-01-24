<?php
/**
 * Teste de Validação de Chave PIX
 *
 * @author Rafael Dias - doisr.com.br
 * @date 23/01/2026
 */

// Simular ambiente CodeIgniter
define('BASEPATH', true);

// Incluir biblioteca PIX
require_once __DIR__ . '/../application/libraries/Pix_lib.php';

// Criar instância
$pix_lib = new Pix_lib();

// Dados do teste
$chave = '420ab7c4-4d63-46d4-809e-cd3eebc129ec';
$tipo = 'aleatoria';

echo "=== TESTE DE VALIDAÇÃO PIX ===\n\n";
echo "Chave: {$chave}\n";
echo "Tipo: {$tipo}\n";
echo "Tamanho original: " . strlen($chave) . " caracteres\n\n";

// Remover hífens
$chave_limpa = str_replace('-', '', $chave);
echo "Chave sem hífens: {$chave_limpa}\n";
echo "Tamanho sem hífens: " . strlen($chave_limpa) . " caracteres\n\n";

// Verificar se é hexadecimal
$is_hex = ctype_xdigit($chave_limpa);
echo "É hexadecimal? " . ($is_hex ? 'SIM' : 'NÃO') . "\n\n";

// Testar validação
$resultado = $pix_lib->validar_chave_pix($chave, $tipo);

echo "=== RESULTADO ===\n";
echo "Validação: " . ($resultado ? '✅ PASSOU' : '❌ FALHOU') . "\n\n";

// Testes adicionais
echo "=== TESTES ADICIONAIS ===\n\n";

$testes = [
    ['chave' => '420ab7c4-4d63-46d4-809e-cd3eebc129ec', 'tipo' => 'aleatoria', 'desc' => 'UUID com hífens'],
    ['chave' => '420ab7c44d6346d4809ecd3eebc129ec', 'tipo' => 'aleatoria', 'desc' => 'UUID sem hífens'],
    ['chave' => '12345678901', 'tipo' => 'cpf', 'desc' => 'CPF'],
    ['chave' => 'teste@email.com', 'tipo' => 'email', 'desc' => 'Email'],
    ['chave' => '5575999999999', 'tipo' => 'telefone', 'desc' => 'Telefone'],
];

foreach ($testes as $teste) {
    $valido = $pix_lib->validar_chave_pix($teste['chave'], $teste['tipo']);
    $status = $valido ? '✅' : '❌';
    echo "{$status} {$teste['desc']}: {$teste['chave']} ({$teste['tipo']})\n";
}

echo "\n=== FIM DO TESTE ===\n";

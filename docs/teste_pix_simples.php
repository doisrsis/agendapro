<?php
/**
 * Teste Simples de Validação PIX
 */

echo "=== TESTE DE VALIDAÇÃO PIX ===\n\n";

$chave = '420ab7c4-4d63-46d4-809e-cd3eebc129ec';
$tipo = 'aleatoria';

echo "Chave: {$chave}\n";
echo "Tipo: {$tipo}\n";
echo "Tamanho original: " . strlen($chave) . " caracteres\n\n";

// Remover hífens
$chave_limpa = str_replace('-', '', $chave);
echo "Chave sem hífens: {$chave_limpa}\n";
echo "Tamanho sem hífens: " . strlen($chave_limpa) . " caracteres\n\n";

// Verificar se é hexadecimal
$is_hex = ctype_xdigit($chave_limpa);
echo "É hexadecimal (ctype_xdigit)? " . ($is_hex ? 'SIM ✅' : 'NÃO ❌') . "\n";

// Verificar caractere por caractere
echo "\nAnálise caractere por caractere:\n";
for ($i = 0; $i < strlen($chave_limpa); $i++) {
    $char = $chave_limpa[$i];
    $is_valid = ctype_xdigit($char);
    echo "  [{$i}] '{$char}' - " . ($is_valid ? '✅' : '❌') . "\n";
}

// Validação final
$valido = (strlen($chave_limpa) == 32 && ctype_xdigit($chave_limpa));
echo "\n=== RESULTADO FINAL ===\n";
echo "Validação: " . ($valido ? '✅ PASSOU' : '❌ FALHOU') . "\n";

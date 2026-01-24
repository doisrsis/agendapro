<?php
/**
 * Script para limpar cache do PHP OPcache
 *
 * Acesse via navegador: http://localhost/agendapro/docs/limpar_cache.php
 */

echo "<h1>Limpeza de Cache PHP</h1>";

// Limpar OPcache
if (function_exists('opcache_reset')) {
    if (opcache_reset()) {
        echo "<p style='color: green;'>✅ OPcache limpo com sucesso!</p>";
    } else {
        echo "<p style='color: red;'>❌ Erro ao limpar OPcache</p>";
    }
} else {
    echo "<p style='color: orange;'>⚠️ OPcache não está habilitado</p>";
}

// Limpar cache de realpath
if (function_exists('clearstatcache')) {
    clearstatcache(true);
    echo "<p style='color: green;'>✅ Cache de realpath limpo!</p>";
}

echo "<hr>";
echo "<h2>Informações do Sistema</h2>";
echo "<p><strong>PHP Version:</strong> " . phpversion() . "</p>";
echo "<p><strong>OPcache Enabled:</strong> " . (function_exists('opcache_get_status') && opcache_get_status() ? 'Sim' : 'Não') . "</p>";

echo "<hr>";
echo "<p><a href='javascript:history.back()'>← Voltar</a></p>";

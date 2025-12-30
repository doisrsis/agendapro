<?php
/**
 * Script para testar conectividade com servidor WAHA
 * Rafael Dias - doisr.com.br (29/12/2025)
 */

// Configuração do banco de dados
$db_host = 'localhost';
$db_user = 'dois8950_agendapro';
$db_pass = 'Rafael@2024';
$db_name = 'dois8950_agendapro';

echo "<h1>🔍 Teste de Conectividade WAHA</h1>";
echo "<p><strong>Data/Hora:</strong> " . date('d/m/Y H:i:s') . "</p>";
echo "<hr>";

// Conectar ao banco
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    die("<p style='color:red;'>❌ Erro ao conectar ao banco de dados: " . $conn->connect_error . "</p>");
}

// Buscar configurações WAHA
$sql = "SELECT chave, valor FROM configuracoes WHERE grupo = 'waha'";
$result = $conn->query($sql);

$config_array = [];
if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $config_array[$row['chave']] = $row['valor'];
    }
}

$api_url = $config_array['waha_api_url'] ?? null;
$api_key = $config_array['waha_api_key'] ?? null;

echo "<h2>📋 Configurações WAHA</h2>";
echo "<table border='1' cellpadding='10' style='border-collapse: collapse;'>";
echo "<tr><td><strong>API URL:</strong></td><td>" . ($api_url ?? '<span style="color:red;">NÃO CONFIGURADO</span>') . "</td></tr>";
echo "<tr><td><strong>API Key:</strong></td><td>" . (isset($api_key) ? substr($api_key, 0, 15) . '...' : '<span style="color:red;">NÃO CONFIGURADO</span>') . "</td></tr>";
echo "</table>";

if (!$api_url || !$api_key) {
    echo "<p style='color:red;'><strong>❌ ERRO: Configurações WAHA não encontradas!</strong></p>";
    exit;
}

echo "<hr>";
echo "<h2>🌐 Teste de Conectividade</h2>";

// Teste 1: Ping básico (DNS)
echo "<h3>Teste 1: Resolução DNS</h3>";
$host = parse_url($api_url, PHP_URL_HOST);
echo "<p>Host: <strong>{$host}</strong></p>";

$dns = gethostbyname($host);
if ($dns === $host) {
    echo "<p style='color:orange;'>⚠️ DNS não resolveu ou é IP direto</p>";
} else {
    echo "<p style='color:green;'>✅ DNS resolvido para: {$dns}</p>";
}

// Teste 2: Conectividade HTTP
echo "<h3>Teste 2: Conectividade HTTP</h3>";

$ch = curl_init($api_url . '/api/sessions');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'X-Api-Key: ' . $api_key,
    'Content-Type: application/json'
]);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curl_error = curl_error($ch);
curl_close($ch);

echo "<table border='1' cellpadding='10' style='border-collapse: collapse;'>";
echo "<tr><td><strong>HTTP Status:</strong></td><td>";

if ($http_code == 0) {
    echo "<span style='color:red;'>❌ SEM RESPOSTA</span></td></tr>";
    echo "<tr><td><strong>Erro:</strong></td><td style='color:red;'>{$curl_error}</td></tr>";
} elseif ($http_code == 502) {
    echo "<span style='color:red;'>❌ 502 BAD GATEWAY</span></td></tr>";
    echo "<tr><td><strong>Problema:</strong></td><td style='color:red;'>Servidor WAHA está OFFLINE ou INACESSÍVEL</td></tr>";
} elseif ($http_code == 200 || $http_code == 401) {
    echo "<span style='color:green;'>✅ {$http_code} - Servidor ONLINE</span></td></tr>";
} else {
    echo "<span style='color:orange;'>⚠️ {$http_code}</span></td></tr>";
}

echo "<tr><td><strong>Resposta:</strong></td><td><pre>" . htmlspecialchars(substr($response, 0, 500)) . "</pre></td></tr>";
echo "</table>";

echo "<hr>";
echo "<h2>📊 Diagnóstico</h2>";

if ($http_code == 0) {
    echo "<div style='background:#ffe6e6; padding:15px; border-left:4px solid red;'>";
    echo "<h3 style='color:red;'>❌ Servidor Totalmente Inacessível</h3>";
    echo "<p><strong>Possíveis causas:</strong></p>";
    echo "<ul>";
    echo "<li>Servidor WAHA está offline</li>";
    echo "<li>Firewall bloqueando conexão</li>";
    echo "<li>URL incorreta</li>";
    echo "<li>Problema de rede</li>";
    echo "</ul>";
    echo "<p><strong>Ação recomendada:</strong></p>";
    echo "<ol>";
    echo "<li>Verificar se o servidor onde o WAHA está instalado está online</li>";
    echo "<li>Verificar se o container Docker do WAHA está rodando</li>";
    echo "<li>Executar: <code>docker ps | grep waha</code></li>";
    echo "<li>Se necessário: <code>docker restart waha</code></li>";
    echo "</ol>";
    echo "</div>";
} elseif ($http_code == 502) {
    echo "<div style='background:#ffe6e6; padding:15px; border-left:4px solid red;'>";
    echo "<h3 style='color:red;'>❌ 502 Bad Gateway</h3>";
    echo "<p><strong>Problema:</strong> O proxy (Cloudflare) não consegue alcançar o servidor WAHA.</p>";
    echo "<p><strong>Possíveis causas:</strong></p>";
    echo "<ul>";
    echo "<li>Container Docker do WAHA parou de funcionar</li>";
    echo "<li>Serviço WAHA travou/crashou</li>";
    echo "<li>Porta do WAHA não está acessível</li>";
    echo "</ul>";
    echo "<p><strong>Ação recomendada:</strong></p>";
    echo "<ol>";
    echo "<li>Acessar o servidor via SSH</li>";
    echo "<li>Verificar status: <code>docker ps -a | grep waha</code></li>";
    echo "<li>Ver logs: <code>docker logs waha --tail 100</code></li>";
    echo "<li>Reiniciar: <code>docker restart waha</code></li>";
    echo "</ol>";
    echo "</div>";
} elseif ($http_code == 200 || $http_code == 401) {
    echo "<div style='background:#e6ffe6; padding:15px; border-left:4px solid green;'>";
    echo "<h3 style='color:green;'>✅ Servidor WAHA está ONLINE!</h3>";
    echo "<p>O servidor está funcionando corretamente.</p>";
    echo "<p><strong>Próximos passos:</strong></p>";
    echo "<ol>";
    echo "<li>Acesse: <a href='/painel/configuracoes?aba=whatsapp'>Painel → Configurações → WhatsApp</a></li>";
    echo "<li>Clique em 'Conectar WhatsApp'</li>";
    echo "<li>Escaneie o QR Code</li>";
    echo "</ol>";
    echo "</div>";
} else {
    echo "<div style='background:#fff3cd; padding:15px; border-left:4px solid orange;'>";
    echo "<h3 style='color:orange;'>⚠️ Status HTTP Inesperado: {$http_code}</h3>";
    echo "<p>Verifique os logs para mais detalhes.</p>";
    echo "</div>";
}

echo "<hr>";
echo "<h2>🔧 SQL de Correção Temporária</h2>";
echo "<p>Enquanto o servidor WAHA não volta, execute:</p>";
echo "<pre style='background:#f5f5f5; padding:10px; border:1px solid #ccc;'>";
echo "UPDATE estabelecimentos \n";
echo "SET \n";
echo "    waha_status = 'desconectado',\n";
echo "    waha_bot_ativo = 0,\n";
echo "    waha_numero_conectado = NULL\n";
echo "WHERE id = 4;";
echo "</pre>";

echo "<hr>";
echo "<p><em>Script executado em: " . date('d/m/Y H:i:s') . "</em></p>";

// Fechar conexão
$conn->close();

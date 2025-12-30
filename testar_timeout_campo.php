<?php
/**
 * Script para testar campo bot_timeout_minutos
 * Rafael Dias - doisr.com.br (30/12/2025)
 */

// Configuração do banco de dados
$db_host = 'localhost';
$db_user = 'dois8950_agendapro';
$db_pass = 'Rafael@2024';
$db_name = 'dois8950_agendapro';

echo "<h1>🔍 Teste do Campo bot_timeout_minutos</h1>";
echo "<hr>";

// Conectar ao banco
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    die("<p style='color:red;'>❌ Erro ao conectar: " . $conn->connect_error . "</p>");
}

// 1. Verificar se o campo existe
echo "<h2>1. Verificar se campo existe na tabela</h2>";
$sql = "SHOW COLUMNS FROM estabelecimentos LIKE 'bot_timeout_minutos'";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    echo "<p style='color:green;'>✅ Campo 'bot_timeout_minutos' existe!</p>";
    $campo = $result->fetch_assoc();
    echo "<pre>";
    print_r($campo);
    echo "</pre>";
} else {
    echo "<p style='color:red;'>❌ Campo 'bot_timeout_minutos' NÃO existe!</p>";
    echo "<p><strong>Execute o SQL:</strong></p>";
    echo "<pre style='background:#f5f5f5; padding:10px;'>";
    echo "ALTER TABLE estabelecimentos \n";
    echo "ADD COLUMN bot_timeout_minutos INT DEFAULT 30 \n";
    echo "COMMENT 'Tempo em minutos para expirar sessão do bot' \n";
    echo "AFTER waha_bot_ativo;";
    echo "</pre>";
    $conn->close();
    exit;
}

// 2. Verificar valor atual do estabelecimento 4
echo "<h2>2. Valor atual do estabelecimento 4</h2>";
$sql = "SELECT id, nome, bot_timeout_minutos FROM estabelecimentos WHERE id = 4";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo "<table border='1' cellpadding='10' style='border-collapse: collapse;'>";
    echo "<tr><th>ID</th><th>Nome</th><th>bot_timeout_minutos</th></tr>";
    echo "<tr>";
    echo "<td>{$row['id']}</td>";
    echo "<td>{$row['nome']}</td>";
    echo "<td>" . ($row['bot_timeout_minutos'] ?? '<span style="color:orange;">NULL</span>') . "</td>";
    echo "</tr>";
    echo "</table>";
} else {
    echo "<p style='color:red;'>❌ Estabelecimento não encontrado!</p>";
}

// 3. Testar UPDATE
echo "<h2>3. Testar UPDATE (definir para 45 minutos)</h2>";
$sql = "UPDATE estabelecimentos SET bot_timeout_minutos = 45 WHERE id = 4";
if ($conn->query($sql) === TRUE) {
    echo "<p style='color:green;'>✅ UPDATE executado com sucesso!</p>";
    echo "<p>Linhas afetadas: " . $conn->affected_rows . "</p>";
} else {
    echo "<p style='color:red;'>❌ Erro no UPDATE: " . $conn->error . "</p>";
}

// 4. Verificar se salvou
echo "<h2>4. Verificar se salvou</h2>";
$sql = "SELECT id, nome, bot_timeout_minutos FROM estabelecimentos WHERE id = 4";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo "<table border='1' cellpadding='10' style='border-collapse: collapse;'>";
    echo "<tr><th>ID</th><th>Nome</th><th>bot_timeout_minutos</th></tr>";
    echo "<tr>";
    echo "<td>{$row['id']}</td>";
    echo "<td>{$row['nome']}</td>";
    $timeout = $row['bot_timeout_minutos'];
    if ($timeout == 45) {
        echo "<td style='color:green;'><strong>{$timeout}</strong> ✅</td>";
    } else {
        echo "<td style='color:red;'>{$timeout} ❌</td>";
    }
    echo "</tr>";
    echo "</table>";

    if ($timeout == 45) {
        echo "<p style='color:green;'><strong>✅ Campo está funcionando corretamente no banco!</strong></p>";
        echo "<p>O problema pode estar no formulário ou no controller.</p>";
    }
}

// 5. Restaurar valor padrão
echo "<h2>5. Restaurar valor padrão (30 minutos)</h2>";
$sql = "UPDATE estabelecimentos SET bot_timeout_minutos = 30 WHERE id = 4";
if ($conn->query($sql) === TRUE) {
    echo "<p style='color:green;'>✅ Valor restaurado para 30 minutos</p>";
}

echo "<hr>";
echo "<h2>📋 Diagnóstico</h2>";
echo "<p>Se o teste acima funcionou, o problema está em:</p>";
echo "<ul>";
echo "<li><strong>Formulário:</strong> Verifique se o campo está sendo enviado no POST</li>";
echo "<li><strong>Controller:</strong> Verifique os logs em application/logs/</li>";
echo "<li><strong>Model:</strong> Verifique se o Estabelecimento_model->update() está funcionando</li>";
echo "</ul>";

$conn->close();

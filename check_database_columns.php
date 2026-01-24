<?php
// Script para verificar colunas das tabelas
$db = new mysqli('177.136.251.242', 'dois8950_proagenda', 'ylfBb.-gl)c*Wa-6@2025', 'dois8950_agendapro');

if ($db->connect_error) {
    die('Erro de conexão: ' . $db->connect_error);
}

echo "=== COLUNAS DA TABELA PROFISSIONAIS ===\n";
$result = $db->query('DESCRIBE profissionais');
while ($row = $result->fetch_assoc()) {
    echo $row['Field'] . " (" . $row['Type'] . ")\n";
}

echo "\n=== COLUNAS DA TABELA ESTABELECIMENTOS (notif_prof) ===\n";
$result = $db->query("SHOW COLUMNS FROM estabelecimentos LIKE 'notif_prof%'");
while ($row = $result->fetch_assoc()) {
    echo $row['Field'] . " (" . $row['Type'] . ")\n";
}

$db->close();

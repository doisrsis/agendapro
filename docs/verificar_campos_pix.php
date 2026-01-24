<?php
/**
 * Script para verificar campos PIX Manual
 */

$host = '177.136.251.242';
$port = 3306;
$database = 'dois8950_agendapro';
$username = 'dois8950_proagenda';
$password = 'ylfBb.-gl)c*Wa-6@2025';

$mysqli = new mysqli($host, $username, $password, $database, $port);

if ($mysqli->connect_error) {
    die("Erro de conexão: " . $mysqli->connect_error);
}

echo "Verificando campos PIX Manual na tabela estabelecimentos:\n";
echo str_repeat("=", 80) . "\n\n";

$result = $mysqli->query("DESCRIBE estabelecimentos");

$campos_pix = [];
while ($row = $result->fetch_assoc()) {
    if (in_array($row['Field'], ['pagamento_tipo', 'pix_chave', 'pix_tipo_chave', 'pix_nome_recebedor', 'pix_cidade'])) {
        $campos_pix[] = $row;
    }
}

if (count($campos_pix) > 0) {
    echo "✅ Campos PIX Manual encontrados:\n\n";
    printf("%-30s %-40s %-10s %-10s\n", "Campo", "Tipo", "Null", "Default");
    echo str_repeat("-", 90) . "\n";

    foreach ($campos_pix as $campo) {
        printf("%-30s %-40s %-10s %-10s\n",
            $campo['Field'],
            $campo['Type'],
            $campo['Null'],
            $campo['Default'] ?? 'NULL'
        );
    }
    echo str_repeat("-", 90) . "\n";
    echo "\n✅ FASE 1 CONCLUÍDA - Estrutura de banco já está pronta!\n";
} else {
    echo "❌ Nenhum campo PIX Manual encontrado. É necessário executar o SQL.\n";
}

$mysqli->close();
?>

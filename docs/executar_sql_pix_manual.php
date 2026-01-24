<?php
/**
 * Script para executar SQL de adição de campos PIX Manual
 * Autor: Rafael Dias - doisr.com.br
 * Data: 23/01/2026
 */

// Configurações do banco
$host = '177.136.251.242';
$port = 3306;
$database = 'dois8950_agendapro';
$username = 'dois8950_proagenda';
$password = 'ylfBb.-gl)c*Wa-6@2025';

// Conectar ao banco
$mysqli = new mysqli($host, $username, $password, $database, $port);

if ($mysqli->connect_error) {
    die("Erro de conexão: " . $mysqli->connect_error);
}

echo "Conectado ao banco de dados com sucesso!\n\n";

// SQL para adicionar campos
$sql = "ALTER TABLE estabelecimentos
ADD COLUMN pagamento_tipo ENUM('mercadopago', 'pix_manual') DEFAULT 'mercadopago'
    COMMENT 'Tipo de pagamento: mercadopago (integração) ou pix_manual (confirmação manual)'
    AFTER agendamento_requer_pagamento,

ADD COLUMN pix_chave VARCHAR(255) NULL
    COMMENT 'Chave PIX do estabelecimento'
    AFTER pagamento_tipo,

ADD COLUMN pix_tipo_chave ENUM('cpf', 'cnpj', 'email', 'telefone', 'aleatoria') NULL
    COMMENT 'Tipo da chave PIX cadastrada'
    AFTER pix_chave,

ADD COLUMN pix_nome_recebedor VARCHAR(255) NULL
    COMMENT 'Nome do recebedor que aparecerá no PIX'
    AFTER pix_tipo_chave,

ADD COLUMN pix_cidade VARCHAR(100) NULL
    COMMENT 'Cidade do recebedor'
    AFTER pix_nome_recebedor";

echo "Executando ALTER TABLE...\n";

if ($mysqli->query($sql)) {
    echo "✅ Campos adicionados com sucesso!\n\n";

    // Verificar estrutura
    echo "Verificando estrutura da tabela:\n";
    $result = $mysqli->query("DESCRIBE estabelecimentos");

    echo "\nCampos PIX Manual adicionados:\n";
    echo str_repeat("-", 80) . "\n";
    printf("%-30s %-30s %-10s\n", "Campo", "Tipo", "Null");
    echo str_repeat("-", 80) . "\n";

    while ($row = $result->fetch_assoc()) {
        if (in_array($row['Field'], ['pagamento_tipo', 'pix_chave', 'pix_tipo_chave', 'pix_nome_recebedor', 'pix_cidade'])) {
            printf("%-30s %-30s %-10s\n", $row['Field'], $row['Type'], $row['Null']);
        }
    }
    echo str_repeat("-", 80) . "\n";

} else {
    echo "❌ Erro ao adicionar campos: " . $mysqli->error . "\n";
}

$mysqli->close();
echo "\nConexão fechada.\n";
?>

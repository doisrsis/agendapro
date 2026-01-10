<?php
/**
 * Script de teste para verificar lógica de confirmações
 * Autor: Rafael Dias - doisr.com.br
 * Data: 09/01/2026 22:23
 */

// Carregar bootstrap do CodeIgniter para ter acesso ao banco
$_SERVER['CI_ENV'] = 'production';
require_once('index.php');

// Obter instância do CI
$CI =& get_instance();
$CI->load->database();

// Usar conexão do CodeIgniter
$db = $CI->db;

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "<h1>Teste de Confirmações - Dia Anterior</h1>";
    echo "<p><strong>Data/Hora atual:</strong> " . date('Y-m-d H:i:s') . "</p>";
    echo "<p><strong>Data de amanhã:</strong> " . date('Y-m-d', strtotime('+1 day')) . "</p>";
    echo "<hr>";

    // 1. Verificar configuração do estabelecimento
    echo "<h2>1. Configuração do Estabelecimento (ID 4)</h2>";
    $stmt = $pdo->query("
        SELECT
            id,
            nome,
            solicitar_confirmacao,
            confirmacao_horas_antes,
            confirmacao_dia_anterior,
            confirmacao_horario_dia_anterior,
            agendamento_requer_pagamento
        FROM estabelecimentos
        WHERE id = 4
    ");
    $estabelecimento = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "<pre>";
    print_r($estabelecimento);
    echo "</pre>";

    // 2. Verificar agendamentos para amanhã
    echo "<h2>2. Agendamentos para Amanhã (10/01/2026)</h2>";
    $stmt = $pdo->query("
        SELECT
            id,
            data,
            hora_inicio,
            status,
            confirmacao_enviada,
            confirmacao_enviada_em,
            estabelecimento_id,
            cliente_id
        FROM agendamentos
        WHERE data = DATE_ADD(CURDATE(), INTERVAL 1 DAY)
        ORDER BY hora_inicio
    ");
    $agendamentos_amanha = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "<p><strong>Total:</strong> " . count($agendamentos_amanha) . "</p>";
    echo "<pre>";
    print_r($agendamentos_amanha);
    echo "</pre>";

    // 3. Testar query completa do cron
    echo "<h2>3. Query Completa do Cron (com debug)</h2>";
    $sql = "
        SELECT
            a.id,
            a.data,
            a.hora_inicio,
            a.status,
            a.confirmacao_enviada,
            e.nome as estabelecimento_nome,
            e.solicitar_confirmacao,
            e.confirmacao_horas_antes,
            e.confirmacao_dia_anterior,
            e.confirmacao_horario_dia_anterior,
            e.agendamento_requer_pagamento,
            c.nome as cliente_nome,
            c.whatsapp as cliente_whatsapp,
            -- Debug
            TIMESTAMPDIFF(HOUR, NOW(), CONCAT(a.data, ' ', a.hora_inicio)) as horas_ate_agendamento,
            DATE_ADD(CURDATE(), INTERVAL 1 DAY) as data_amanha,
            TIME(NOW()) as hora_atual,
            (a.data = DATE_ADD(CURDATE(), INTERVAL 1 DAY)) as eh_amanha,
            (TIME(NOW()) >= e.confirmacao_horario_dia_anterior) as passou_horario,
            -- Verificar cada condição
            (a.status = 'pendente') as status_ok,
            (a.confirmacao_enviada = 0) as nao_enviado,
            (a.data >= CURDATE()) as data_futura,
            (e.agendamento_requer_pagamento = 'nao') as sem_pagamento,
            (e.solicitar_confirmacao = 1) as solicita_confirmacao,
            (e.confirmacao_dia_anterior = 1) as dia_anterior_ativo
        FROM agendamentos a
        JOIN estabelecimentos e ON a.estabelecimento_id = e.id
        JOIN clientes c ON a.cliente_id = c.id
        WHERE a.estabelecimento_id = 4
          AND a.data = DATE_ADD(CURDATE(), INTERVAL 1 DAY)
        ORDER BY a.data, a.hora_inicio
    ";

    $stmt = $pdo->query($sql);
    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "<p><strong>Total de agendamentos encontrados:</strong> " . count($resultados) . "</p>";

    foreach ($resultados as $r) {
        echo "<div style='border: 1px solid #ccc; padding: 10px; margin: 10px 0;'>";
        echo "<h3>Agendamento #{$r['id']}</h3>";
        echo "<p><strong>Data/Hora:</strong> {$r['data']} {$r['hora_inicio']}</p>";
        echo "<p><strong>Cliente:</strong> {$r['cliente_nome']} ({$r['cliente_whatsapp']})</p>";
        echo "<p><strong>Status:</strong> {$r['status']}</p>";
        echo "<p><strong>Confirmação enviada:</strong> {$r['confirmacao_enviada']}</p>";

        echo "<h4>Verificação de Condições:</h4>";
        echo "<ul>";
        echo "<li>Status = pendente: " . ($r['status_ok'] ? '✅ SIM' : '❌ NÃO') . "</li>";
        echo "<li>Confirmação não enviada: " . ($r['nao_enviado'] ? '✅ SIM' : '❌ NÃO') . "</li>";
        echo "<li>Data futura: " . ($r['data_futura'] ? '✅ SIM' : '❌ NÃO') . "</li>";
        echo "<li>Sem pagamento obrigatório: " . ($r['sem_pagamento'] ? '✅ SIM' : '❌ NÃO') . "</li>";
        echo "<li>Solicita confirmação: " . ($r['solicita_confirmacao'] ? '✅ SIM' : '❌ NÃO') . "</li>";
        echo "<li>Dia anterior ativo: " . ($r['dia_anterior_ativo'] ? '✅ SIM' : '❌ NÃO') . "</li>";
        echo "<li>É amanhã: " . ($r['eh_amanha'] ? '✅ SIM' : '❌ NÃO') . "</li>";
        echo "<li>Passou horário ({$r['confirmacao_horario_dia_anterior']}): " . ($r['passou_horario'] ? '✅ SIM' : '❌ NÃO') . " (Hora atual: {$r['hora_atual']})</li>";
        echo "<li>Horas até agendamento: {$r['horas_ate_agendamento']}h (Config: {$r['confirmacao_horas_antes']}h)</li>";
        echo "</ul>";

        // Verificar se passaria no filtro
        $passaria_filtro =
            $r['status_ok'] &&
            $r['nao_enviado'] &&
            $r['data_futura'] &&
            $r['sem_pagamento'] &&
            $r['solicita_confirmacao'] &&
            (
                ($r['horas_ate_agendamento'] <= $r['confirmacao_horas_antes']) ||
                ($r['dia_anterior_ativo'] && $r['eh_amanha'] && $r['passou_horario'])
            );

        echo "<p><strong>PASSARIA NO FILTRO DO CRON?</strong> " . ($passaria_filtro ? '✅ SIM' : '❌ NÃO') . "</p>";
        echo "</div>";
    }

    // 4. Query exata do cron
    echo "<h2>4. Query Exata do Cron (sem debug)</h2>";
    $sql_cron = "
        SELECT
            a.*,
            e.nome as estabelecimento_nome,
            c.nome as cliente_nome,
            c.whatsapp as cliente_whatsapp
        FROM agendamentos a
        JOIN estabelecimentos e ON a.estabelecimento_id = e.id
        JOIN clientes c ON a.cliente_id = c.id
        JOIN servicos s ON a.servico_id = s.id
        JOIN profissionais p ON a.profissional_id = p.id
        WHERE a.status = 'pendente'
          AND a.confirmacao_enviada = 0
          AND a.data >= CURDATE()
          AND e.agendamento_requer_pagamento = 'nao'
          AND e.solicitar_confirmacao = 1
          AND (
              a.confirmacao_enviada = 0
              OR (a.confirmacao_enviada_em IS NOT NULL AND TIMESTAMPDIFF(HOUR, a.confirmacao_enviada_em, NOW()) >= 23)
          )
          AND (
              TIMESTAMPDIFF(HOUR, NOW(), CONCAT(a.data, ' ', a.hora_inicio)) <= e.confirmacao_horas_antes
              OR
              (e.confirmacao_dia_anterior = 1
               AND a.data = DATE_ADD(CURDATE(), INTERVAL 1 DAY)
               AND TIME(NOW()) >= e.confirmacao_horario_dia_anterior)
          )
        ORDER BY a.data, a.hora_inicio
    ";

    $stmt = $pdo->query($sql_cron);
    $resultados_cron = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "<p><strong>Total retornado pela query do cron:</strong> " . count($resultados_cron) . "</p>";
    if (count($resultados_cron) > 0) {
        echo "<pre>";
        print_r($resultados_cron);
        echo "</pre>";
    } else {
        echo "<p style='color: red;'><strong>NENHUM AGENDAMENTO ENCONTRADO!</strong></p>";
        echo "<p>Isso significa que alguma condição do WHERE não está sendo atendida.</p>";
    }

} catch (PDOException $e) {
    echo "<p style='color: red;'><strong>Erro:</strong> " . $e->getMessage() . "</p>";
}
?>

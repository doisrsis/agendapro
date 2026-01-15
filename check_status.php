<?php
// Verificar status do agendamento #124
define('BASEPATH', 'system/');
$_SERVER['REQUEST_METHOD'] = 'GET';

require_once('index.php');

$CI =& get_instance();
$CI->load->database();

$query = $CI->db->query("SELECT id, status, data, hora_inicio, cliente_id, created_at FROM agendamentos WHERE id = 124");
$row = $query->row_array();

if ($row) {
    echo "Agendamento #124:\n";
    echo "Status: " . $row['status'] . "\n";
    echo "Data: " . $row['data'] . "\n";
    echo "Hora: " . $row['hora_inicio'] . "\n";
    echo "Cliente ID: " . $row['cliente_id'] . "\n";
    echo "Criado em: " . $row['created_at'] . "\n";
} else {
    echo "Agendamento não encontrado\n";
}

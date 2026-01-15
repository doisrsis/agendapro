<?php
define('BASEPATH', 'system/');
$_SERVER['REQUEST_METHOD'] = 'GET';
require_once('index.php');

$CI =& get_instance();
$CI->load->database();

$query = $CI->db->query("SELECT id, status, data, hora_inicio, profissional_id, criado_em FROM agendamentos WHERE estabelecimento_id = 4 ORDER BY id DESC LIMIT 5");

foreach($query->result_array() as $row) {
    echo "ID: {$row['id']} | Status: {$row['status']} | Prof: {$row['profissional_id']} | Data: {$row['data']} {$row['hora_inicio']} | Criado: {$row['criado_em']}\n";
}

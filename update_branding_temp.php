<?php
// Define ambiente como development para ver erros
define('ENVIRONMENT', 'development');

// Carrega o framework básico
$system_path = 'system';
$application_folder = 'application';

if (realpath($system_path) !== FALSE) { $system_path = realpath($system_path).'/'; }
if (realpath($application_folder) !== FALSE) { $application_folder = realpath($application_folder).'/'; }

define('SELF', pathinfo(__FILE__, PATHINFO_BASENAME));
define('BASEPATH', $system_path);
define('FCPATH', dirname(__FILE__).'/');
define('SYSDIR', basename(BASEPATH));
define('APPPATH', $application_folder);
define('VIEWPATH', $application_folder.'views/');

require_once BASEPATH.'core/CodeIgniter.php';

// Instância do CI (Embora o require acima já inicie o bootstrap, vamos tentar acessar via getInstance se possível ou usar SQL direto se falhar - mas o bootstrap do CI roda o app.
// Melhor abordagem para script CLI simples em CI3 é criar um controller ou usar conexão direta se não quiser carregar tudo.
// Mas para ser rápido e usar a config do database.php, vamos fazer uma conexão direta lendo o arquivo.

include('application/config/database.php');

$db_config = $db['default'];

$conn = new mysqli($db_config['hostname'], $db_config['username'], $db_config['password'], $db_config['database']);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 1. Atualizar Nome
$sql_nome = "UPDATE configuracoes SET valor = 'ZappAgenda' WHERE chave = 'sistema_nome'";
if ($conn->query($sql_nome) === TRUE) {
    echo "Nome atualizado com sucesso.\n";
} else {
    // Se não existe, insere
    $conn->query("INSERT INTO configuracoes (chave, valor) VALUES ('sistema_nome', 'ZappAgenda')");
}

// 2. Atualizar Logo
$sql_logo = "UPDATE configuracoes SET valor = 'logo_full.png' WHERE chave = 'sistema_logo'";
if ($conn->query($sql_logo) === TRUE) {
    echo "Logo atualizada com sucesso.\n";
} else {
     $conn->query("INSERT INTO configuracoes (chave, valor) VALUES ('sistema_logo', 'logo_full.png')");
}

// 3. Atualizar Icone (se houver config)
$sql_icon = "UPDATE configuracoes SET valor = 'logo_icon.png' WHERE chave = 'sistema_icon'";
$conn->query($sql_icon); // Tenta atualizar, sem problema se falhar

$conn->close();
echo "Rebranding aplicado no banco de dados!\n";
?>

<?php
// Conexão com o banco. Os valores podem ser sobrescritos por variáveis de ambiente;
// o padrão abaixo é o do WAMP local (localhost / root / sem senha / base "users").
$host = getenv('DB_HOST') ?: 'localhost';
$user = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASS') ?: '';
$database = getenv('DB_NAME') ?: 'users';

if (empty($_POST['email']) OR empty($_POST['senha'])) {
    header("Location: login.html");
    exit;
}

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $mysqli = new mysqli($host, $user, $password, $database);
} catch (mysqli_sql_exception $e) {
    http_response_code(503);
    exit('Banco de dados indisponível. Verifique a conexão e a base "users".');
}

$sql = "SELECT `email`, `senha` FROM `dados` WHERE `email` = ? AND `senha` = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param('ss', $_POST['email'], $_POST['senha']);
$stmt->execute();
$query = $stmt->get_result();

if (mysqli_num_rows($query) != 1) {
    echo "Login inválido!";
    exit;
}

$resultado = mysqli_fetch_assoc($query);

if (!isset($_SESSION)) session_start();

// Salva os dados encontrados na sessão
$_SESSION['UsuarioNome'] = $resultado['email'];
$_SESSION['UsuarioNivel'] = $resultado['senha'];

// Redireciona o visitante
header("Location: index.html"); exit;

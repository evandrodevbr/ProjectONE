<?php
// Conexão com o banco. Os valores podem ser sobrescritos por variáveis de ambiente;
// o padrão abaixo é o do WAMP local (localhost / root / sem senha / base "users").
$host = getenv('DB_HOST') ?: 'localhost';
$user = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASS') ?: '';
$database = getenv('DB_NAME') ?: 'users';

$email = $_POST['email_name'] ?? '';
$senha = $_POST['senha_name'] ?? '';
$nome = $_POST['nome_name'] ?? '';
$sobrenome = $_POST['sobrenome_name'] ?? '';
$rg = $_POST['rg_name'] ?? '';
$cpf = $_POST['cpf_name'] ?? '';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $mysqli = new mysqli($host, $user, $password, $database);
} catch (mysqli_sql_exception $e) {
    http_response_code(503);
    exit('Banco de dados indisponível. Verifique a conexão e a base "users".');
}

$sql = "INSERT INTO dados (email, senha, nome, sobrenome, rg, cpf) VALUES (?, ?, ?, ?, ?, ?)";

try {
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('ssssss', $email, $senha, $nome, $sobrenome, $rg, $cpf);
    $stmt->execute();
} catch (mysqli_sql_exception $e) {
    http_response_code(500);
    exit('Não foi possível concluir o cadastro.');
}

$stmt->close();
mysqli_close($mysqli);

header('Location: login.html');
exit;

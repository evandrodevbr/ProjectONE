<?php
// Conexão com o banco. Os valores podem ser sobrescritos por variáveis de ambiente;
// o padrão abaixo é o do WAMP local (localhost / root / sem senha / base "users").
$host = getenv('DB_HOST') ?: 'localhost';
$user = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASS') ?: '';
$database = getenv('DB_NAME') ?: 'users';

$pesquisar = $_POST['search-field'] ?? '';
$termo = '%' . $pesquisar . '%';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = mysqli_connect($host, $user, $password, $database);
} catch (mysqli_sql_exception $e) {
    http_response_code(503);
    exit('Banco de dados indisponível. Verifique a conexão e a base "users".');
}

$sql_select = "SELECT * FROM dados WHERE email LIKE ? OR nome LIKE ? OR sobrenome LIKE ? OR rg LIKE ? OR cpf LIKE ? LIMIT 5";
$stmt = $conn->prepare($sql_select);
$stmt->bind_param('sssss', $termo, $termo, $termo, $termo, $termo);
$stmt->execute();
$resultado = $stmt->get_result();

while ($rows = mysqli_fetch_array($resultado)) {
    echo $rows['email'].'<br>';
}

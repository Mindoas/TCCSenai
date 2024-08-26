<?php
header('Content-Type: application/json');

// Conectar ao banco de dados (ajuste com as credenciais corretas)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "meu_banco_frequencia";

// Cria uma nova conexão com o banco de dados usando MySQLi
$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica se houve um erro na conexão
if ($conn->connect_error) {
    echo json_encode(['erro' => 'Falha na conexão com o banco de dados: ' . $conn->connect_error]);
    exit();
}

// Prepara a consulta SQL para obter os dados da tabela painel_adm
$sql = "SELECT nome, serie, MAX(datas) AS horario
FROM paineladm
JOIN cartoes ON cartoes_id = id
WHERE DATE(datas) = CURDATE()
GROUP BY nome, serie;
";

$result = $conn->query($sql);

// Verifica se houve um erro na execução da consulta
if ($result === false) {
    echo json_encode(['erro' => 'Erro na execução da consulta: ' . $conn->error]);
    exit();
}

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

// Envia os dados como JSON
echo json_encode($data);

// Fecha a conexão com o banco de dados
$conn->close();
?>

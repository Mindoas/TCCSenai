<?php
header('Content-Type: application/json'); // Define o tipo de resposta como JSON

// Configurações de conexão com o banco de dados
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "meu_banco_frequencia";

// Tenta criar uma nova conexão com o banco de dados
$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica se houve um erro na conexão
if ($conn->connect_error) {
    echo json_encode(['erro' => 'Falha na conexão com o banco de dados: ' . $conn->connect_error]);
    exit();
}

// Consulta SQL para obter dados dos cartões para o dia atual
$sql = "SELECT nome, serie, datas AS horario, status
FROM paineladm
JOIN cartoes ON cartoes_id = id
WHERE DATE(datas) = CURDATE()";

$result = $conn->query($sql);

// Verifica se houve um erro na execução da consulta
if ($result === false) {
    echo json_encode(['erro' => 'Erro na execução da consulta: ' . $conn->error]);
    exit();
}

// Coleta os resultados da consulta em um array
$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

// Envia os dados como JSON
echo json_encode($data);

// Fecha a conexão com o banco de dados
$conn->close();
?>

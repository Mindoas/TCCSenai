<?php
header('Content-Type: application/json');

// Conectar ao banco de dados
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sua_base_de_dados";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Buscar todos os cartões
$sql = "SELECT id, nome, info FROM sua_tabela";
$result = $conn->query($sql);

$cards = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $cards[] = $row;
    }
}

echo json_encode($cards);

$conn->close();
?>
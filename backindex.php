<?php
header('Content-Type: application/json');

// Conectar ao banco de dados (ajuste com as credenciais corretas)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "meu_banco_frequencia";

// Conexão com o banco de dados
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(['erro' => 'Falha na conexão com o banco de dados']));
}

// Obter o código do aluno do POST
$data = json_decode(file_get_contents('php://input'), true);
$codigo = $data['codigo'];

// Verificar se o código do aluno existe
$sql = "SELECT nome FROM alunos WHERE codigo = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $codigo);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // O código foi encontrado
    $row = $result->fetch_assoc();
    echo json_encode(['existe' => true, 'nome' => $row['nome']]);
} else {
    // O código não foi encontrado
    echo json_encode(['existe' => false]);
}

$stmt->close();
$conn->close();
?>

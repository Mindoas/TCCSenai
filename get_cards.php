<?php
header('Content-Type: application/json'); // Define o tipo de conteúdo como JSON

// Configurações do banco de dados
$servidor = "localhost";
$usuario = "root";
$senha = "";
$banco = "meu_banco_frequencia";

// Cria a conexão
$conn = new mysqli($servidor, $usuario, $senha, $banco);

// Verifica a conexão
if ($conn->connect_error) {
    echo json_encode(["error" => "Conexão falhou: " . $conn->connect_error]);
    exit();
}

// Obtém o parâmetro de pesquisa
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Prepara a consulta SQL com filtro de pesquisa
$sql = "SELECT id, nome, info FROM cartoes";
$params = [];
$types = '';

// Adiciona cláusula WHERE se houver um termo de pesquisa
if ($search !== '') {
    $sql .= " WHERE nome LIKE ? OR info LIKE ?";
    $searchTerm = "%$search%";
    $params = [$searchTerm, $searchTerm];
    $types = 'ss'; // 'ss' para string e string
}

// Prepara a consulta
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    echo json_encode(["error" => "Erro ao preparar a consulta: " . $conn->error]);
    exit();
}

// Se houver parâmetros, vincula-os
if ($params) {
    $stmt->bind_param($types, ...$params);
}

// Executa a consulta
if (!$stmt->execute()) {
    echo json_encode(["error" => "Erro ao executar a consulta: " . $stmt->error]);
    exit();
}

// Obtém o resultado
$result = $stmt->get_result();

// Inicializa um array para armazenar os dados
$cards = [];

// Verifica se há resultados e os adiciona ao array
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $cards[] = $row;
    }
}

// Retorna os dados em formato JSON
echo json_encode($cards);

// Fecha a conexão
$stmt->close();
$conn->close();
?>

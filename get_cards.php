<?php
// Configurações do banco de dados
$servidor = "localhost";
$usuario = "root";
$senha = "";
$banco = "meu_banco_frequencia";
$porta = "3306";

// Cria a conexão
$conn = new mysqli($servidor, $usuario, $senha, $banco);

// Verifica a conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Obtém o parâmetro de pesquisa
$search = isset($_GET['search']) ? $_GET['search'] : '';

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

// Se houver parâmetros, vincula-os
if ($params) {
    $stmt->bind_param($types, ...$params);
}

// Executa a consulta
$stmt->execute();

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
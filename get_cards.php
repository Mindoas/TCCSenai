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

// Consulta SQL para obter os dados
$sql = "SELECT id,nome, info FROM cartoes";
$result = $conn->query($sql);

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
$conn->close();
?>
